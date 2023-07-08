<?php

namespace App\Listeners\Loan;

use App\Events\Loan\LoanNextPeriodChecked;
use App\SmartMicro\Repositories\Contracts\LoanInterface;

/**
 * Calculate principal and interest to be paid for the next period.
 *
 * Any loan whose next_repayment_date is the given date, calculate principal and interest to pay for the next period.
 *
 * Class CalculateLoanPaymentDue
 * @package App\Listeners\Loan
 */
class CalculateLoanAmountDue
{
    /**
     * @var LoanInterface
     */
    protected $loanRepository;

    /**
     * CalculateLoanPaymentDue constructor.
     * @param LoanInterface $loanInterface
     */
    public function __construct(LoanInterface $loanInterface)
    {
        $this->loanRepository   = $loanInterface;
    }

    /**
     * Fetch all loans whose next_repayment_date is today. Calculate their interest and principal to pay for the next period.
     * Finally update the next calculation date for the loan.
     *
     * @param  LoanNextPeriodChecked  $event
     * @return void
     */
    public function handle(LoanNextPeriodChecked $event)
    {
        $today = date('Y-m-d');
        $this->loanRepository->calculateLoanRepaymentDue($today);
    }
}
