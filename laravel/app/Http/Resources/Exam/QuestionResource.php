<?php

namespace App\Http\Resources\Exam;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class QuestionResource extends JsonResource
{
    /**
     * Whether to show the correct answer and explanation.
     * This is controlled by the parent resource based on feedback mode and attempt state.
     *
     * @var bool
     */
    protected $showAnswer = false;

    /**
     * Set whether to show the answer.
     *
     * @param bool $show
     * @return self
     */
    public function showAnswer($show = true)
    {
        $this->showAnswer = $show;
        return $this;
    }

    public function toArray($request): array
    {
        return [
            'id' => (string) $this->resource->id,
            'examId' => $this->resource->exam_id,
            'questionNumber' => $this->resource->question_number,
            'statement' => $this->resource->statement,
            'statementImage' => $this->generateImageUrl($this->resource->statement_image),
            'options' => [
                [
                    'letter' => 'A',
                    'text' => $this->resource->option_a,
                    'image' => $this->generateImageUrl($this->resource->option_a_image),
                ],
                [
                    'letter' => 'B',
                    'text' => $this->resource->option_b,
                    'image' => $this->generateImageUrl($this->resource->option_b_image),
                ],
                [
                    'letter' => 'C',
                    'text' => $this->resource->option_c,
                    'image' => $this->generateImageUrl($this->resource->option_c_image),
                ],
                [
                    'letter' => 'D',
                    'text' => $this->resource->option_d,
                    'image' => $this->generateImageUrl($this->resource->option_d_image),
                ],
                [
                    'letter' => 'E',
                    'text' => $this->resource->option_e,
                    'image' => $this->generateImageUrl($this->resource->option_e_image),
                ],
            ],
            'correctAnswer' => $this->when($this->showAnswer, $this->resource->correct_answer),
            'explanation' => $this->when($this->showAnswer, $this->resource->explanation),
        ];
    }

    /**
     * Generate a complete URL for an image path.
     * Returns null if the path is empty.
     */
    protected function generateImageUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        // If already a complete URL, return as-is
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // If it's a storage path, generate the URL
        if (str_starts_with($path, 'storage/')) {
            return url($path);
        }

        // Otherwise, assume it's a public storage path
        return url('storage/' . $path);
    }
}
