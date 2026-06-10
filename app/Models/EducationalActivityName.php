<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EducationalActivityName extends Model
{
    use HasFactory;

    protected $table = 'educational_activity_names';

    protected $fillable = [
        'activity_name',
        'activity_domain',
        'available_in_active_groups',
        'description',
        'teachers',
        'activation',
    ];

    protected $casts = [
        'teachers' => 'array',
        'available_in_active_groups' => 'boolean',
        'activation' => 'integer',
    ];

    // ========================
    // Normalization & Core Content
    // ========================

    /**
     * Normalize an activity name to a consistent format:
     * - Trim leading/trailing whitespace
     * - Replace dashes and underscores surrounded by optional spaces with a single space
     * - Collapse multiple consecutive spaces into one
     */
    public static function normalizeName(string $name): string
    {
        // Trim outer whitespace
        $name = trim($name);

        // Replace any separator (dash or underscore) optionally surrounded by spaces with a single space
        $name = preg_replace('/\s*[-_]+\s*/', ' ', $name);

        // Collapse multiple spaces into one
        $name = preg_replace('/\s+/', ' ', $name);

        return trim($name);
    }

    /**
     * Extract the "core" content of a name for semantic duplicate detection:
     * - Strip anything inside parentheses () e.g. "(Hello abc)"
     * - Apply normalizeName rules (separators → space, collapse spaces)
     * - Lowercase (for case-insensitive comparison)
     * - Strip session/skill prefixes (المهارة / الجلسة followed by ordinals or digits)
     */
    public static function extractCoreName(string $name): string
    {
        // Remove anything inside parentheses (and the parentheses themselves)
        $name = preg_replace('/\([^)]*\)/', '', $name);

        // Apply standard normalization
        $name = self::normalizeName($name);

        // Lowercase for comparison (works for both Arabic and Latin)
        $name = mb_strtolower($name, 'UTF-8');

        // Strip session/skill prefixes: (المهارة / المهارة / الجلسة / الجلسه / مهارة / جلسة) + (الأولى/الثانية/... or digits)
        $ordinals = '(الأولى|الاولى|الثانية|الثانيه|الثالثة|الثالثه|الرابعة|الرابعه|الخامسة|الخامسه|السادسة|السادسه|السابعة|السابعه|الثامنة|الثامنه|التاسعة|التاسعه|العاشرة|العاشره|الاول|الأول|الثاني|الثالث|الرابع|الخامس|السادس|السابع|الثامن|التاسع|العاشر|\d+)';
        $prefixPattern = '/(المهارة|المهاره|المهارات|المهاراه|الجلسة|الجلسه|جلسة|جلسه|مهارة|مهاره)\s+' . $ordinals . '/u';

        $name = preg_replace($prefixPattern, ' ', $name);

        // Re-normalize to clean up any leftover spaces/separators
        return self::normalizeName($name);
    }

    /**
     * Check whether another record with the same core content already exists.
     * Pass $excludeId to ignore the current record when editing.
     */
    public static function isCoreDuplicate(string $name, ?int $excludeId = null): bool
    {
        $core = self::extractCoreName($name);

        return self::all(['id', 'activity_name'])
            ->filter(function ($record) use ($core, $excludeId) {
                if ($excludeId && $record->id === $excludeId) {
                    return false;
                }
                return self::extractCoreName($record->getRawOriginal('activity_name')) === $core;
            })
            ->isNotEmpty();
    }

    /**
     * Mutator: automatically normalize activity_name on every assignment.
     */
    public function setActivityNameAttribute(string $value): void
    {
        $this->attributes['activity_name'] = self::normalizeName($value);
    }

    // ========================
    // Relations
    // ========================

    /**
     * Get the status (domain) associated with this activity name.
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'activity_domain');
    }
}
