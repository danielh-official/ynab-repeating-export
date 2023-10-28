<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet"/>

    <!-- Styles -->
    @vite('resources/css/app.css')
</head>
<body class="antialiased">
<div
    class="relative flex justify-center items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">

    @isset($error)
        <div class="absolute top-0 left-0 right-0 p-4 bg-red-500 text-white">
            {{ $error }}
        </div>
    @endif

    @if($access_token)
        <form method="POST" action="{{ route('export') }}" class="space-y-2">
            @csrf

            <div>
                <label>
                    Select a File Extension
                    <select name="file_extension" id="file_extension">
                        <option value="csv">CSV</option>
                        <option value="excel">Excel</option>
                    </select>
                </label>
            </div>
            <div>
                <button
                    class="inline-block px-12 py-3 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded active:text-blue-500 hover:bg-blue-700 focus:outline-none focus:ring"
                    type="submit">Export Transactions
                </button>
            </div>
        </form>
    @else
        <a href="{{ $auth_url }}">Authenticate YNAB</a>
    @endif
</div>
</body>
</html>
