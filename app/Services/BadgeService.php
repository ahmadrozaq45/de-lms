<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;
use App\Models\UserBadge;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BadgeService
{
    /**
     * Periksa dan berikan badge setelah quiz selesai.
     */
    public function checkAfterQuiz(User $user, int $score, bool $isPassed): void
    {
        if ($score === 100) {
            $this->award($user, 'quiz_perfect');
        }
        if ($isPassed) {
            $this->award($user, 'quiz_passed');
        }
    }

    /**
     * Periksa dan berikan badge setelah assignment di-submit.
     */
    public function checkAfterSubmission(User $user): void
    {
        $this->award($user, 'assignment_submitted');
    }

    /**
     * Periksa dan berikan badge setelah kursus selesai.
     */
    public function checkAfterCourseComplete(User $user): void
    {
        $this->award($user, 'course_complete');
    }

    /**
     * Periksa dan berikan badge setelah semua materi di-module selesai.
     */
    public function checkAfterMaterialComplete(User $user): void
    {
        $this->award($user, 'material_complete');
    }

    /**
     * Periksa badge first login.
     */
    public function checkFirstLogin(User $user): void
    {
        $this->award($user, 'first_login');
    }

    /**
     * Berikan badge kepada user jika belum punya.
     * Return true jika badge baru diberikan.
     */
    public function award(User $user, string $badgeType): bool
    {
        $badge = Badge::where('type', $badgeType)->first();

        if (!$badge) {
            return false;
        }

        // Cek apakah user sudah punya badge ini
        $alreadyHas = UserBadge::where('user_id', $user->id)
                                ->where('badge_id', $badge->id)
                                ->exists();

        if ($alreadyHas) {
            return false;
        }

        UserBadge::create([
            'user_id'   => $user->id,
            'badge_id'  => $badge->id,
            'earned_at' => Carbon::now(),
        ]);

        return true;
    }

    /**
     * Ambil semua badge milik user beserta detailnya.
     */
    public function getUserBadges(User $user): array
    {
        return $user->badges()->orderByPivot('earned_at', 'desc')->get()->toArray();
    }
}
