<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 21/09/2019
 * Time: 21:18
 */

namespace App\SmartMicro\Repositories\Contracts;

interface LoanInterestRepaymentInterface extends BaseInterface
{
    /**
     * @param $interestRepaymentId
     * @return mixed
     */
    function paidAmount($interestRepaymentId);

    /**
     * @param $amount
     * @param $loan
     * @param $date
     * @return mixed
     */
    function payDueInterest($amount, $loan, $date);
}