<?php

declare(strict_types=1);

namespace App\Domain\Career\Actions\Admin;

use App\Domain\Career\DTOs\CareerData;
use App\Domain\Career\DTOs\NoticeData;
use App\Domain\Career\Models\Notice;
use Carbon\Carbon;

final class CreateNoticeAction
{
    /**
     * Execute the action to create a new notice
     *
     * @param int $careerId
     * @param string $title
     * @param string|null $description
     * @param string|null $examDate
     * @param string|null $registrationStart
     * @param string|null $registrationEnd
     * @param string|null $pdfUrl
     * @param bool $active
     * @return NoticeData
     */
    public function execute(
        int $careerId,
        string $title,
        ?string $description = null,
        ?string $examDate = null,
        ?string $registrationStart = null,
        ?string $registrationEnd = null,
        ?string $pdfUrl = null,
        bool $active = true
    ): NoticeData {
        $notice = Notice::create([
            'career_id' => $careerId,
            'title' => $title,
            'description' => $description,
            'exam_date' => $examDate ? Carbon::parse($examDate) : null,
            'registration_start' => $registrationStart ? Carbon::parse($registrationStart) : null,
            'registration_end' => $registrationEnd ? Carbon::parse($registrationEnd) : null,
            'pdf_url' => $pdfUrl,
            'active' => $active,
        ]);

        $notice->load('career');

        $careerData = null;
        if ($notice->career) {
            $careerData = new CareerData(
                id: $notice->career->id,
                name: $notice->career->name,
                description: $notice->career->description,
                active: $notice->career->active,
                createdAt: $notice->career->created_at->toIso8601String(),
                updatedAt: $notice->career->updated_at->toIso8601String(),
                slug: $notice->career->slug ?? '',
                examsCount: 0,
            );
        }

        return new NoticeData(
            id: $notice->id,
            careerId: $notice->career_id,
            title: $notice->title,
            description: $notice->description,
            examDate: $notice->exam_date?->toIso8601String(),
            registrationStart: $notice->registration_start?->toIso8601String(),
            registrationEnd: $notice->registration_end?->toIso8601String(),
            pdfUrl: $notice->pdf_url,
            active: $notice->active,
            createdAt: $notice->created_at->toIso8601String(),
            updatedAt: $notice->updated_at->toIso8601String(),
            career: $careerData,
        );
    }
}
