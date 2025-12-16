<?php

declare(strict_types=1);

namespace App\Domain\Career\Repositories;

use App\Domain\Career\DTOs\NoticeData;
use App\Domain\Career\Models\Notice;
use Illuminate\Support\Collection;

final class NoticeRepository
{
    /**
     * Get all active notices for a career
     *
     * @return Collection<int, NoticeData>
     */
    public function getByCareer(int $careerId): Collection
    {
        return Notice::where('career_id', $careerId)
            ->where('active', true)
            ->orderBy('exam_date', 'desc')
            ->get()
            ->map(fn (Notice $notice) => $this->toDTO($notice));
    }

    /**
     * Get all active notices
     *
     * @return Collection<int, NoticeData>
     */
    public function getAllActive(): Collection
    {
        return Notice::where('active', true)
            ->orderBy('exam_date', 'desc')
            ->get()
            ->map(fn (Notice $notice) => $this->toDTO($notice));
    }

    /**
     * Find notice by ID
     */
    public function findById(int $id): ?NoticeData
    {
        $notice = Notice::find($id);

        return $notice ? $this->toDTO($notice) : null;
    }

    /**
     * Convert Notice model to NoticeData DTO
     */
    private function toDTO(Notice $notice): NoticeData
    {
        return new NoticeData(
            id: $notice->id,
            careerId: $notice->career_id,
            title: $notice->title,
            description: $notice->description,
            examDate: $notice->exam_date?->toDateString(),
            registrationStart: $notice->registration_start?->toDateString(),
            registrationEnd: $notice->registration_end?->toDateString(),
            pdfUrl: $notice->pdf_url,
            active: $notice->active,
            createdAt: $notice->created_at->toIso8601String(),
            updatedAt: $notice->updated_at->toIso8601String(),
        );
    }
}
