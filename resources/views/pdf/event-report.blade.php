@php use App\Enums\Gender; @endphp
        <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1"
    >

    <title>{{ __('pdf.event_report.title') }}</title>

    <!-- Fonts -->
    <link rel="preconnect"
          href="https://fonts.bunny.net"
    >
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap"
          rel="stylesheet"
    />


    <style>
        p {
            padding: 0;
            margin: 0;
        }

        html,
        body {
            margin: 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
            background: hsl(0, 0%, 98%);
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Lato', sans-serif;
            color: #000000;
            margin-top: 0;
            font-weight: 400;
        }

        body {
            font-family: 'Lato', sans-serif;
            font-weight: 400;
            font-size: 11pt;
            line-height: 1.8;
            color: rgba(0, 0, 0, .4);
        }
    </style>
</head>
<body style="font-size: 10pt">
<br>
<br>
<table align="center"
       role="presentation"
       cellspacing="0"
       cellpadding="0"
       border="0"
       width="100%"
       style="margin: auto;"
>
    <tr>
        <td align="center">
            <span style="font-size:20pt;">{{ setting('organization.name') }}</span>
        </td>
    </tr>
</table>
<br><br>
<h1>{{ __('pdf.event_report.heading', ['title' => $event->title['de']]) }}</h1>

<h2>{{ __('pdf.event_report.summary') }}</h2>

<h3>{{ __('pdf.event_report.finances') }}</h3>
<table width="60%">
    <tr>
        <th>{{ __('pdf.event_report.income') }}</th>
        <td align="right">{{ \App\Helpers\MoneyHelper::formatCents((int)($income * 100)) }}</td>
    </tr>
    <tr>
        <th>{{ __('pdf.event_report.expenses') }}</th>
        <td align="right">{{ \App\Helpers\MoneyHelper::formatCents((int)($spending * 100)) }}</td>
    </tr>
    <tr>
        <th style="border-top: 1px solid #888; font-size: 12pt;">{{ __('pdf.event_report.total') }}</th>
        <td style="border-top: 1px solid #888; font-size: 12pt;"
            align="right"
        >{{ \App\Helpers\MoneyHelper::formatCents((int)(($income - $spending) * 100)) }}</td>
    </tr>
</table>


<h3>{{ __('pdf.event_report.visitors') }}</h3>

<table width="60%">
    <tr>
        <th>{{ __('pdf.event_report.total_visitors') }}</th>
        <td align="right">{{ $visitors->count() }}</td>
    </tr>
    <tr>
        <th>{{ __('pdf.event_report.total_male') }}</th>
        <td align="right">{{ $visitors->count() }}</td>
    </tr>
    <tr>
        <th>{{ __('pdf.event_report.total_female') }}</th>
        <td align="right">{{ $visitors->count() }}</td>
    </tr>
</table>
<br>
<table width="60%">
    <tr>
        <th>{{ __('pdf.event_report.members') }}</th>
        <td align="right">{{ $visitors->count() }}</td>
    </tr>
    <tr>
        <th>{{ __('pdf.event_report.registered_online') }}</th>
        <td align="right">{{ $visitors->count() }}</td>
    </tr>
</table>

<p style="page-break-after: right; "></p>
<br><br>


<h1>{{ __('pdf.event_report.details') }}</h1>

<h2>{{ __('pdf.event_report.income_detail_header') }}</h2>

<table cellpadding="3">
    <tr>
        <th>{{ __('pdf.event_report.table_text') }}</th>
        <th>{{ __('pdf.event_report.table_reference') }}</th>
        <th>{{ __('pdf.event_report.table_status') }}</th>
        <th>{{ __('pdf.event_report.table_account') }}</th>
        <th align="right">{{ __('pdf.event_report.table_amount') }}</th>
    </tr>
    @foreach($incomes as $item)
        <tr>
            <td>{{ $item->transaction->label }}</td>
            <td>{{ $item->transaction->reference }}</td>
            <td>{{ $item->transaction->status }}</td>
            <td>{{ $item->transaction->account->name }}</td>
            <td align="right">{{ \App\Helpers\MoneyHelper::formatCents($item->transaction->amount_gross) }}</td>

        </tr>

    @endforeach

</table>


<h2>{{ __('pdf.event_report.expenses_detail_header') }}</h2>

<table cellpadding="3">
    <tr>
        <th>{{ __('pdf.event_report.table_text') }}</th>
        <th>{{ __('pdf.event_report.table_reference') }}</th>
        <th>{{ __('pdf.event_report.table_status') }}</th>
        <th>{{ __('pdf.event_report.table_account') }}</th>
        <th align="right">{{ __('pdf.event_report.table_amount') }}</th>
    </tr>
    @foreach($spendings as $item)
        <tr>
            <td>{{ $item->transaction->label }}</td>
            <td>{{ $item->transaction->reference }}</td>
            <td>{{ $item->transaction->status }}</td>
            <td>{{ $item->transaction->account->name }}</td>
            <td align="right">{{ \App\Helpers\MoneyHelper::formatCents($item->transaction->amount_gross) }}</td>
        </tr>

    @endforeach
</table>

<h2>{{ __('pdf.event_report.visitors_detail_header') }}</h2>

<table cellpadding="3">
    <tr>
        <th>{{ __('pdf.event_report.table_name') }}</th>
        <th>{{ __('pdf.event_report.table_email') }}</th>
        <th align="center">{{ __('pdf.event_report.table_member') }}</th>
        <th align="center">{{ __('pdf.event_report.table_subscribed') }}</th>
        <th align="center">{{ __('pdf.event_report.table_male') }}</th>
        <th align="center">{{ __('pdf.event_report.table_female') }}</th>
    </tr>

    @foreach($visitors as $visitor)
        <tr>
            <td>{{ $visitor->name }}</td>
            <td>{{ $visitor->email }}</td>
            <td align="center">{{ $visitor->member ? 'x' : '' }}</td>
            <td align="center">{{ $visitor->subscription ? 'x' : '' }}</td>
            <td align="center">{{ $visitor->gender === Gender::ma ? 'x' : '' }}</td>
            <td align="center">{{ $visitor->gender === Gender::fe ? 'x' : '' }}</td>
        </tr>
    @endforeach
    <tfoot>
    <tr>
        <td style="font-size: 8pt; border-top: 1px slategray solid"
            colspan="6"
            align="right"
        >{{ __('pdf.event_report.legend_member') }} <br> {{ __('pdf.event_report.legend_subscribed') }} <br> {{ __('pdf.event_report.legend_male') }} <br> {{ __('pdf.event_report.legend_female') }}
        </td>
    </tr>
    </tfoot>
</table>


</body>
</html>
