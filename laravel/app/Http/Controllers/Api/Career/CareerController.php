<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Career;

use App\Domain\Career\Actions\GetCareerDetailsAction;
use App\Domain\Career\Actions\ListCareersAction;
use App\Domain\Career\Actions\ListExamsByCareerAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Career\CareerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class CareerController extends Controller
{
    public function __construct(
        private readonly ListCareersAction $listCareersAction,
        private readonly GetCareerDetailsAction $getCareerDetailsAction,
        private readonly ListExamsByCareerAction $listExamsByCareerAction,
    ) {
    }

    /**
     * List all active careers
     */
    public function index(): AnonymousResourceCollection
    {
        $careers = $this->listCareersAction->execute();

        return CareerResource::collection($careers);
    }

    /**
     * Get career details
     */
    public function show(int $id): JsonResponse
    {
        $career = $this->getCareerDetailsAction->execute($id);

        if (! $career) {
            return response()->json([
                'message' => 'Career not found',
            ], 404);
        }

        return response()->json([
            'data' => new CareerResource($career),
        ]);
    }

    /**
     * List all exams for a career
     */
    public function exams(int $id): JsonResponse
    {
        // First check if career exists
        $career = $this->getCareerDetailsAction->execute($id);

        if (! $career) {
            return response()->json([
                'message' => 'Career not found',
            ], 404);
        }

        $exams = $this->listExamsByCareerAction->execute($id);

        return response()->json([
            'data' => $exams,
        ]);
    }
}
