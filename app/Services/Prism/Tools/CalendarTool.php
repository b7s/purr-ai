<?php

declare(strict_types=1);

namespace App\Services\Prism\Tools;

use Prism\Prism\Schema\EnumSchema;
use Prism\Prism\Schema\NumberSchema;
use Prism\Prism\Schema\StringSchema;
use Prism\Prism\Tool;

class CalendarTool
{
    public static function make(): Tool
    {
        return (new Tool)
            ->as('calendar')
            ->for(
                'Manage calendar appointments, events, schedules, and reminders. Can create, list, update, and delete appointments and reminders for meetings, medical appointments, and other events. Can also create, list, and delete reminders for appointments. If the user does not provide the duration, consider the default of 1 hour. '.
                'When setting a reminder, search the database first for the item they want and if you can\'t find it, ask the user.'
            )
            ->withParameter(new EnumSchema(
                'action',
                'The action to perform on the calendar',
                ['create', 'list', 'update', 'delete', 'check_availability', 'create_reminder', 'list_reminders', 'delete_reminder']
            ), required: true)
            ->withParameter(new StringSchema(
                'title',
                'Title of the appointment (required for create/update)'
            ), required: false)
            ->withParameter(new StringSchema(
                'description',
                'Description of the appointment'
            ), required: false)
            ->withParameter(new StringSchema(
                'start_date',
                'Start date and time (identify what user send and convert to ISO 8601 format. e.g., 2024-12-01T10:00:00)'
            ), required: false)
            ->withParameter(new StringSchema(
                'end_date',
                'End date and time (identify what user send and convert to ISO 8601 format. e.g., 2024-12-01T11:00:00)'
            ), required: false)
            ->withParameter(new NumberSchema(
                'schedule_id',
                'Schedule ID (required for update/delete)'
            ), required: false)
            ->withParameter(new EnumSchema(
                'type',
                'Type of schedule',
                ['appointment', 'meeting', 'medical', 'blocked']
            ), required: false)
            ->withParameter(new StringSchema(
                'date',
                'Date to check availability (identify what user send and convert to ISO 8601 format. e.g., 2024-12-01)'
            ), required: false)
            ->withParameter(new NumberSchema(
                'limit',
                'Number of appointments to return (for list action). Default: 5. Max 50.'
            ), required: false)
            ->withParameter(new EnumSchema(
                'order',
                'Order direction for list action',
                ['upcoming', 'recent']
            ), required: false)
            ->withParameter(new NumberSchema(
                'minutes_before',
                'Minutes before the appointment to trigger reminder (e.g., 15, 30, 60)'
            ), required: false)
            ->withParameter(new NumberSchema(
                'reminder_id',
                'Reminder ID (required for delete_reminder)'
            ), required: false)
            ->using(function (string $action, ?string $title = null, ?string $description = null, ?string $start_date = null, ?string $end_date = null, ?int $schedule_id = null, ?string $type = null, ?string $date = null, ?int $limit = null, ?string $order = null, ?int $minutes_before = null, ?int $reminder_id = null): string {
                $handler = new CalendarToolHandler;

                return match ($action) {
                    'create' => $handler->createAppointment($title, $description, $start_date, $end_date, $type),
                    'list' => $handler->listAppointments($start_date, $end_date, $limit, $order),
                    'update' => $handler->updateAppointment($schedule_id, $title, $description, $start_date, $end_date, $type),
                    'delete' => $handler->deleteAppointment($schedule_id),
                    'check_availability' => $handler->checkAvailability($date),
                    'create_reminder' => $handler->createReminder($schedule_id, $minutes_before),
                    'list_reminders' => $handler->listReminders($schedule_id),
                    'delete_reminder' => $handler->deleteReminder($reminder_id),
                    default => json_encode(['error' => "Action [$action] not found"]),
                };
            });
    }
}
