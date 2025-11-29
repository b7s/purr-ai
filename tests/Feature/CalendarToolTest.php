<?php

declare(strict_types=1);

use App\Services\Prism\Tools\CalendarTool;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Zap\Models\Schedule;

uses(RefreshDatabase::class);

it('creates a calendar tool successfully', function () {
    $tool = CalendarTool::make();

    expect($tool->name())->toBe('calendar')
        ->and($tool->description())->toContain('calendar appointments')
        ->and($tool->hasParameters())->toBeTrue();
});

it('creates an appointment successfully', function () {
    $tool = CalendarTool::make();

    $result = $tool->handle(
        action: 'create',
        title: 'Team Meeting',
        description: 'Weekly sync meeting',
        start_date: '2025-12-15T10:00:00',
        end_date: '2025-12-15T11:00:00',
        type: 'meeting'
    );

    $data = json_decode($result, true);

    expect($data)->toHaveKey('success')
        ->and($data['success'])->toBeTrue()
        ->and($data)->toHaveKey('user_message')
        ->and($data['title'])->toBe('Team Meeting')
        ->and(Schedule::where('name', 'Team Meeting')->exists())->toBeTrue();
});

it('lists appointments', function () {
    $calendar = \App\Models\Calendar::create(['name' => 'Test Calendar']);

    \Zap\Facades\Zap::for($calendar)
        ->named('Doctor Appointment')
        ->appointment()
        ->from(now()->addDay()->format('Y-m-d'))
        ->addPeriod('10:00', '11:00')
        ->withMetadata(['description' => 'Annual checkup'])
        ->save();

    $tool = CalendarTool::make();

    $result = $tool->handle(action: 'list');

    $data = json_decode($result, true);

    expect($data)->toHaveKey('count')
        ->and($data['count'])->toBeGreaterThan(0)
        ->and($data['appointments'])->toBeArray();
});

it('updates an appointment', function () {
    $calendar = \App\Models\Calendar::create(['name' => 'Test Calendar']);

    $schedule = \Zap\Facades\Zap::for($calendar)
        ->named('Old Title')
        ->appointment()
        ->from(now()->addDay()->format('Y-m-d'))
        ->addPeriod('10:00', '11:00')
        ->save();

    $tool = CalendarTool::make();

    $result = $tool->handle(
        action: 'update',
        schedule_id: $schedule->id,
        title: 'Updated Title'
    );

    $data = json_decode($result, true);

    expect($data)->toHaveKey('success')
        ->and($data['success'])->toBeTrue()
        ->and($data)->toHaveKey('user_message')
        ->and($schedule->fresh()->name)->toBe('Updated Title');
});

it('deletes an appointment', function () {
    $calendar = \App\Models\Calendar::create(['name' => 'Test Calendar']);

    $schedule = \Zap\Facades\Zap::for($calendar)
        ->named('To Delete')
        ->appointment()
        ->from(now()->addDay()->format('Y-m-d'))
        ->addPeriod('10:00', '11:00')
        ->save();

    $tool = CalendarTool::make();

    $result = $tool->handle(
        action: 'delete',
        schedule_id: $schedule->id
    );

    $data = json_decode($result, true);

    expect($data)->toHaveKey('success')
        ->and($data['success'])->toBeTrue()
        ->and($data)->toHaveKey('user_message')
        ->and(Schedule::find($schedule->id))->toBeNull();
});

it('checks availability for a date', function () {
    $tool = CalendarTool::make();

    $result = $tool->handle(
        action: 'check_availability',
        date: '2025-12-20'
    );

    $data = json_decode($result, true);

    expect($data['date'])->toBe('2025-12-20')
        ->and($data)->toHaveKey('busy_slots')
        ->and($data)->toHaveKey('is_available');
});

it('returns error when required fields are missing for create', function () {
    $tool = CalendarTool::make();

    $result = $tool->handle(action: 'create');

    $data = json_decode($result, true);

    expect($data)->toHaveKey('error')
        ->and($data['error'])->toContain('required');
});

it('returns error when schedule not found for update', function () {
    $tool = CalendarTool::make();

    $result = $tool->handle(
        action: 'update',
        schedule_id: 99999
    );

    $data = json_decode($result, true);

    expect($data)->toHaveKey('error')
        ->and($data['error'])->toBe('Schedule not found');
});
