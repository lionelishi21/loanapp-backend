<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 11/10/2019
 * Time: 23:17
 */

namespace App\SmartMicro\Repositories\Contracts;

interface ReportInterface extends BaseInterface
{
    function loansDue($branchId, $startDate, $endDate);

    function loanArrears($branchId, $startDate, $endDate);

    function loanRepayment($loanId);
}