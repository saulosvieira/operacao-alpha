<?php

declare(strict_types=1);

namespace App\Domain\Exam\Actions\Admin;

use App\Domain\Exam\DTOs\Admin\QuestionFormData;
use App\Domain\Exam\Models\Question;
use Illuminate\Support\Facades\Storage;

final class CreateQuestionAction
{
    /**
     * Execute the action to create a new question for an exam
     *
     * @param int $examId
     * @param QuestionFormData $data
     * @return Question
     */
    public function execute(int $examId, QuestionFormData $data): Question
    {
        $questionData = $data->toArray();
        $questionData['exam_id'] = $examId;

        // Handle statement image upload
        if ($data->statementImage) {
            $questionData['statement_image'] = $this->storeImage($data->statementImage, $examId);
        }

        // Handle option images upload
        if ($data->optionAImage) {
            $questionData['option_a_image'] = $this->storeImage($data->optionAImage, $examId);
        }
        if ($data->optionBImage) {
            $questionData['option_b_image'] = $this->storeImage($data->optionBImage, $examId);
        }
        if ($data->optionCImage) {
            $questionData['option_c_image'] = $this->storeImage($data->optionCImage, $examId);
        }
        if ($data->optionDImage) {
            $questionData['option_d_image'] = $this->storeImage($data->optionDImage, $examId);
        }
        if ($data->optionEImage) {
            $questionData['option_e_image'] = $this->storeImage($data->optionEImage, $examId);
        }

        return Question::create($questionData);
    }

    /**
     * Store an uploaded image and return the storage path
     */
    private function storeImage(\Illuminate\Http\UploadedFile $file, int $examId): string
    {
        $path = $file->store("questions/exam_{$examId}", 'public');

        return $path;
    }
}
