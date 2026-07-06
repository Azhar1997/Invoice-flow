<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>InvoiceFlow</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
        <main class="mx-auto flex min-h-screen max-w-7xl flex-col px-6 py-8 lg:px-10">
            <header class="flex items-center justify-between border-b border-slate-800 pb-6">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-500/15 text-sm font-semibold text-sky-300">IF</span>
                    <span class="text-lg font-semibold text-white">InvoiceFlow</span>
                </a>

                <nav class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-md border border-slate-700 px-4 py-2 text-sm text-slate-200 transition hover:border-sky-400 hover:text-white">
                            Open Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-md border border-slate-700 px-4 py-2 text-sm text-slate-200 transition hover:border-sky-400 hover:text-white">
                            Log In
                        </a>
                        <a href="{{ route('register') }}" class="rounded-md bg-sky-500 px-4 py-2 text-sm font-medium text-slate-950 transition hover:bg-sky-400">
                            Create Account
                        </a>
                    @endauth
                </nav>
            </header>

            <section class="grid flex-1 items-center gap-12 py-16 lg:grid-cols-[1.1fr_0.9fr]">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.2em] text-sky-300">Freelance Invoicing Demo</p>
                    <h1 class="mt-6 max-w-3xl text-4xl font-semibold tracking-tight text-white sm:text-5xl">
                        Clean invoice and client workflows in a recruiter-friendly Laravel app.
                    </h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-300">
                        InvoiceFlow is a polished portfolio project for managing clients, drafting invoices, tracking payments, and showcasing modern Laravel architecture without turning into an oversized SaaS clone.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-md bg-sky-500 px-5 py-3 text-sm font-medium text-slate-950 transition hover:bg-sky-400">
                                Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="rounded-md bg-sky-500 px-5 py-3 text-sm font-medium text-slate-950 transition hover:bg-sky-400">
                                Get Started
                            </a>
                            <a href="{{ route('login') }}" class="rounded-md border border-slate-700 px-5 py-3 text-sm font-medium text-slate-100 transition hover:border-sky-400 hover:text-white">
                                Sign In
                            </a>
                        @endauth
                    </div>
                </div>

                <div class="grid gap-4">
                    <section class="rounded-lg border border-slate-800 bg-slate-900/70 p-6">
                        <h2 class="text-sm font-medium uppercase tracking-[0.18em] text-slate-400">Included Now</h2>
                        <ul class="mt-4 space-y-3 text-sm text-slate-200">
                            <li>Authentication with register, login, logout, and password recovery routes</li>
                            <li>Protected dashboard access that resolves normally</li>
                            <li>InvoiceFlow-branded entry page instead of the default Laravel starter screen</li>
                        </ul>
                    </section>

                    <section class="rounded-lg border border-slate-800 bg-slate-900/70 p-6">
                        <h2 class="text-sm font-medium uppercase tracking-[0.18em] text-slate-400">Next Build Layers</h2>
                        <ul class="mt-4 space-y-3 text-sm text-slate-200">
                            <li>Client CRUD and invoice editor on top of the existing schema</li>
                            <li>Queued invoice sending, PDF generation, and payment tracking</li>
                            <li>API resources, tests, and portfolio-ready documentation</li>
                        </ul>
                    </section>
                </div>
            </section>
        </main>
    </body>
</html>
