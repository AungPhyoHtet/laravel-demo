<x-mail::message>
# Your idea has been published

{{ $idea->description }}

<x-mail::button :url="$url">
View Idea
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
