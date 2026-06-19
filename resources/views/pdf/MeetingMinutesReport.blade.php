<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $meetingMinute->title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .meta {
            margin-bottom: 20px;
        }
        .meta-item {
            margin-bottom: 5px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .attendees {
            margin-bottom: 5px;
        }
        .topics {
            margin-left: 20px;
        }
        .topic {
            margin-bottom: 15px;
        }
        .action-items {
            margin-left: 20px;
        }
        .action-item {
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $meetingMinute->title }}</h1>
    </div>

    <div class="meta">
        <div class="meta-item">
            <strong>{{ __('minutes.details.date') }}:</strong> {{ \App\Helpers\DateHelper::formatDate($meetingMinute->meeting_date) }}
        </div>
        @if($meetingMinute->location)
        <div class="meta-item">
            <strong>{{ __('minutes.details.location') }}:</strong> {{ $meetingMinute->location }}
        </div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">{{ __('minutes.details.attendees') }}</div>
        @if($attendees->count() > 0)
            @foreach($attendees as $attendee)
                <div class="attendees">
                    {{ $attendee->name }} @if($attendee->email) &lt;{{ $attendee->email }}&gt; @endif
                </div>
            @endforeach
        @else
            <div>{{ __('minutes.details.no_attendees') }}</div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">{{ __('minutes.details.topics') }}</div>
        @if($topics->count() > 0)
            @foreach($topics as $topic)
                <div class="topic">
                    <div>{{ $topic->content }}</div>

                    @if($topic->topicActionItems->count() > 0)
                        <div class="action-items">
                            <div style="font-weight: bold; margin-top: 5px;">{{ __('minutes.details.action_items') }}:</div>
                            @foreach($topic->topicActionItems as $actionItem)
                                <div class="action-item">
                                    - {{ $actionItem->description }}
                                    @if($actionItem->assignee)
                                        ({{ __('minutes.details.assigned_to') }}: {{ $actionItem->assignee->name }})
                                    @endif
                                    @if($actionItem->due_date)
                                        ({{ __('minutes.details.due') }}: {{ \App\Helpers\DateHelper::formatDate($actionItem->due_date) }})
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div>{{ __('minutes.details.no_topics') }}</div>
        @endif
    </div>

    <div class="footer">
        {{ config('app.name') }} - {{ __('minutes.index.page_title') }} - {{ now()->format('d.m.Y H:i') }}
    </div>
</body>
</html>
