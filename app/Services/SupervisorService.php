<?php

namespace App\Services;

use App\Models\User;
use App\Models\Employee;
use App\Models\StudentGroup;
use App\Models\TeacherStudentGroup;
use Illuminate\Support\Collection;

class SupervisorService
{
    protected static array $isSupervisorCache = [];
    protected static array $supervisedGroupIdsCache = [];
    protected static array $supervisedGroupsCache = [];
    protected static array $supervisedEmployeesCache = [];

    /**
     * Clear the static in-memory caches.
     * Useful during testing to prevent stale states between different test scenarios.
     */
    public static function clearCache(): void
    {
        self::$isSupervisorCache = [];
        self::$supervisedGroupIdsCache = [];
        self::$supervisedGroupsCache = [];
        self::$supervisedEmployeesCache = [];
    }

    /**
     * Determine if a user (or employee) is a supervisor.
     *
     * @param mixed $user
     * @return bool
     */
    public static function isSupervisor($user): bool
    {
        if (app()->runningUnitTests()) {
            self::clearCache();
        }

        if (!$user) {
            return false;
        }

        $userId = $user instanceof User ? $user->id : ($user instanceof Employee ? $user->user_id : $user);

        if (!$userId) {
            return false;
        }

        if (isset(self::$isSupervisorCache[$userId])) {
            return self::$isSupervisorCache[$userId];
        }

        return self::$isSupervisorCache[$userId] = TeacherStudentGroup::where('teacher_id', $userId)
            ->where('job_title', 167)
            ->exists();
    }

    /**
     * Get the student groups under supervision.
     *
     * @param mixed $user
     * @param bool $onlyActive
     * @return Collection
     */
    public static function getSupervisedGroups($user, bool $onlyActive = false): Collection
    {
        if (app()->runningUnitTests()) {
            self::clearCache();
        }

        $userId = $user instanceof User ? $user->id : ($user instanceof Employee ? $user->user_id : $user);

        if (!$userId) {
            return collect();
        }

        $cacheKey = $userId . '_' . ($onlyActive ? '1' : '0');
        if (isset(self::$supervisedGroupsCache[$cacheKey])) {
            return self::$supervisedGroupsCache[$cacheKey];
        }

        $query = StudentGroup::whereIn('id', function ($q) use ($userId) {
            $q->select('student_group_id')
                ->from('teacher_student_group')
                ->where('teacher_id', $userId)
                ->where('job_title', 167);
        });

        if ($onlyActive) {
            $query->where('activation', 1);
        }

        return self::$supervisedGroupsCache[$cacheKey] = $query->orderBy('id', 'desc')->get();
    }

    /**
     * Get group IDs under supervision.
     *
     * @param mixed $user
     * @param bool $onlyActive
     * @return array
     */
    public static function getSupervisedGroupIds($user, bool $onlyActive = false): array
    {
        if (app()->runningUnitTests()) {
            self::clearCache();
        }

        $userId = $user instanceof User ? $user->id : ($user instanceof Employee ? $user->user_id : $user);

        if (!$userId) {
            return [];
        }

        $cacheKey = $userId . '_' . ($onlyActive ? '1' : '0');
        if (isset(self::$supervisedGroupIdsCache[$cacheKey])) {
            return self::$supervisedGroupIdsCache[$cacheKey];
        }

        $query = TeacherStudentGroup::where('teacher_id', $userId)
            ->where('job_title', 167);

        if ($onlyActive) {
            $query->whereHas('studentGroup', function ($q) {
                $q->where('activation', 1);
            });
        }

        return self::$supervisedGroupIdsCache[$cacheKey] = $query->pluck('student_group_id')
            ->unique()
            ->toArray();
    }

    /**
     * Get ordinary employees (job_title = 166) working in the supervised groups.
     *
     * @param mixed $user
     * @return Collection
     */
    public static function getSupervisedEmployees($user): Collection
    {
        if (app()->runningUnitTests()) {
            self::clearCache();
        }

        $userId = $user instanceof User ? $user->id : ($user instanceof Employee ? $user->user_id : $user);

        if (!$userId) {
            return collect();
        }

        if (isset(self::$supervisedEmployeesCache[$userId])) {
            return self::$supervisedEmployeesCache[$userId];
        }

        $groupIds = self::getSupervisedGroupIds($userId);

        if (empty($groupIds)) {
            return self::$supervisedEmployeesCache[$userId] = collect();
        }

        $teacherUserIds = TeacherStudentGroup::whereIn('student_group_id', $groupIds)
            ->where('job_title', 166)
            ->pluck('teacher_id')
            ->unique()
            ->toArray();

        return self::$supervisedEmployeesCache[$userId] = \App\Reposotries\employeeRepo::employees()
            ->whereIn('user_id', $teacherUserIds)
            ->sortBy('full_name')
            ->values();
    }
}
