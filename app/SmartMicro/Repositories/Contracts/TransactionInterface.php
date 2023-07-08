<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 28/08/2019
 * Time: 14:19
 */

namespace App\SmartMicro\Repositories\Contracts;

interface TransactionInterface extends BaseInterface
{
    /**
     * @param $loanPenaltyRepaymentId
     * @param $amount
     * @param $loanId
     * @return mixed
     */
    function penaltyWaiverEntry($loanPenaltyRepaymentId, $amount, $loanId);

    /**
     * Transaction for penalty amount payment
     * @param $amount
     * @param $loanPenaltyRepaymentId
     * @param $loanId
     * @return mixed
     */
    function penaltyPaymentEntry($amount, $loanPenaltyRepaymentId, $loanId);

    /**
     * Transaction for interest amount payment
     * @param $amount
     * @param $loanInterestRepaymentId
     * @param $loanId
     * @return mixed
     */
    function interestPaymentEntry($amount, $loanInterestRepaymentId, $loanId);

    /**
     * Transaction for waive accrued interest amount
     * @param $amount
     * @param $loanInterestRepaymentId
     * @param $loanId
     * @return mixed
     */
    function interestWaiverEntry($amount, $loanInterestRepaymentId, $loanId);

    /**
     * Transaction for principal amount payment
     * @param $amount
     * @param $loanPrincipalRepaymentId
     * @param $loanId
     * @return mixed
     */
    function principalPaymentEntry($amount, $loanPrincipalRepaymentId, $loanId);

    /**
     *  Transaction to  reduce Principal balance
     * @param $amount
     * @param $paymentId
     * @param $loan
     * @return mixed
     */
    function balanceReductionEntry($amount, $paymentId, $loan);
}