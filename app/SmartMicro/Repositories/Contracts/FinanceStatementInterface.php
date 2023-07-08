<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 15/10/2019
 * Time: 12:28
 */

namespace App\SmartMicro\Repositories\Contracts;

interface FinanceStatementInterface extends BaseInterface
{
    /**
     * @param $branchId
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    function trialBalance($branchId, $startDate, $endDate);

    /**
     * @param $branchId
     * @return mixed
     */
    function incomeStatement($branchId);

    /**
     * @param $branchId
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    function balanceSheet($branchId, $startDate, $endDate);
}