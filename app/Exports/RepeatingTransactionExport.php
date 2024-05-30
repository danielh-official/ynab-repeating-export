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
    ) {
        $this->ynabScheduledTransactionService = new YnabScheduledTransactionService(
            new \Illuminate\Database\Eloquent\Collection($scheduledTransactions),
            new \Illuminate\Database\Eloquent\Collection($accounts),
            new \Illuminate\Database\Eloquent\Collection($payees),
            new \Illuminate\Database\Eloquent\Collection($categories),
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
            if (data_get($transaction, 'deleted')) {
                continue;
            }

            $frequency = YnabAcceptedFrequency::tryFrom(data_get($transaction, 'frequency'));

            if (empty($frequency)) {
                continue;
            }

            $amount = data_get($transaction, 'amount');

            if ($amount) {
                $amount = $amount / 1000;
            }

            $data->push([
                'date_first' => Carbon::parse(data_get($transaction, 'date_first'))->format('Y-m-d'),
                'date_next' => Carbon::parse(data_get($transaction, 'date_next'))->format('Y-m-d'),
                'frequency' => $frequency->value,
                'raw_amount' => $amount,
                'amount' => abs($amount),
                'inflow_outflow' => $amount < 0 ? 'outflow' : 'inflow',
                'parent_memo' => data_get($transaction, 'parent_memo'),
                'memo' => data_get($transaction, 'memo'),
                'flag_color' => data_get($transaction, 'flag_color'),
                'account_name' => data_get($transaction, 'account.name'),
                'payee_name' => data_get($transaction, 'payee.name'),
                'parent_payee_name' => data_get($transaction, 'parent_payee.name'),
                'category_name' => data_get($transaction, 'category.name'),
                'category_group_name' => data_get($transaction, 'category.category_group_name'),
                'transfer_account_name' => data_get($transaction, 'transfer_account.name'),
                'raw_amount_per_week' => $amountPerWeek =
                    YnabAcceptedFrequency::convertAmountFromFrequencyToFrequency(
                        $amount,
                        $frequency,
                        YnabAcceptedFrequency::weekly
                    ),
                'raw_amount_per_month' => $amountPerMonth =
                    YnabAcceptedFrequency::convertAmountFromFrequencyToFrequency(
                        $amount,
                        $frequency,
                        YnabAcceptedFrequency::monthly
                    ),
                'raw_amount_per_year' => $amountPerYear =
                    YnabAcceptedFrequency::convertAmountFromFrequencyToFrequency(
                        $amount,
                        $frequency,
                        YnabAcceptedFrequency::yearly
                    ),
                'amount_per_week' => abs($amountPerWeek),
                'amount_per_month' => abs($amountPerMonth),
                'amount_per_year' => abs($amountPerYear),
            ]);
        }

        return $data;
    }

    public function collection(): Collection
    {
        return $this->parseLiveData();
    }

    /**
     * @return string[]
     *
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
            'Parent Memo',
            'Memo',
            'Flag Color',
            'Account Name',
            'Payee Name',
            'Parent Payee Name',
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
