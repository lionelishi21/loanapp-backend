<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 25/10/2018
 * Time: 10:54
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SmartMicroServiceProvider extends ServiceProvider {

    /**
     * System repositories
     * @var array
     */
    protected $repositories = [
        'User',
        'Borrower',
        'Loan',
        'LoanType',
        'LoanApplication',
        'LoanStatus',
        'Branch',
        'Role',
        'Permission',
        'Payment',
        'PaymentMethod',
        'Employee',
        'Member',
        'Guarantor',
        'LoanApplicationStatus',
        'BorrowerStatus',
        'GeneralSetting',
        'WitnessType',
        'Asset',
        'AssetPhoto',
        'Account',
        'AccountStatus',
        'AccountType',
        'EmailSetting',
        'InterestType',
        'Expense',
        'ExpenseCategory',
        'FiscalPeriod',
        'Journal',
        'Transaction',
        'TransactionType',
        'AccountClass',
        'PaymentFrequency',
        'LoanPenalty',
        'LoanInterestRepayment',
        'LoanPrincipalRepayment',
        'Report',
        'ReportType',
        'FinanceStatement',
        'AccountLedger',
        'SmsSetting',
        'Capital',
        'EmailTemplate',
        'SmsTemplate',
        'PenaltyFrequency',
        'PenaltyType',
        'SmsSend',
        'CommunicationSetting',
        'MpesaBulkPayment',
        'MpesaCustomSend',
        'Withdrawal',
        'MpesaScheduledDisbursement'
    ];

    /**
     *  Loops through all repositories and binds them with their Eloquent implementation
     */
    public function register()
    {
        array_walk($this->repositories, function($repository) {
            $this->app->bind(
                'App\SmartMicro\Repositories\Contracts\\'. $repository . 'Interface',
                'App\SmartMicro\Repositories\Eloquent\\' . $repository . 'Repository'
            );
        });

    }
}