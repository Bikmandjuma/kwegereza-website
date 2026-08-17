<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_number', 'verification_code', 'user_id', 'course_id',
        'title', 'issued_by', 'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Certificate $certificate) {
            if (!$certificate->certificate_number) {
                $certificate->certificate_number = static::generateCertificateNumber();
            }
            if (!$certificate->verification_code) {
                $certificate->verification_code = (string) Str::uuid();
            }
            if (!$certificate->issued_at) {
                $certificate->issued_at = now();
            }
        });
    }

    public static function generateCertificateNumber(): string
    {
        $year = now()->year;
        $sequence = static::whereYear('created_at', $year)->count() + 1;

        return sprintf('KIU-%d-%06d', $year, $sequence);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function issuer()
    {
        return $this->belongsTo(Owner::class, 'issued_by');
    }

    public function verifyUrl(): string
    {
        return route('certificate.verify', $this->verification_code);
    }
}
