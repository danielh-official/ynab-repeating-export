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

            $subtransactions = data_get($scheduledTransaction, 'subtransactions');

            if ($subtransactions) {
                foreach ($subtransactions as $subtransaction) {
                    $subtransactionCategoryId = data_get($subtransaction, 'category_id');
                    $subtransactionCategory = $this->categories->firstWhere('id', $subtransactionCategoryId);

                    $subtransactionTransferAccountId = data_get($subtransaction, 'transfer_account_id');
                    $subtransactionTransferAccount = $this->accounts->firstWhere('id', $subtransactionTransferAccountId);

                    $subtransactionPayeeId = data_get($subtransaction, 'payee_id');
                    $subtransactionPayee = $this->payees->firstWhere('id', $subtransactionPayeeId);

                    $data->push([
                        'deleted' => data_get($scheduledTransaction, 'deleted'),
                        'frequency' => data_get($scheduledTransaction, 'frequency'),
                        'date_first' => data_get($scheduledTransaction, 'date_first'),
                        'date_next' => data_get($scheduledTransaction, 'date_next'),
                        'amount' => data_get($subtransaction, 'amount'),
                        'parent_memo' => data_get($scheduledTransaction, 'memo'),
                        'memo' => data_get($subtransaction, 'memo'),
                        'flag_color' => data_get($scheduledTransaction, 'flag_color'),
                        'account' => $account,
                        'payee' => $subtransactionPayee,
                        'category' => $subtransactionCategory,
                        'transfer_account' => $subtransactionTransferAccount,
                        'parent_payee' => $payee,
                    ]);
                }
            } else {
                $data->push([
                    'deleted' => data_get($scheduledTransaction, 'deleted'),
                    'frequency' => data_get($scheduledTransaction, 'frequency'),
                    'date_first' => data_get($scheduledTransaction, 'date_first'),
                    'date_next' => data_get($scheduledTransaction, 'date_next'),
                    'amount' => data_get($scheduledTransaction, 'amount'),
                    'parent_memo' => null,
                    'memo' => data_get($scheduledTransaction, 'memo'),
                    'flag_color' => data_get($scheduledTransaction, 'flag_color'),
                    'account' => $account,
                    'payee' => $payee,
                    'category' => $category,
                    'transfer_account' => $transferAccount,
                    'parent_payee' => null,
                ]);
            }
        }

        return $data;
    }
}
