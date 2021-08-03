<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class AchievementsController extends Controller
{
    public function index(User $user): JsonResponse
    {
        return response()->json([
            'unlocked_achievements' => $user->achievements->pluck('name'),
            'next_available_achievements' => $user->next_available_achievements->pluck('name'),
            'current_badge' => $user->badge,
            'next_badge' => $user->next_badge,
            'remaining_to_unlock_next_badge' => $user->remaining_to_unlock_next_badge,
        ]);
    }
}
