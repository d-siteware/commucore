<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1"
    >

    <title>{{ __('members.apply.print.title') }}</title>

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
    </style>
</head>
<body style="font-size: 10pt">
<br>
<br>
<br>
<p><span style="font-size: 8pt">Atn:</span>
    <br>{{ setting('orgnanization.name') }}
    <br>{{ setting('orgnanization.address') }} <br>{{ setting('orgnanization.zip') }} {{ setting('orgnanization.city') }}</p>

<br><br>

<h3>{{ __('members.apply.print.title') }}</h3>

<br><br>
<p>{{ __('members.apply.print.greeting') }}</p>

<p>{{ __('members.apply.print.text') }}</p>
<br><br><br><br>

<p style="font-size: 11pt;">{{ __('members.apply.print.overview.person') }}</p>


<table cellpadding="2"
       cellspacing="0"
       border="0"
>
    <tr>
        <th>{{ __('members.name') }}, {{ __('members.first_name') }}</th>
        <td>{{ $member->name }}, {{ $member->first_name }}</td>
    </tr>
    <tr>
        <th>{{ __('members.birth_date') }}</th>
        <td>{{ $member->birth_date??'-' }} </td>
    </tr>
    <tr>
        <th>{{ __('members.gender') }}</th>
        <td>{{ $member->gender->label()  }}</td>
    </tr>
    <tr>
        <th>{{ __('members.locale') }}</th>
        @if($member->locale)
            <td>{{  $member->locale::getLabel() }}</td>
        @else
            <td>{{ app()->getLocale()  }}</td>
        @endif
    </tr>
</table>
<br>
<p style="font-size: 11pt;">{{ __('members.apply.print.overview.contact') }}</p>
<br>
<table cellpadding="2"
       cellspacing="0"
       border="0"
>
    <tr>
        <th>{{ __('members.address') }}</th>
        <td>{{ $member->address??'-' }}</td>
    </tr>
    <tr>
        <th>{{ __('members.city') }}</th>
        <td>{{ $member->city??'-' }}</td>
    </tr>
    @if($member->country !== 'Deutschland')
        <tr>
            <th>{{ __('members.country') }}</th>
            <td>{{ $member->country??'-'}} </td>
        </tr>
    @endif
    <tr>
        <th>{{ __('members.phone') }}</th>
        <td>{{ $member->phone??'-'  }}</td>
    </tr>
    <tr>
        <th>{{ __('members.mobile') }}</th>
        <td>{{ $member->mobile??'-'  }}</td>
    </tr>
    <tr>
        <th>{{ __('members.email') }}</th>
        <td>{{ $member->email??'-'  }} </td>
    </tr>
</table>


@if($member->is_deducted)

    <br><br>
    <p>{{ __('members.apply.discount.label') }}</p>
    <table cellpadding="2"
           cellspacing="0"
           border="0"
    >
        <tr>
            <th>{{ __('members.apply.discount.reason.label') }} </th>
            <td>{{ $member->deduction_reason??'-' }}</td>
        </tr>
    </table>
    <br><br>

@else
    <br><br>
    <p style="font-size: 12pt; font-weight: bold;">{{ __('members.apply.fee.text', ['sum' => $member->fee_type->fee() ]) }}</p>
@endif

<p>{{ __('members.apply.print.regards') }}<br><span style="font-size: 11pt;">{{ $member->name }}, {{ $member->first_name }}</span>
</p>

<table cellpadding="2"
       cellspacing="0"
       border="0"
       width="30%"
>
    <tr>
        <td>
            <br><br><br><br><br>
            <p style="font-size: 9pt; border-top: 1px solid #777777">{{ $member->city }} {{ date('Y-m-d') }}</p>

        </td>
    </tr>
</table>


</body>
</html>
