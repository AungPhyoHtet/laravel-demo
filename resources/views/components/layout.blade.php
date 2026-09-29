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

<body class="flex min-h-screen flex-col">
    <x-nav />
    <main class="mx-auto flex w-full max-w-3xl flex-1 flex-col px-4 py-6">
        {{ $slot }}
    </main>
    <x-ui.flash />
</body>

</html>
