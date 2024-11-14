<?php

namespace App\Exports;

use App\Enums\YnabAcceptedFrequency;
use Illuminate\Support\Carbon;
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
    ) {
        $this->parseTransactions();
    }

    public function collection(): Collection
    {
        return $this->data;
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

    private function parseTransactions(): void
    {
        $data = collect();

        foreach ($this->scheduledTransactions as $scheduledTransaction) {
            $subtransactions = data_get($scheduledTransaction, 'subtransactions', []);

            foreach ($subtransactions as $subtransaction) {
                $item = $this->parseTransaction($subtransaction, $scheduledTransaction);

                if ($item) {
                    $data->push($item);
                }
            }

            if (! $subtransactions) {
                $item = $this->parseTransaction($scheduledTransaction);

                if ($item) {
                    $data->push($item);
                }
            }
        }

        $this->data = $data;
    }

    private function parseTransaction(array $transaction, ?array $parentTransaction = null): ?array
    {
        if (data_get($transaction, 'deleted', false)) {
            return null;
        }

        if (data_get($transaction, 'amount')) {
            $amount = data_get($transaction, 'amount') / 1000;
        } else {
            return null;
        }

        if (data_get($transaction, 'frequency')) {
            $frequency = YnabAcceptedFrequency::tryFrom(data_get($transaction, 'frequency'));
        } elseif ($parentTransaction) {
            $frequency = YnabAcceptedFrequency::tryFrom(data_get($parentTransaction, 'frequency'));
        } else {
            return null;
        }

        if ($frequency === null) {
            return null;
        }

        $account = $this->accounts->filter(fn ($item) => $this->filterByNotDeleted($item))->firstWhere('id', data_get($transaction, 'account_id'));

        if ($parentTransaction) {
            $account = $this->accounts->filter(fn ($item) => $this->filterByNotDeleted($item))->firstWhere('id', data_get($parentTransaction, 'account_id'));
        }

        $payee = $this->payees->filter(fn ($item) => $this->filterByNotDeleted($item))->firstWhere('id', data_get($transaction, 'payee_id'));

        $category = $this->categories->filter(fn ($item) => $this->filterByNotDeleted($item))->firstWhere('id', data_get($transaction, 'category_id'));

        $transferAccount = $this->accounts->filter(fn ($item) => $this->filterByNotDeleted($item))->firstWhere(
            'id',
            data_get($transaction, 'transfer_account_id')
        );

        $parentPayeeName = null;

        if ($parentTransaction) {
            $parentPayee = $this->payees->filter(fn ($item) => $this->filterByNotDeleted($item))->firstWhere('id', data_get($parentTransaction, 'payee_id'));

            $parentPayeeName = data_get($parentPayee, 'name');
        }

        $amountPerWeek =
            YnabAcceptedFrequency::convertAmountFromFrequencyToFrequency(
                $amount,
                $frequency,
                YnabAcceptedFrequency::weekly
            );

        $amountPerMonth =
            YnabAcceptedFrequency::convertAmountFromFrequencyToFrequency(
                $amount,
                $frequency,
                YnabAcceptedFrequency::monthly
            );

        $amountPerYear =
            YnabAcceptedFrequency::convertAmountFromFrequencyToFrequency(
                $amount,
                $frequency,
                YnabAcceptedFrequency::yearly
            );

        $dateFirst = $parentTransaction ? data_get($parentTransaction, 'date_first') : data_get($transaction, 'date_first');

        $dateNext = $parentTransaction ? data_get($parentTransaction, 'date_next') : data_get($transaction, 'date_next');

        $flagColor = $parentTransaction ? data_get($parentTransaction, 'flag_color') : data_get($transaction, 'flag_color');

        $parentMemo = $parentTransaction ? data_get($parentTransaction, 'memo') : null;

        return [
            'date_first' => $dateFirst ? Carbon::parse($dateFirst)->format('Y-m-d') : null,
            'date_next' => $dateNext ? Carbon::parse($dateNext)->format('Y-m-d') : null,
            'frequency' => $frequency->value,
            'raw_amount' => $amount,
            'amount' => abs($amount),
            'inflow_outflow' => $amount < 0 ? 'outflow' : 'inflow',
            'parent_memo' => $parentMemo,
            'memo' => data_get($transaction, 'memo'),
            'flag_color' => $flagColor,
            'account_name' => data_get($account, 'name'),
            'payee_name' => data_get($payee, 'name'),
            'parent_payee_name' => $parentPayeeName,
            'category_name' => data_get($category, 'name'),
            'category_group_name' => data_get($category, 'category_group_name'),
            'transfer_account_name' => data_get($transferAccount, 'name'),
            'raw_amount_per_week' => $amountPerWeek,
            'raw_amount_per_month' => $amountPerMonth,
            'raw_amount_per_year' => $amountPerYear,
            'amount_per_week' => abs($amountPerWeek),
            'amount_per_month' => abs($amountPerMonth),
            'amount_per_year' => abs($amountPerYear),
        ];
    }

    private function filterByNotDeleted(array $item): bool
    {
        return ! data_get($item, 'deleted') ?? true;
    }
}
