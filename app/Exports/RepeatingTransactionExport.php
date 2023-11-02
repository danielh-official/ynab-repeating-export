<?php

namespace App\Exports;

use App\Enums\YnabAcceptedFrequency;
use App\Services\YnabScheduledTransactionService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RepeatingTransactionExport implements FromCollection, WithHeadings
{
    use Exportable;

    private Collection $data;

    protected readonly YnabScheduledTransactionService $ynabScheduledTransactionService;

    public function __construct(
        private readonly Collection $scheduledTransactions,
        private readonly Collection $accounts,
        private readonly Collection $payees,
        private readonly Collection $categories,
    )
    {
        $scheduledTransactions = new \Illuminate\Database\Eloquent\Collection($scheduledTransactions);
        $accounts = new \Illuminate\Database\Eloquent\Collection($accounts);
        $payees = new \Illuminate\Database\Eloquent\Collection($payees);
        $categories = new \Illuminate\Database\Eloquent\Collection($categories);

        $this->ynabScheduledTransactionService = new YnabScheduledTransactionService(
            $scheduledTransactions,
            $accounts,
            $payees,
            $categories,
        );

        $this->mergeLiveData();
    }

    private function mergeLiveData(): void
    {
        $this->data = $this->ynabScheduledTransactionService->merge();
    }

    private function parseLiveData(): Collection
    {
        $data = collect();

        foreach ($this->data as $transaction) {
            $isDeleted = data_get($transaction, 'deleted');

            if ($isDeleted) {
                continue;
            }

            $frequency = data_get($transaction, 'frequency');

            $frequency = YnabAcceptedFrequency::tryFrom($frequency);

            $notValidFrequency = empty($frequency);

            if ($notValidFrequency) {
                continue;
            }

            $dateFirst = data_get($transaction, 'date_first');
            $dateFirst = Carbon::parse($dateFirst);

            $dateNext = data_get($transaction, 'date_next');
            $dateNext = Carbon::parse($dateNext);
            $amount = data_get($transaction, 'amount');

            if ($amount) {
                $amount = $amount / 1000;
            }

            $memo = data_get($transaction, 'memo');
            $flagColor = data_get($transaction, 'flag_color');
            $accountName = data_get($transaction, 'account.name');
            $transferAccountName = data_get($transaction, 'transfer_account.name');
            $payeeName = data_get($transaction, 'payee.name');
            $categoryName = data_get($transaction, 'category.name');
            $categoryGroupName = data_get($transaction, 'category.category_group_name');

            $data->push([
                'date_first' => $dateFirst->format('Y-m-d'),
                'date_next' => $dateNext->format('Y-m-d'),
                'frequency' => $frequency->value,
                'raw_amount' => $amount,
                'amount' => abs($amount),
                'inflow_outflow' => $amount < 0 ? 'outflow' : 'inflow',
                'memo' => $memo,
                'flag_color' => $flagColor,
                'account_name' => $accountName,
                'payee_name' => $payeeName,
                'category_name' => $categoryName,
                'category_group_name' => $categoryGroupName,
                'transfer_account_name' => $transferAccountName,
                'raw_amount_per_week' => $amountPerWeek = YnabAcceptedFrequency::convertAmountFromFrequencyToFrequency($amount, $frequency, YnabAcceptedFrequency::weekly),
                'raw_amount_per_month' => $amountPerMonth = YnabAcceptedFrequency::convertAmountFromFrequencyToFrequency($amount, $frequency, YnabAcceptedFrequency::monthly),
                'raw_amount_per_year' => $amountPerYear = YnabAcceptedFrequency::convertAmountFromFrequencyToFrequency($amount, $frequency, YnabAcceptedFrequency::yearly),
                'amount_per_week' => abs($amountPerWeek),
                'amount_per_month' => abs($amountPerMonth),
                'amount_per_year' => abs($amountPerYear),
            ]);
        }

        return $data;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return $this->parseLiveData();
    }

    /**
     * @return string[]
     * @codeCoverageIgnore
     */
    public function headings(): array
    {
        return [
            'Date First',
            'Date Next',
            'Frequency',
            'Raw Amount',
            'Amount',
            'Inflow/Outflow',
            'Memo',
            'Flag Color',
            'Account Name',
            'Payee Name',
            'Category Name',
            'Category Group Name',
            'Transfer Account Name',
            'Raw Amount Per Week',
            'Raw Amount Per Month',
            'Raw Amount Per Year',
            'Amount Per Week',
            'Amount Per Month',
            'Amount Per Year',
        ];
    }
}
