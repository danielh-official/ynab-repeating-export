<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;

class YnabScheduledTransactionService
{
    public function __construct(
        private readonly Collection $scheduledTransactions,
        private readonly Collection $accounts,
        private readonly Collection $payees,
        private readonly Collection $categories,
    )
    {
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function merge(): \Illuminate\Support\Collection
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

        return $data;
    }
}
