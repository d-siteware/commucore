@php use App\Enums\Gender; @endphp
        <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1"
    >

    <title>Report</title>

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
<h1>Event-Report: {{ $event->title['de'] }}</h1>

<h2>Zusammenfassung</h2>

<h3>Finanzen</h3>
<table width="60%">
    <tr>
        <th>Einnahmen:</th>
        <td align="right">EUR {{  number_format($income,'2',',','.') }}</td>
    </tr>
    <tr>
        <th>Ausgaben:</th>
        <td align="right">EUR {{  number_format($spending,'2',',','.') }}</td>
    </tr>
    <tr>
        <th style="border-top: 1px solid #888; font-size: 12pt;">Gesamt:</th>
        <td style="border-top: 1px solid #888; font-size: 12pt;"
            align="right"
        >EUR {{ number_format($income - $spending,'2',',','.')  }}</td>
    </tr>
</table>


<h3>Besucher</h3>

<table width="60%">
    <tr>
        <th>Gesamtzahl der erfassten Bescher:</th>
        <td align="right">{{ $visitors->count() }}</td>
    </tr>
    <tr>
        <th>Gesamt männlich:</th>
        <td align="right">{{ $visitors->count() }}</td>
    </tr>
    <tr>
        <th>Gesamt weiblich:</th>
        <td align="right">{{ $visitors->count() }}</td>
    </tr>
</table>
<br>
<table width="60%">
    <tr>
        <th>Mitglieder</th>
        <td align="right">{{ $visitors->count() }}</td>
    </tr>
    <tr>
        <th>Über die Webseite angemeldet</th>
        <td align="right">{{ $visitors->count() }}</td>
    </tr>
</table>

<p style="page-break-after: right; "></p>
<br><br>


<h1>Details</h1>

<h2>Einnahmen</h2>

<table cellpadding="3">
    <tr>
        <th>Text</th>
        <th>Referenz</th>
        <th>Status</th>
        <th>Konto</th>
        <th align="right">Betrag</th>
    </tr>
    @foreach($incomes as $item)
        <tr>
            <td>{{ $item->transaction->label }}</td>
            <td>{{ $item->transaction->reference }}</td>
            <td>{{ $item->transaction->status }}</td>
            <td>{{ $item->transaction->account->name }}</td>
            <td align="right">{{ number_format($item->transaction->amount_gross/100,'2',',','.') }}</td>

        </tr>

    @endforeach

</table>


<h2>Ausgaben</h2>

<table cellpadding="3">
    <tr>
        <th>Text</th>
        <th>Referenz</th>
        <th>Status</th>
        <th>Konto</th>
        <th align="right">Betrag</th>
    </tr>
    @foreach($spendings as $item)
        <tr>
            <td>{{ $item->transaction->label }}</td>
            <td>{{ $item->transaction->reference }}</td>
            <td>{{ $item->transaction->status }}</td>
            <td>{{ $item->transaction->account->name }}</td>
            <td align="right">{{ number_format($item->transaction->amount_gross/100,'2',',','.') }}</td>
        </tr>

    @endforeach
</table>

<h2>Besucher</h2>

<table cellpadding="3">
    <tr>
        <th>Name</th>
        <th>E-Mail</th>
        <th align="center">M</th>
        <th align="center">A</th>
        <th align="center">M</th>
        <th align="center">W</th>
    </tr>

    @foreach($visitors as $visitor)
        <tr>
            <td>{{ $visitor->name }}</td>
            <td>{{ $visitor->email }}</td>
            <td align="center">{{ $visitor->member ? 'x' : '' }}</td>
            <td align="center">{{ $visitor->subscription ? 'x' : '' }}</td>
            <td align="center">{{ $visitor->gender === Gender::ma->value ? 'x' : '' }}</td>
            <td align="center">{{ $visitor->gender === Gender::fe->value ? 'x' : '' }}</td>
        </tr>
    @endforeach
    <tfoot>
    <tr>
        <td style="font-size: 8pt; border-top: 1px slategray solid"
            colspan="6"
            align="right"
        >M: Besucher ist Mitglied <br> A: Besuche hat sich angemeldet <br> M: Besucher ist männlich <br> W: Besucher ist weiblich
        </td>
    </tr>
    </tfoot>
</table>


</body>
</html>
