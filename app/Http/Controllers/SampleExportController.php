<?php

namespace App\Http\Controllers;

use App\Enums\YnabAcceptedFrequency;
use App\Exports\RepeatingTransactionExport;
use App\Services\YnabAccessTokenService;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SampleExportController extends ExportController
{
    protected Generator $faker;

    public function __construct(YnabAccessTokenService $ynabAccessTokenService)
    {
        parent::__construct($ynabAccessTokenService);

        $this->faker = Factory::create();
    }

    /**
     * @param Request $request
     * @param string $budgetId
     * @return Collection
     */
    protected function getScheduledTransactions(Request $request, string $budgetId = 'default')
    {
        $result = collect();

        $faker = $this->faker;

        foreach (range(1, 10) as $index) {
            /**
             * @var YnabAcceptedFrequency $randomFrequency
             */
            $randomFrequency = $faker->randomElement(YnabAcceptedFrequency::cases());

            $determineDaysAfterNumberFromFrequency = function (YnabAcceptedFrequency $frequency) {
                return match ($frequency) {
                    YnabAcceptedFrequency::weekly => 7,
                    YnabAcceptedFrequency::everyOtherWeek => 14,
                    YnabAcceptedFrequency::twiceAMonth => 15,
                    YnabAcceptedFrequency::every4Weeks => 28,
                    YnabAcceptedFrequency::monthly => 30,
                    YnabAcceptedFrequency::everyOtherMonth => 60,
                    YnabAcceptedFrequency::every3Months => 90,
                    YnabAcceptedFrequency::every4Months => 120,
                    YnabAcceptedFrequency::twiceAYear => 180,
                    YnabAcceptedFrequency::yearly => 365,
                    default => 1,
                };
            };

            $dateFirst = Carbon::parse($faker->date());

            $daysAfterDateFirst = $determineDaysAfterNumberFromFrequency($randomFrequency);

            $result->push([
                'date_first' => $dateFirst->format('Y-m-d'),
                'date_next' => $dateFirst->addDays($daysAfterDateFirst)->format('Y-m-d'),
                'frequency' => $randomFrequency->value,
                'amount' => $faker->boolean ? $faker->randomFloat(2, 1000, 10000000) : $faker->randomFloat(2, -10000000, -1000),
                'memo' => $faker->sentence(),
                'flag_color' => $faker->randomElement(['red', 'orange', 'yellow', 'green', 'blue', 'purple']),
                'account_id' => $index,
                'payee_id' => $index,
                'category_id' => $index,
            ]);
        }

        return $result;
    }

    /**
     * @param Request $request
     * @param string $budgetId
     * @return Collection
     */
    protected function getCategories(Request $request, string $budgetId = 'default')
    {
        $result = collect();

        $faker = $this->faker;

        foreach (range(1, 10) as $index) {
            $result->push([
                'id' => $index,
                'name' => $faker->randomElement(['Groceries', 'Rent', 'Utilities', 'Gas', 'Car Payment', 'Car Insurance', 'Internet', 'Phone', 'Entertainment', 'Dining Out', 'Clothing', 'Medical', 'Household Goods', 'Personal Care', 'Miscellaneous', 'Savings', 'Gifts', 'Vacation', 'Travel', 'Charity', 'Taxes', 'Home Improvement', 'Fees', 'Business Services', 'Education', 'Investments', 'Kids', 'Mortgage', 'Rent', 'Home Improvement', 'Home Insurance', 'Home Services', 'Home Goods', 'Furnishings', 'Electronics', 'Software', 'Books', 'Supplies', 'Music', 'Movies', 'Games', 'Hobbies', 'Sporting Goods', 'Gym', 'Subscriptions', 'Pets', 'Toys', 'Baby Supplies', 'Childcare', 'Gifts', 'Alcohol', 'Tobacco', 'Coffee', 'Restaurants', 'Fast Food', 'Parking', 'Public Transportation', 'Gas', 'Auto Payment', 'Auto Insurance', 'Auto Parts', 'Auto Services', 'Fines', 'Health Insurance', 'Doctor', 'Dentist', 'Pharmacy', 'Eyecare', 'Life Insurance', 'Income', 'Interest Income', 'Bonus', 'Gift', 'Refunds', 'Reimbursement', 'Rental Income', 'Returned Purchase', 'Savings', 'Cash', 'Investments', 'Other Income', 'Uncategorized']),
                'group' => $faker->randomElement(['Immediate Obligations', 'True Expenses', 'Debt Payments', 'Quality of Life Goals', 'Just for Fun', 'Giving', 'Savings Goals', 'Hidden Categories', 'Internal Master Category']),
            ]);
        }

        return $result;
    }

    /**
     * @param Request $request
     * @param string $budgetId
     * @return Collection
     */
    protected function getAccounts(Request $request, string $budgetId = 'default')
    {
        $result = collect();

        $faker = $this->faker;

        foreach (range(1, 10) as $index) {
            $result->push([
                'id' => $index,
                'name' => $faker->randomElement(['Checking', 'Savings', 'Cash', 'Credit Card', 'Line of Credit', 'Other Asset', 'Other Liability']),
                'type' => $faker->randomElement(['checking', 'savings', 'cash', 'creditCard', 'lineOfCredit', 'otherAsset', 'otherLiability']),
            ]);
        }

        return $result;
    }

    /**
     * @param Request $request
     * @param string $budgetId
     * @return Collection
     */
    protected function getPayees(Request $request, string $budgetId = 'default')
    {
        $result = collect();

        $faker = $this->faker;

        foreach (range(1, 10) as $index) {
            $result->push([
                'id' => $index,
                'name' => $faker->randomElement(['Google', 'Amazon', 'Netflix', 'Hulu', 'Spotify', 'Apple', 'Microsoft', 'Facebook', 'Twitter', 'Instagram', 'Twitch', 'YouTube', 'TikTok', 'Snapchat', 'Pinterest', 'Reddit', 'LinkedIn', 'PayPal', 'Venmo', 'Cash App', 'Robinhood', 'Coinbase', 'Chase', 'Bank of America', 'Wells Fargo', 'Citi', 'Capital One', 'US Bank', 'American Express', 'Discover', 'Synchrony', 'Barclays', 'TD Bank', 'PNC', 'Fidelity', 'Vanguard', 'Charles Schwab', 'Morgan Stanley', 'Goldman Sachs', 'Edward Jones', 'E-Trade', 'Ally', 'TIAA', 'Navy Federal', 'USAA', 'Walmart', 'Target', 'Costco', 'Sam\'s Club', 'BJ\'s', 'Kroger']),
            ]);
        }

        return $result;
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response|BinaryFileResponse|RedirectResponse
    {
        $fileName = $this->buildFileName($request);

        $scheduledTransactions = $this->getScheduledTransactions($request);

        $accounts = $this->getAccounts($request);

        $payees = $this->getPayees($request);

        $categories = $this->getCategories($request);

        return (new RepeatingTransactionExport(
            scheduledTransactions: $scheduledTransactions,
            accounts: $accounts,
            payees: $payees,
            categories: $categories,
        ))->download($fileName);
    }
}
