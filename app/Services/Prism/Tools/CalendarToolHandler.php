<?php

declare(strict_types=1);

namespace App\Services\Prism\Tools;

use Illuminate\Support\Facades\Log;

class CalendarToolHandler
{
    public function createAppointment(?string $title, ?string $description, ?string $start_date, ?string $end_date, ?string $type): string
    {
        if (empty($title) || empty($start_date) || empty($end_date)) {
            return json_encode([
                'error' => 'Title, start_date, and end_date are required',
                'user_message' => __('chat.calendar.errors.missing_required_fields'),
            ]);
        }

        try {
            $calendar = \App\Models\Calendar::query()->firstOrCreate([
                'name' => 'Default Calendar',
            ]);

            $startDateTime = new \DateTime($start_date);
            $endDateTime = new \DateTime($end_date);

            $builder = \Zap\Facades\Zap::for($calendar)
                ->named($title);

            if ($description) {
                $builder->withMetadata(['description' => $description]);
            }

            $scheduleType = strtolower($type ?? 'appointment');
            if ($scheduleType === 'blocked') {
                $builder->blocked();
            } else {
                $builder->appointment();
            }

            $schedule = $builder
                ->from($startDateTime->format('Y-m-d'))
                ->addPeriod($startDateTime->format('H:i'), $endDateTime->format('H:i'))
                ->save();

            $formattedStart = $startDateTime->format('M d, Y \a\t g:i A');
            $formattedEnd = $endDateTime->format('g:i A');

            return json_encode([
                'success' => true,
                'schedule_id' => $schedule->id,
                'title' => $schedule->name,
                'start' => $start_date,
                'end' => $end_date,
                'user_message' => __('chat.calendar.appointment_created', [
                    'title' => $title,
                    'start' => $formattedStart,
                    'end' => $formattedEnd,
                ]),
            ]);
        } catch (\Throwable $e) {
            Log::error('CalendarTool: Failed to create appointment', [
                'error' => $e->getMessage(),
            ]);

            return json_encode([
                'error' => $e->getMessage(),
                'user_message' => __('chat.calendar.errors.create_failed'),
            ]);
        }
    }

    public function listAppointments(?string $start_date, ?string $end_date, ?int $limit = null, ?string $order = null): string
    {
        try {
            $calendar = \App\Models\Calendar::first();

            if (! $calendar) {
                return json_encode([
                    'success' => true,
                    'count' => 0,
                    'appointments' => [],
                    'user_message' => __('chat.calendar.no_appointments'),
                ]);
            }

            $query = $calendar->schedules()->with('periods');

            if ($limit !== null && $order !== null) {
                $now = \Carbon\Carbon::now();

                if ($order === 'upcoming') {
                    $schedules = $query->get()->filter(fn ($schedule) => $schedule->periods->contains(fn ($period) => $period->start_date_time->gte($now)))
                        ->sortBy(fn ($schedule) => $schedule->periods->min('start_date_time'))
                        ->take($limit);
                } else {
                    $schedules = $query->get()->filter(fn ($schedule) => $schedule->periods->contains(fn ($period) => $period->end_date_time->lte($now)))
                        ->sortByDesc(fn ($schedule) => $schedule->periods->max('end_date_time'))
                        ->take($limit);
                }
            } else {
                $schedules = $query->get();

                if (! empty($start_date)) {
                    $startDate = \Carbon\Carbon::parse($start_date);
                    $schedules = $schedules->filter(fn ($schedule) => $schedule->periods->contains(fn ($period) => $period->start_date_time->gte($startDate)));
                }

                if (! empty($end_date)) {
                    $endDate = \Carbon\Carbon::parse($end_date);
                    $schedules = $schedules->filter(fn ($schedule) => $schedule->periods->contains(fn ($period) => $period->end_date_time->lte($endDate)));
                }
            }

            $appointments = $schedules->map(fn ($schedule) => [
                'id' => $schedule->id,
                'title' => $schedule->name,
                'description' => $schedule->metadata['description'] ?? null,
                'type' => $schedule->schedule_type?->value ?? 'custom',
                'periods' => $schedule->periods->map(fn ($period) => [
                    'start' => $period->start_date_time->toIso8601String(),
                    'end' => $period->end_date_time->toIso8601String(),
                ])->toArray(),
            ])
                ->values()
                ->toArray();

            $count = \count($appointments);

            if ($limit !== null && $order !== null) {
                $summary = $count === 0
                    ? ($order === 'upcoming' ? __('chat.calendar.no_upcoming_appointments') : __('chat.calendar.no_recent_appointments'))
                    : ($order === 'upcoming'
                        ? trans_choice('chat.calendar.upcoming_appointments_found', $count, ['count' => $count])
                        : trans_choice('chat.calendar.recent_appointments_found', $count, ['count' => $count]));
            } else {
                $summary = $count === 0
                    ? __('chat.calendar.no_appointments_period')
                    : trans_choice('chat.calendar.appointments_found', $count, ['count' => $count]);
            }

            return json_encode([
                'success' => true,
                'count' => $count,
                'appointments' => $appointments,
                'user_message' => $summary,
            ]);
        } catch (\Throwable $e) {
            Log::error('CalendarTool: Failed to list appointments', [
                'error' => $e->getMessage(),
            ]);

            return json_encode([
                'error' => $e->getMessage(),
                'user_message' => __('chat.calendar.errors.list_failed'),
            ]);
        }
    }

    public function updateAppointment(?int $schedule_id, ?string $title, ?string $description, ?string $start_date, ?string $end_date, ?string $type): string
    {
        if (empty($schedule_id)) {
            return json_encode([
                'error' => 'schedule_id is required',
                'user_message' => __('chat.calendar.errors.schedule_id_required'),
            ]);
        }

        try {
            $schedule = \Zap\Models\Schedule::find($schedule_id);

            if (! $schedule) {
                return json_encode([
                    'error' => 'Schedule not found',
                    'user_message' => __('chat.calendar.appointment_not_found'),
                ]);
            }

            $changes = [];

            if (! empty($title)) {
                $schedule->name = $title;
                $changes[] = 'title';
            }

            if (! empty($type)) {
                $schedule->schedule_type = $this->mapScheduleType($type);
                $changes[] = 'type';
            }

            $metadata = $schedule->metadata ?? [];
            if (! empty($description)) {
                $metadata['description'] = $description;
                $schedule->metadata = $metadata;
                $changes[] = 'description';
            }

            $schedule->save();

            if (! empty($start_date) && ! empty($end_date)) {
                $startDateTime = new \DateTime($start_date);
                $endDateTime = new \DateTime($end_date);

                $schedule->periods()->delete();

                $schedule->periods()->create([
                    'date' => $startDateTime->format('Y-m-d'),
                    'start_time' => $startDateTime->format('H:i:s'),
                    'end_time' => $endDateTime->format('H:i:s'),
                    'is_available' => false,
                ]);

                $changes[] = 'time';
            }

            $changesText = empty($changes) ? __('chat.calendar.no_changes') : implode(', ', $changes);

            return json_encode([
                'success' => true,
                'schedule_id' => $schedule->id,
                'title' => $schedule->name,
                'user_message' => __('chat.calendar.appointment_updated', [
                    'title' => $schedule->name,
                    'changes' => $changesText,
                ]),
            ]);
        } catch (\Throwable $e) {
            Log::error('CalendarTool: Failed to update appointment', [
                'schedule_id' => $schedule_id,
                'error' => $e->getMessage(),
            ]);

            return json_encode([
                'error' => $e->getMessage(),
                'user_message' => __('chat.calendar.errors.update_failed'),
            ]);
        }
    }

    public function deleteAppointment(?int $schedule_id): string
    {
        if (empty($schedule_id)) {
            return json_encode([
                'error' => 'schedule_id is required',
                'user_message' => __('chat.calendar.errors.schedule_id_required_delete'),
            ]);
        }

        try {
            $schedule = \Zap\Models\Schedule::find($schedule_id);

            if (! $schedule) {
                return json_encode([
                    'error' => 'Schedule not found',
                    'user_message' => __('chat.calendar.appointment_not_found'),
                ]);
            }

            $title = $schedule->name;
            $schedule->delete();

            return json_encode([
                'success' => true,
                'deleted_title' => $title,
                'user_message' => __('chat.calendar.appointment_deleted', ['title' => $title]),
            ]);
        } catch (\Throwable $e) {
            Log::error('CalendarTool: Failed to delete appointment', [
                'schedule_id' => $schedule_id,
                'error' => $e->getMessage(),
            ]);

            return json_encode([
                'error' => $e->getMessage(),
                'user_message' => __('chat.calendar.errors.delete_failed'),
            ]);
        }
    }

    public function checkAvailability(?string $date): string
    {
        if (empty($date)) {
            return json_encode([
                'error' => 'date is required',
                'user_message' => __('chat.calendar.errors.date_required'),
            ]);
        }

        try {
            $calendar = \App\Models\Calendar::first();

            if (! $calendar) {
                return json_encode([
                    'success' => true,
                    'date' => $date,
                    'busy_slots' => [],
                    'is_available' => true,
                    'user_message' => __('chat.calendar.availability_no_calendar', ['date' => $date]),
                ]);
            }

            $slots = $calendar->getBookableSlots($date, 60, 0);

            $busySlots = collect($slots)
                ->filter(fn ($slot) => ! $slot['is_available'])
                ->map(fn ($slot) => [
                    'start' => $slot['start_time'],
                    'end' => $slot['end_time'],
                ])
                ->values()
                ->toArray();

            $availableCount = collect($slots)->filter(fn ($slot) => $slot['is_available'])->count();
            $busyCount = \count($busySlots);

            $summary = $busyCount === 0
                ? __('chat.calendar.availability_full', ['date' => $date])
                : __('chat.calendar.availability_summary', [
                    'date' => $date,
                    'available' => $availableCount,
                    'busy' => $busyCount,
                ]);

            return json_encode([
                'success' => true,
                'date' => $date,
                'busy_slots' => $busySlots,
                'available_slots' => $availableCount,
                'is_available' => $availableCount > 0,
                'user_message' => $summary,
            ]);
        } catch (\Throwable $e) {
            Log::error('CalendarTool: Failed to check availability', [
                'date' => $date,
                'error' => $e->getMessage(),
            ]);

            return json_encode([
                'error' => $e->getMessage(),
                'user_message' => __('chat.calendar.errors.availability_failed'),
            ]);
        }
    }

    public function createReminder(?int $schedule_id, ?int $minutes_before): string
    {
        if (empty($schedule_id)) {
            return json_encode([
                'error' => 'schedule_id is required',
                'user_message' => __('chat.calendar.errors.schedule_id_required'),
            ]);
        }

        if (empty($minutes_before) || $minutes_before <= 0) {
            return json_encode([
                'error' => 'minutes_before must be a positive number',
                'user_message' => __('chat.calendar.errors.invalid_minutes'),
            ]);
        }

        try {
            $schedule = \Zap\Models\Schedule::with('periods')->find($schedule_id);

            if (! $schedule) {
                return json_encode([
                    'error' => 'Schedule not found',
                    'user_message' => __('chat.calendar.appointment_not_found'),
                ]);
            }

            $firstPeriod = $schedule->periods->first();
            if (! $firstPeriod) {
                return json_encode([
                    'error' => 'Schedule has no periods',
                    'user_message' => __('chat.calendar.errors.no_periods'),
                ]);
            }

            $remindAt = $firstPeriod->start_date_time->copy()->subMinutes($minutes_before);

            $reminder = \App\Models\ScheduleReminder::create([
                'schedule_id' => $schedule_id,
                'minutes_before' => $minutes_before,
                'remind_at' => $remindAt,
            ]);

            return json_encode([
                'success' => true,
                'reminder_id' => $reminder->id,
                'schedule_title' => $schedule->name,
                'minutes_before' => $minutes_before,
                'remind_at' => $remindAt->toIso8601String(),
                'user_message' => __('chat.calendar.reminder_created', [
                    'title' => $schedule->name,
                    'minutes' => $minutes_before,
                    'time' => $remindAt->format('M d, Y \a\t g:i A'),
                ]),
            ]);
        } catch (\Throwable $e) {
            Log::error('CalendarTool: Failed to create reminder', [
                'schedule_id' => $schedule_id,
                'error' => $e->getMessage(),
            ]);

            return json_encode([
                'error' => $e->getMessage(),
                'user_message' => __('chat.calendar.errors.reminder_create_failed'),
            ]);
        }
    }

    public function listReminders(?int $schedule_id): string
    {
        try {
            $query = \App\Models\ScheduleReminder::with('schedule');

            if ($schedule_id) {
                $query->where('schedule_id', $schedule_id);
            }

            $reminders = $query->orderBy('remind_at')->get();

            $remindersList = $reminders->map(fn ($reminder) => [
                'id' => $reminder->id,
                'schedule_id' => $reminder->schedule_id,
                'schedule_title' => $reminder->schedule->name ?? 'Unknown',
                'minutes_before' => $reminder->minutes_before,
                'hours_before' => $reminder->minutes_before / 60,
                'days_before' => $reminder->minutes_before / 1440,
                'remind_at' => $reminder->remind_at->toIso8601String(),
                'is_sent' => $reminder->is_sent,
                'sent_at' => $reminder->sent_at?->toIso8601String(),
            ])->toArray();

            $count = \count($remindersList);

            $summary = $schedule_id
                ? trans_choice('chat.calendar.reminders_for_schedule', $count, ['count' => $count])
                : trans_choice('chat.calendar.total_reminders', $count, ['count' => $count]);

            return json_encode([
                'success' => true,
                'count' => $count,
                'reminders' => $remindersList,
                'user_message' => $summary,
            ]);
        } catch (\Throwable $e) {
            Log::error('CalendarTool: Failed to list reminders', [
                'error' => $e->getMessage(),
            ]);

            return json_encode([
                'error' => $e->getMessage(),
                'user_message' => __('chat.calendar.errors.reminder_list_failed'),
            ]);
        }
    }

    public function deleteReminder(?int $reminder_id): string
    {
        if (empty($reminder_id)) {
            return json_encode([
                'error' => 'reminder_id is required',
                'user_message' => __('chat.calendar.errors.reminder_id_required'),
            ]);
        }

        try {
            $reminder = \App\Models\ScheduleReminder::with('schedule')->find($reminder_id);

            if (! $reminder) {
                return json_encode([
                    'error' => 'Reminder not found',
                    'user_message' => __('chat.calendar.errors.reminder_not_found'),
                ]);
            }

            $scheduleTitle = $reminder->schedule->name ?? 'Unknown';
            $reminder->delete();

            return json_encode([
                'success' => true,
                'deleted_reminder_id' => $reminder_id,
                'schedule_title' => $scheduleTitle,
                'user_message' => __('chat.calendar.reminder_deleted', [
                    'title' => $scheduleTitle,
                ]),
            ]);
        } catch (\Throwable $e) {
            Log::error('CalendarTool: Failed to delete reminder', [
                'reminder_id' => $reminder_id,
                'error' => $e->getMessage(),
            ]);

            return json_encode([
                'error' => $e->getMessage(),
                'user_message' => __('chat.calendar.errors.reminder_delete_failed'),
            ]);
        }
    }

    private function mapScheduleType(string $type): \Zap\Enums\ScheduleTypes
    {
        return match (strtolower($type)) {
            'meeting' => \Zap\Enums\ScheduleTypes::APPOINTMENT,
            'medical' => \Zap\Enums\ScheduleTypes::APPOINTMENT,
            'blocked' => \Zap\Enums\ScheduleTypes::BLOCKED,
            default => \Zap\Enums\ScheduleTypes::APPOINTMENT,
        };
    }
}
