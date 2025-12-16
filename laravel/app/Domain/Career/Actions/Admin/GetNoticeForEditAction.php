<?php

declare(strict_types=1);

namespace App\Domain\Career\Actions\Admin;

use App\Domain\Career\DTOs\CareerData;
use App\Domain\Career\DTOs\NoticeData;
use App\Domain\Career\Models\Notice;

final class GetNoticeForEditAction
{
    /**
     * Execute the action to get a single notice for editing
     *
     * @param int $noticeId
     * @return NoticeData
     */
    public function execute(int $noticeId): NoticeData
    {
        $notice = Notice::with('career')->findOrFail($noticeId);

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
