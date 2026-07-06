<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | InvoiceFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <main class="mx-auto flex min-h-screen max-w-4xl items-center px-6 py-16">
        <section class="w-full rounded-lg border border-slate-800 bg-slate-900/70 p-8 shadow-2xl shadow-slate-950/30">
            <p class="text-sm font-medium uppercase tracking-[0.2em] text-sky-400">InvoiceFlow</p>
            <h1 class="mt-4 text-3xl font-semibold text-white">{{ $title }}</h1>
            <p class="mt-4 max-w-2xl text-base leading-7 text-slate-300">{{ $summary }}</p>
        </section>
    </main>
</body>
</html>
