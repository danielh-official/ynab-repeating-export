<x-layout>
    <div class="sm:max-w-screen-sm px-10">
        <a class="font-medium text-blue-600 dark:text-blue-500 hover:underline" href="{{ route('home') }}">Go
            Back</a>
        <h1 class="text-4xl mt-4 mb-4">Privacy Policy</h1>
        <p class="mt-2">{{ config('app.name') }} does not store any of your data on the server or database. It only uses
            your data to export it to a file.</p>
        <p class="mt-2">The access token is stored on your browser as an encrypted session cookie, which is set to expire every
            2
            hours (as per YNAB's token expiration policy).</p>
        <p class="mt-2 font-bold">Your data will <span class="uppercase">not</span> unknowingly be passed to any
            third-party.</p>
    </div>
</x-layout>
