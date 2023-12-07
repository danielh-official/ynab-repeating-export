<form method="POST" action="{{ route('sample.export') }}" class="space-y-2">
    @csrf

    @isset($showQuestion)
        Want to test the export before connecting to YNAB?
    @endisset
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
            class="inline-block px-12 py-3 text-sm font-medium text-white bg-gray-600 border border-gray-600 rounded active:text-gray-300 hover:text-gray-300 focus:outline-none focus:ring"
            type="submit">Export Sample Transactions
        </button>
    </div>
</form>
