<x-mail::message>
# {{ $mailDetail['Subject'] }}

{{ $mailDetails['text'] }}

<x-mail::button :url="''">
{{ token }}
</x-mail::button>
{{ $mailDetails['toeknWarning'] }}
Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
