<?php

declare(strict_types=1);

namespace App\Domain\Exam\Actions\Admin;

use App\Domain\Exam\DTOs\Admin\QuestionFormData;
use App\Domain\Exam\Models\Question;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class UpdateQuestionAction
{
    /**
     * Execute the action to update an existing question
     *
     * @param int $questionId
     * @param QuestionFormData $data
     * @return Question
     */
    public function execute(int $questionId, QuestionFormData $data): Question
    {
        $question = Question::findOrFail($questionId);
        $questionData = $data->toArray();

        // Handle statement image
        $questionData['statement_image'] = $this->handleImageField(
            $data->statementImage,
            $question->statement_image,
            $data->removeStatementImage,
            $question->exam_id
        );

        // Handle option A image
        $questionData['option_a_image'] = $this->handleImageField(
            $data->optionAImage,
            $question->option_a_image,
            $data->removeOptionAImage,
            $question->exam_id
        );

        // Handle option B image
        $questionData['option_b_image'] = $this->handleImageField(
            $data->optionBImage,
            $question->option_b_image,
            $data->removeOptionBImage,
            $question->exam_id
        );

        // Handle option C image
        $questionData['option_c_image'] = $this->handleImageField(
            $data->optionCImage,
            $question->option_c_image,
            $data->removeOptionCImage,
            $question->exam_id
        );

        // Handle option D image
        $questionData['option_d_image'] = $this->handleImageField(
            $data->optionDImage,
            $question->option_d_image,
            $data->removeOptionDImage,
            $question->exam_id
        );

        // Handle option E image
        $questionData['option_e_image'] = $this->handleImageField(
            $data->optionEImage,
            $question->option_e_image,
            $data->removeOptionEImage,
            $question->exam_id
        );

        $question->update($questionData);

        return $question->fresh();
    }

    /**
     * Handle image field update logic
     *
     * @param UploadedFile|null $newImage New uploaded image
     * @param string|null $existingImage Current image path in database
     * @param bool $removeImage Whether to remove the existing image
     * @param int $examId Exam ID for storage path
     * @return string|null The image path to store
     */
    private function handleImageField(
        ?UploadedFile $newImage,
        ?string $existingImage,
        bool $removeImage,
        int $examId
    ): ?string {
        // If a new image is uploaded, delete old and store new
        if ($newImage) {
            $this->deleteImage($existingImage);

            return $newImage->store("questions/exam_{$examId}", 'public');
        }

        // If removal is requested, delete and return null
        if ($removeImage && $existingImage) {
            $this->deleteImage($existingImage);

            return null;
        }

        // Keep existing image
        return $existingImage;
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
