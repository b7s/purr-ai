@php
    use Zap\Models\Schedule;
    use Carbon\Carbon;

    $upcomingSchedules = Schedule::query()
        ->active()
        ->with(['periods' => fn($q) => $q->orderBy('date')->orderBy('start_time')])
        ->where('start_date', '>=', Carbon::today())
        ->orderBy('start_date')
        ->orderBy('id')
        ->limit(3)
        ->get();
@endphp

<div class="card purrai-opacity-box upcoming-schedules-widget">
    <div class="widget-header">
        <h3 class="widget-title">
            <i class="iconoir-calendar widget-icon"></i>
            {{ __('chat.widgets.upcoming_schedules.title') }}
        </h3>
    </div>

    <div class="widget-content">
        @if ($upcomingSchedules->isEmpty())
            <div class="empty-state">
                <p class="empty-message">
                    {{ __('chat.widgets.upcoming_schedules.no_schedules') }}
                </p>
                <p class="empty-hint pt-4">
                    <button
                        class="cursor-pointer button"
                        @click="document.querySelector(`[x-ref='messageInput']`).focus();"
                    >
                        {{ __('chat.widgets.upcoming_schedules.create_hint') }}
                    </button>
                </p>
            </div>
        @else
            <ul class="schedules-list">
                @foreach ($upcomingSchedules as $schedule)
                    <li
                        class="schedule-item"
                        @click="
    const messageInput = document.querySelector(`[x-ref='messageInput']`);
    messageInput.value = `{{ __('chat.widgets.upcoming_schedules.tell_me_more_abour_schedule', ['name' => $schedule->name]) }}`;
    messageInput.dispatchEvent(new Event('input'));
    messageInput.dispatchEvent(new Event('change'));
    messageInput.focus();
"
                    >
                        <div class="schedule-date">
                            <span class="schedule-day">{{ $schedule->start_date->format('d') }}</span>
                            <span class="schedule-month">{{ $schedule->start_date->format('M') }}</span>
                        </div>
                        <div class="schedule-details">
                            <h4 class="schedule-name">{{ $schedule->name }}</h4>

                            @if ($schedule->description)
                                <p class="schedule-description">{{ Str::limit($schedule->description, 50) }}</p>
                            @endif
                            <div class="schedule-meta">
                                @php
                                    $firstPeriod = $schedule->periods->first();
                                @endphp
                                @if ($firstPeriod)
                                    <span class="schedule-time">
                                        {{ Carbon::parse($firstPeriod->start_time)->format('H:i') }}
                                        @if ($firstPeriod->end_time)
                                            - {{ Carbon::parse($firstPeriod->end_time)->format('H:i') }}
                                        @endif
                                        @php
                                            $startTime = Carbon::parse($firstPeriod->start_time);
                                            $now = now();
                                            $isToday = $schedule->start_date->isToday() && $now->lt($startTime);
                                        @endphp
                                        @if ($isToday)
                                            <span class="schedule-badge today">
                                                {{ __('chat.widgets.upcoming_schedules.today') }}
                                            </span>
                                        @endif
                                    </span>
                                @endif
                                @if ($schedule->is_recurring)
                                    <span class="schedule-badge recurring">
                                        {{ __('chat.widgets.upcoming_schedules.recurring') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
