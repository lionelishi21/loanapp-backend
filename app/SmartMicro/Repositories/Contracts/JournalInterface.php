<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 28/08/2019
 * Time: 14:16
 */

namespace App\SmartMicro\Repositories\Contracts;

interface JournalInterface extends BaseInterface
{
    /**
     * @param $capitalData
     * @return mixed
     */
    public function capitalToCashEntry($capitalData);

    /**
     * @param $capitalData
     * @return mixed
     */
    public function capitalToBankEntry($capitalData);

    /**
     * @param $capitalData
     * @return mixed
     */
    public function capitalToMpesaEntry($capitalData);

    /**
     * @param $loan
     * @return mixed
     */
    function loanDisburseBank($loan);

    /**
     * @param $loan
     * @return mixed
     */
    function loanDisburseCash($loan);

    /**
     * @param $loan
     * @return mixed
     */
    function loanDisburseMpesa($loan);

    /**
     * @param $loan
     * @return mixed
     */
    function serviceFeeDemand($loan);

    /**
     * @param $loan
     * @param $interestAmount
     * @param $interestDueId
     * @return mixed
     */
    function interestDue($loan, $interestAmount, $interestDueId);

    /**
     * @param $loan
     * @param $penaltyAmount
     * @param $penaltyDueId
     * @return mixed
     */
    function penaltyDue($loan, $penaltyAmount, $penaltyDueId);

    /**
     * @param $paymentData
     * @return mixed
     */
    function paymentReceivedEntryMpesa($paymentData);

    /**
     * @param $paymentData
     * @return mixed
     */
    function paymentReceivedEntryCash($paymentData);

    /**
     * @param $paymentData
     * @return mixed
     */
    function paymentReceivedEntryBank($paymentData);

    /**
     * @param $loan
     * @param $amount
     * @return mixed
     */
    function repayLoanPenalty($loan, $amount);

    /**
     * @param $loan
     * @param $amount
     * @return mixed
     */
    function repayLoanInterest($loan, $amount);

    /**
     * @param $loan
     * @param $amount
     * @return mixed
     */
    function repayLoanPrincipal($loan, $amount);

    /**
     * @param $loan
     * @param $waivedAmount
     * @param $penaltyDueId
     * @return mixed
     */
    function penaltyWaiver($loan, $waivedAmount, $penaltyDueId);

    /**
     * @param $expense
     * @return mixed
     */
    function expenseEntry($expense);

    /**
     * @param $expense
     * @return mixed
     */
    function expenseReverse($expense);

    /**
     * @param $original
     * @return mixed
     */
    function expenseDelete($original);

    /**
     * @param $withdrawalData
     * @return mixed
     */
    function withdrawalEntryBank($withdrawalData);

    /**
     * @param $withdrawalData
     * @return mixed
     */
    function withdrawalEntryCash($withdrawalData);

    /**
     * @param $withdrawalData
     * @return mixed
     */
    function withdrawalEntryMpesa($withdrawalData);
}