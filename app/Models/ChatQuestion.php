<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'question', 'answer', 'category', 'keywords', 'language',
        'status', 'times_matched', 'created_by', 'updated_by',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Very small, dependency-free keyword-overlap matcher.
     * Not AI — deliberately simple and inspectable, per the spec's
     * "predefined Q&A now, AI later" instruction. Swap this method's
     * internals for a real AI/embedding call later without touching
     * anything else that depends on it.
     */
    public static function findBestMatch(string $userQuestion): ?self
    {
        $normalize = fn ($s) => collect(preg_split('/[\s,،؟?!.]+/u', mb_strtolower(trim($s))))
            ->filter(fn ($w) => mb_strlen($w) > 2)
            ->values();

        $userWords = $normalize($userQuestion);

        if ($userWords->isEmpty()) {
            return null;
        }

        $best = null;
        $bestScore = 0;

        static::active()->get()->each(function ($candidate) use ($normalize, $userWords, &$best, &$bestScore) {
            $haystack = $candidate->question . ' ' . $candidate->keywords;
            $candidateWords = $normalize($haystack);

            $overlap = $userWords->intersect($candidateWords)->count();

            if ($overlap > $bestScore) {
                $bestScore = $overlap;
                $best = $candidate;
            }
        });

        // Require at least one real keyword overlap — otherwise it's a miss.
        return $bestScore > 0 ? $best : null;
    }
}
