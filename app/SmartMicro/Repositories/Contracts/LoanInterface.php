<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 26/10/2018
 * Time: 12:17
 */

namespace App\SmartMicro\Repositories\Contracts;

interface LoanInterface extends BaseInterface {

    /**
     * @param $loanAmount
     * @param $totalPeriods
     * @param $rate
     * @param null $startDate
     * @param null $frequency
     * @return mixed
     */
    function printReducingBalance($loanAmount, $totalPeriods, $rate, $startDate = null, $frequency = null);

    /**
     * @param $loanAmount
     * @param $totalPeriods
     * @param $rate
     * @param null $startDate
     * @param null $frequency
     * @return mixed
     */
    function printFixedInterest($loanAmount, $totalPeriods, $rate, $startDate = null, $frequency = null);

    /**
     * @param $loanAmount
     * @param $totalPeriods
     * @param $interest
     * @return mixed
     */
    function calculateReducingBalancePayment($loanAmount, $totalPeriods, $interest);

    /**
     * @param $balance
     * @param $totalPeriods
     * @param $rate
     * @param $counter
     * @return mixed
     */
    function calculatePeriodicalReducingBalancePayment($balance, $totalPeriods, $rate, $counter);

    /**
     * @param $balance
     * @param $totalPeriods
     * @param $rate
     * @param $counter
     * @return mixed
     */
    function calculatePeriodicalFixedInterest($balance, $totalPeriods, $rate, $counter);

    /**
     * @param $memberId
     * @param array $load
     * @return mixed
     */
    function getActiveLoan($memberId, $load = array());

    /**
     * @param array $load
     * @return mixed
     */
    function getAllActiveLoans($load = array());

    /**
     * @param $branchId
     * @param array $load
     * @return mixed
     */
    function getActiveLoansPerBranch($branchId, $load = array());

    /**
     * @param $date
     * @return mixed
     */
    function calculateLoanRepaymentDue($date);

    /**
     * @param $date
     * @return mixed
     */
    function calculatePenalties($date);

    /**
     * @param $loanId
     * @return mixed
     */
    function paidAmount($loanId);

    /**
     * Loans due on the provided date or current date
     * @param string $date
     * @return mixed
     */
    public function dueOnDate($date = '');

    /**
     * Loans Overdue as per today''s date
     * Loans overdue
     * @return mixed
     */
    public function overDue();

    /**
     * @param $loanId
     * @return mixed
     */
    public function pendingPenalty($loanId);

    /**
     * @param $loanId
     * @return mixed
     */
    public function pendingInterest($loanId);

    /**
     * @param $loanId
     * @return mixed
     */
    public function pendingPrincipal($loanId);

    /**
     * @param $loanId
     * @return mixed
     */
    public function totalPendingAmount($loanId);

    /**
     * @param $memberId
     * @param array $load
     * @return mixed
     */
    public function memberLoans($memberId, $load = array());

    public function loansWithPendingPrincipal();

    public function loansWithPendingInterest();

}