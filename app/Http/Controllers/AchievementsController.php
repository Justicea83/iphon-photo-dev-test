<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repository\Achievement\AchievementRepositoryInterface;
use Illuminate\Http\JsonResponse;

class AchievementsController extends Controller
{
    private AchievementRepositoryInterface $repository;
    function __construct(AchievementRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(User $user): JsonResponse
    {
        return $this->successResponse($this->repository->getUserAchievements($user));
    }
}
