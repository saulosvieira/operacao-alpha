<?php

namespace App\Http\Controllers\Api\User;

use App\Domain\Auth\Actions\DeleteUserAccountAction;
use App\Domain\Auth\Actions\GetUserProfileAction;
use App\Domain\Auth\Actions\UpdateUserProfileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Resources\Auth\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profile(Request $request, GetUserProfileAction $action): JsonResponse
    {
        $user = $action->execute($request->user()->id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'data' => new UserResource($user),
        ]);
    }

    public function updateProfile(
        UpdateProfileRequest $request,
        UpdateUserProfileAction $action
    ): JsonResponse {
        try {
            $user = $action->execute(
                $request->user()->id,
                $request->validated()
            );

            if (!$user) {
                return response()->json([
                    'message' => 'User not found',
                ], 404);
            }

            return response()->json([
                'message' => 'Profile updated successfully',
                'data' => new UserResource($user),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function deleteAccount(
        Request $request,
        DeleteUserAccountAction $action
    ): JsonResponse {
        try {
            $deleted = $action->execute($request->user()->id);

            if (!$deleted) {
                return response()->json([
                    'message' => 'Failed to delete account',
                ], 500);
            }

            // Revoke all tokens for this user
            $request->user()->tokens()->delete();

            return response()->json([
                'message' => 'Account deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
