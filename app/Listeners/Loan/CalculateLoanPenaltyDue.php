<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 04/02/2020
 * Time: 20:47
 */

namespace App\Listeners\Loan;

use App\Events\Loan\LoanNextPeriodChecked;
use App\SmartMicro\Repositories\Contracts\LoanInterface;

/**
 * Calculate penalty that should be applied to a loan.
 * Any loan with overdue amounts (interest / principal), calculate any penalty that should be applied.
 *
 * Class CalculateLoanPenaltyDue
 * @package App\Listeners\Loan
 */
class CalculateLoanPenaltyDue
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
        $this->loanRepository = $loanInterface;
    }

    /**
     * Fetch all loans due for penalty today. Calculate and charge penalty.
     * Finally update the next calculation date for the loan.
     *
     * @param LoanNextPeriodChecked $event
     * @return void
     */
    public function handle(LoanNextPeriodChecked $event)
    {
        $today = date('Y-m-d');
        $this->loanRepository->calculatePenalties($today);
    }
}
