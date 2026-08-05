<?php

namespace App\Repositories\Eloquent;

use App\Models\OfficialHoliday;
use App\Models\WorkScheduleDay;
use App\Models\WorkScheduleTemplate;
use App\Repositories\Contracts\WorkScheduleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class WorkScheduleRepository extends BaseRepository implements WorkScheduleRepositoryInterface
{
    public function model(): string
    {
        return WorkScheduleTemplate::class;
    }

    public function getAllTemplatesWithDays(): Collection
    {
        return WorkScheduleTemplate::with(['days', 'consultants'])
            ->withCount('consultants')
            ->orderBy('is_default', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }

    public function createTemplateWithDays(array $templateData, array $daysData): WorkScheduleTemplate
    {
        /** @var WorkScheduleTemplate $template */
        $template = $this->model->create([
            'name' => $templateData['name'],
            'description' => $templateData['description'] ?? null,
            'is_default' => $templateData['is_default'] ?? false,
        ]);

        foreach ($daysData as $dayItem) {
            $template->days()->create([
                'day_of_week' => $dayItem['day_of_week'],
                'is_working_day' => $dayItem['is_working_day'] ?? true,
            ]);
        }

        return $template->load('days');
    }

    public function updateTemplateWithDays(WorkScheduleTemplate $template, array $templateData, array $daysData): WorkScheduleTemplate
    {
        $template->update([
            'name' => $templateData['name'],
            'description' => $templateData['description'] ?? null,
            'is_default' => $templateData['is_default'] ?? false,
        ]);

        if (!empty($daysData)) {
            foreach ($daysData as $dayItem) {
                WorkScheduleDay::updateOrCreate(
                    [
                        'template_id' => $template->id,
                        'day_of_week' => $dayItem['day_of_week'],
                    ],
                    [
                        'is_working_day' => $dayItem['is_working_day'] ?? true,
                    ]
                );
            }
        }

        return $template->fresh(['days']);
    }

    public function resetDefaultTemplates(): void
    {
        $this->model->where('is_default', true)->update(['is_default' => false]);
    }

    public function addOfficialHoliday(array $data): OfficialHoliday
    {
        return OfficialHoliday::create([
            'name' => $data['name'],
            'start_date' => $data['start_date'] ?? $data['holiday_date'] ?? now()->toDateString(),
            'end_date' => $data['end_date'] ?? $data['start_date'] ?? $data['holiday_date'] ?? now()->toDateString(),
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function getOfficialHolidays(): Collection
    {
        return OfficialHoliday::orderBy('start_date', 'asc')->get();
    }

    public function deleteTemplate(WorkScheduleTemplate $template): bool
    {
        return (bool) $template->delete();
    }

    public function updateOfficialHoliday(OfficialHoliday $holiday, array $data): OfficialHoliday
    {
        $holiday->update([
            'name' => $data['name'],
            'start_date' => $data['start_date'] ?? $data['holiday_date'] ?? $holiday->start_date,
            'end_date' => $data['end_date'] ?? $data['start_date'] ?? $holiday->end_date,
            'notes' => $data['notes'] ?? null,
        ]);

        return $holiday->fresh();
    }

    public function deleteOfficialHoliday(OfficialHoliday $holiday): bool
    {
        return (bool) $holiday->delete();
    }
}
