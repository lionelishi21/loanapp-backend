<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 21/09/2019
 * Time: 21:24
 */

namespace App\SmartMicro\Repositories\Contracts;

interface LoanPrincipalRepaymentInterface extends BaseInterface
{
    /**
     * @param $principalRepaymentId
     * @return mixed
     */
    function paidAmount($principalRepaymentId);

    /**
     * @param $amount
     * @param $loan
     * @param $date
     * @return mixed
     */
    function payDuePrincipal($amount, $loan, $date);
}