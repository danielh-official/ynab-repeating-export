<x-layout>
    @isset($error)
        <div class="absolute top-0 left-0 right-0 p-4 bg-red-500 text-white">
            {{ $error }}
        </div>
    @endif

    @if($access_token)
        <form method="POST" action="{{ route('export') }}" class="space-y-4">
            @csrf

            <div>
                <label for="file_extension" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Select a File Extension
                </label>
                <select
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    name="file_extension" id="file_extension">
                    <option value="csv">CSV</option>
                    <option value="excel">Excel</option>
                </select>
            </div>
            <div>
                <button
                    class="inline-block px-12 py-3 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded active:text-blue-500 hover:bg-blue-700 focus:outline-none focus:ring"
                    type="submit">Export Transactions
                </button>
            </div>
        </form>
    @else
        <a class="inline-block px-12 py-3 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded active:text-blue-500 hover:bg-blue-700 focus:outline-none focus:ring"
           href="{{ $auth_url }}">Authenticate YNAB</a>
    @endif

    <div class="mt-4 flex flex-col space-y-2 text-center justify-center">
        <button
            class="inline-block rounded bg-primary px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#3b71ca] transition duration-150 ease-in-out hover:bg-primary-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:bg-primary-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] dark:shadow-[0_4px_9px_-4px_rgba(59,113,202,0.5)] dark:hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)] dark:active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.2),0_4px_18px_0_rgba(59,113,202,0.1)]"
            type="button"
            data-te-collapse-init
            data-te-ripple-init
            data-te-ripple-color="light"
            data-te-target="#collapseExample"
            aria-expanded="false"
            aria-controls="collapseExample">
            Want to test the export before connecting to YNAB?
        </button>
        <div class="!visible hidden" id="collapseExample" data-te-collapse-item>
            <div
                class="block rounded-lg bg-white p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] dark:bg-neutral-700 dark:text-neutral-50">
                <x-sample-export></x-sample-export>
            </div>
        </div>

        <hr class="border-gray-300 dark:border-gray-700">

        <x-links></x-links>
    </div>
</x-layout>
