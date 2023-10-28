<x-layout>
    <div class="sm:max-w-screen-sm px-10">
        <a class="font-medium text-blue-600 dark:text-blue-500 hover:underline" href="{{ route('home') }}">Go Back</a>
        <h1 class="text-4xl mb-4 mt-4">Guide</h1>
        <h2 class="text-2xl mb-2">Column Definitions</h2>
        <ul class="list-disc">
            <li class="list-item"><b class="font-bold">Date First</b>: The first date the repeating transaction was
                created.
            </li>
            <li class="list-item"><b class="font-bold">Date Next</b>: The next date the repeating transaction will show.
            </li>
            <li class="list-item"><b class="font-bold">Frequency</b>: The frequency by which the transaction repeats.
            </li>
            <li class="list-item"><b class="font-bold">Raw Amount (Per Week/Month/Year)</b>: The amount as represented
                by
                negative for outflow and positive for inflow. Per week, month, or year columns show the amount converted
                to
                that frequency.
            </li>
            <li class="list-item"><b class="font-bold">Amount (Per Week/Month/Year)</b>: The same as Raw Amount, except
                it uses the absolute value (e.g., -10 becomes 10). You can tell whether it's inflow or outflow by
                looking at the "Inflow/Outflow" column.
            </li>
            <li class="list-item"><b class="font-bold">Inflow/Outflow</b>: States whether or not the transaction is an
                inflow or outflow.
            </li>
            <li class="list-item"><b class="font-bold">Payee Name</b>: The payee of the transaction.</li>
            <li class="list-item"><b class="font-bold">Category Name</b>: The category of the transaction.</li>
            <li class="list-item"><b class="font-bold">Memo</b>: The memo of the transaction.</li>
            <li class="list-item"><b class="font-bold">Account Name</b>: The account of the transaction.</li>
            <li class="list-item"><b class="font-bold">Flag Color</b>: The flag color of the transaction.</li>
            <li class="list-item"><b class="font-bold">Transfer Account Name</b>: If the transaction is a transfer, the
                transfer account name will be included. Otherwise, this field is empty.
            </li>
        </ul>
    </div>
</x-layout>
