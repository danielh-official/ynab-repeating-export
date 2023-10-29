<x-layout>
    @isset($error)
        <div class="absolute top-0 left-0 right-0 p-4 bg-red-500 text-white">
            {{ $error }}
        </div>
    @endif

    @if($access_token)
        <form method="POST" action="{{ route('export') }}" class="space-y-4">
            @csrf

            <div class="flex flex-col space-y-2">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input name="only_retrieve_changed_records" id="only_retrieve_changed_records" type="checkbox" class="sr-only peer">
                    <div
                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Only Retrieve Changed Records</span>
                </label>
                <div class="text-sm text-gray-500 dark:text-gray-400"><p>Only retrieve transactions that have been changed since the last export.</p><p>This option is faster since less data is getting parsed.</p></div>
            </div>

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
        <a href="{{ $auth_url }}">Authenticate YNAB</a>
    @endif

    <div class="mt-8 flex flex-col space-y-2 text-center">
        <div>
            <a class="font-medium text-blue-600 dark:text-blue-500 hover:underline" href="{{ route('privacy') }}">Privacy
                Policy</a>
        </div>
        <div>
            <a class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
               href="{{ route('guide') }}">Guide</a>
        </div>
    </div>
</x-layout>
