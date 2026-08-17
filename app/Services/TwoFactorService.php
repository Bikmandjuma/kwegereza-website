<?php

namespace App\Services;

/**
 * A from-scratch, dependency-free implementation of TOTP (RFC 6238, the
 * algorithm behind Google Authenticator / Authy / most "authenticator app"
 * 2FA). No composer package required — this is standard PHP hash functions
 * plus base32 encode/decode (which PHP doesn't ship, so it's implemented
 * here).
 *
 * Deliberately NOT generating QR code images — a hand-rolled QR encoder
 * (Reed-Solomon error correction, module placement, etc.) is genuinely easy
 * to get subtly wrong in a way that produces an unscannable code, which is
 * worse than not having one. Instead this shows the secret as manual-entry
 * text, which every authenticator app supports as a fallback to scanning.
 */
class TwoFactorService
{
    private const BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    private const PERIOD = 30;
    private const DIGITS = 6;

    public function generateSecretKey(int $length = 20): string
    {
        return $this->base32Encode(random_bytes($length));
    }

    public function getOtpAuthUri(string $secret, string $accountLabel, string $issuer = 'Kwegereza Islam Umuryango'): string
    {
        return sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=SHA1&digits=%d&period=%d',
            rawurlencode($issuer),
            rawurlencode($accountLabel),
            $secret,
            rawurlencode($issuer),
            self::DIGITS,
            self::PERIOD
        );
    }

    public function currentCode(string $secret): string
    {
        return $this->generateCode($secret, (int) floor(time() / self::PERIOD));
    }

    /**
     * Verifies a 6-digit code, tolerating clock drift of up to one 30s
     * step in either direction (the standard practical window — most
     * authenticator apps and phones do drift by a few seconds).
     */
    public function verifyCode(string $secret, string $code, int $window = 1): bool
    {
        $code = preg_replace('/\s+/', '', $code);

        if (!preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        $currentStep = (int) floor(time() / self::PERIOD);

        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals($this->generateCode($secret, $currentStep + $i), $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, string> plain-text codes to show the user ONCE
     */
    public function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];

        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(4))) . '-' . strtoupper(bin2hex(random_bytes(4)));
        }

        return $codes;
    }

    /**
     * @param array<int, string> $codes plain-text codes
     * @return array<int, string> bcrypt-hashed, safe to store in the DB
     */
    public function hashRecoveryCodes(array $codes): array
    {
        return array_map(fn($code) => password_hash($code, PASSWORD_BCRYPT), $codes);
    }

    public function verifyRecoveryCode(array $hashedCodes, string $submitted): ?int
    {
        foreach ($hashedCodes as $index => $hash) {
            if (password_verify(strtoupper(trim($submitted)), $hash)) {
                return $index; // caller should remove this index so it can't be reused
            }
        }

        return null;
    }

    // --- TOTP core (RFC 6238 / HOTP RFC 4226) ---

    private function generateCode(string $base32Secret, int $counter): string
    {
        $key = $this->base32Decode($base32Secret);
        $counterBytes = pack('N*', 0) . pack('N*', $counter); // 8-byte big-endian counter

        $hash = hash_hmac('sha1', $counterBytes, $key, true);

        $offset = ord($hash[19]) & 0x0F;

        $truncated = ((ord($hash[$offset]) & 0x7F) << 24)
            | ((ord($hash[$offset + 1]) & 0xFF) << 16)
            | ((ord($hash[$offset + 2]) & 0xFF) << 8)
            | (ord($hash[$offset + 3]) & 0xFF);

        $code = $truncated % (10 ** self::DIGITS);

        return str_pad((string) $code, self::DIGITS, '0', STR_PAD_LEFT);
    }

    // --- Base32 (RFC 4648), since PHP has no native support ---

    private function base32Encode(string $data): string
    {
        $bits = '';
        foreach (str_split($data) as $char) {
            $bits .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }

        $output = '';
        foreach (str_split($bits, 5) as $chunk) {
            $chunk = str_pad($chunk, 5, '0', STR_PAD_RIGHT);
            $output .= self::BASE32_ALPHABET[bindec($chunk)];
        }

        return $output;
    }

    private function base32Decode(string $encoded): string
    {
        $encoded = strtoupper(rtrim($encoded, '='));
        $bits = '';

        foreach (str_split($encoded) as $char) {
            $pos = strpos(self::BASE32_ALPHABET, $char);
            if ($pos === false) {
                continue;
            }
            $bits .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
        }

        $bytes = '';
        foreach (str_split($bits, 8) as $chunk) {
            if (strlen($chunk) < 8) {
                continue;
            }
            $bytes .= chr(bindec($chunk));
        }

        return $bytes;
    }
}
