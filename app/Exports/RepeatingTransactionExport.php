<?php

namespace App\Exports;

use App\Enums\YnabAcceptedFrequency;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RepeatingTransactionExport implements FromCollection, WithHeadings
{
    use Exportable;

    private Collection $data;

    public function __construct(
        private readonly Collection $scheduledTransactions,
        private readonly Collection $accounts,
        private readonly Collection $payees,
        private readonly Collection $categories,
    )
    {
        $this->mergeLiveData();
    }

    private function mergeLiveData(): void
    {
        $data = collect();

        foreach ($this->scheduledTransactions as $scheduledTransaction) {
            $accountId = data_get($scheduledTransaction, 'account_id');
            $account = $this->accounts->firstWhere('id', $accountId);

            $payeeId = data_get($scheduledTransaction, 'payee_id');
            $payee = $this->payees->firstWhere('id', $payeeId);

            $categoryId = data_get($scheduledTransaction, 'category_id');
            $category = $this->categories->firstWhere('id', $categoryId);

            $transferAccountId = data_get($scheduledTransaction, 'transfer_account_id');
            $transferAccount = $this->accounts->firstWhere('id', $transferAccountId);

            $data->push([
                'deleted' => data_get($scheduledTransaction, 'deleted'),
                'frequency' => data_get($scheduledTransaction, 'frequency'),
                'date_first' => data_get($scheduledTransaction, 'date_first'),
                'date_next' => data_get($scheduledTransaction, 'date_next'),
                'amount' => data_get($scheduledTransaction, 'amount'),
                'memo' => data_get($scheduledTransaction, 'memo'),
                'flag_color' => data_get($scheduledTransaction, 'flag_color'),
                'account' => $account,
                'payee' => $payee,
                'category' => $category,
                'transfer_account' => $transferAccount,
            ]);
        }

        $this->data = $data;
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
