<x-mail::message>
# Your profile has been updated

The following details on your account were changed:

@foreach ($changedFieldLabels as $label)
- {{ $label }}
@endforeach

If you did not make this change, please secure your account immediately.

<x-mail::button :url="$url">
View Profile
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
