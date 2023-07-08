<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 11/10/2019
 * Time: 23:18
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\Report;
use App\SmartMicro\Repositories\Contracts\ReportInterface;

class ReportRepository extends BaseRepository implements ReportInterface
{
    protected $model;

    /**
     * ReportRepository constructor.
     * @param Report $model
     */
    function __construct(Report $model)
    {
        $this->model = $model;
    }

    public function loansDue($branchId, $startDate, $endDate){}

    public function loanArrears($branchId, $startDate, $endDate){}

    public function loanRepayment($loanId){}
}