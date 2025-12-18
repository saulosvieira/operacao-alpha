<?php

declare(strict_types=1);

namespace App\Domain\Exam\Actions\Admin;

use App\Domain\Exam\Models\Question;
use Illuminate\Support\Facades\Storage;

final class DeleteQuestionAction
{
    /**
     * Execute the action to delete a question and its associated images
     *
     * @param int $questionId
     * @return bool
     */
    public function execute(int $questionId): bool
    {
        $question = Question::find($questionId);

        if (! $question) {
            return false;
        }

        // Delete all associated images from storage
        $this->deleteQuestionImages($question);

        return $question->delete();
    }

    /**
     * Delete all images associated with a question
     */
    private function deleteQuestionImages(Question $question): void
    {
        $imageFields = [
            'statement_image',
            'option_a_image',
            'option_b_image',
            'option_c_image',
            'option_d_image',
            'option_e_image',
        ];

        foreach ($imageFields as $field) {
            $this->deleteImage($question->{$field});
        }
    }

    /**
     * Delete an image from storage if it exists
     */
    private function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
