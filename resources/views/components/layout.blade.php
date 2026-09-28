@props([
    'title' => 'Laravel Demo',
])

<!DOCTYPE html>
<html lang="en" data-theme="dracula">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <x-nav />
    <main class="mx-auto max-w-3xl px-4 py-6">
        {{ $slot }}
    </main>
</body>

</html>
