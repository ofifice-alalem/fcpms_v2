<?php

namespace App\Repositories\Contracts;

use App\Models\OfficialHoliday;
use App\Models\WorkScheduleTemplate;
use Illuminate\Database\Eloquent\Collection;

interface WorkScheduleRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all schedule templates with their assigned days and assigned consultants count.
     */
    public function getAllTemplatesWithDays(): Collection;

    /**
     * Create a schedule template along with its 7 days configuration.
     */
    public function createTemplateWithDays(array $templateData, array $daysData): WorkScheduleTemplate;

    /**
     * Update a schedule template along with its days configuration.
     */
    public function updateTemplateWithDays(WorkScheduleTemplate $template, array $templateData, array $daysData): WorkScheduleTemplate;

    /**
     * Reset default status on all templates.
     */
    public function resetDefaultTemplates(): void;

    /**
     * Record an official holiday.
     */
    public function addOfficialHoliday(array $data): OfficialHoliday;

    /**
     * Get all official holidays ordered by date.
     */
    public function getOfficialHolidays(): Collection;

    /**
     * Delete a schedule template.
     */
    public function deleteTemplate(WorkScheduleTemplate $template): bool;

    /**
     * Update an official holiday.
     */
    public function updateOfficialHoliday(OfficialHoliday $holiday, array $data): OfficialHoliday;

    /**
     * Delete an official holiday.
     */
    public function deleteOfficialHoliday(OfficialHoliday $holiday): bool;
}
