<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 11/10/2019
 * Time: 23:15
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\ReportRequest;
use App\Http\Resources\ReportTypeResource;
use App\SmartMicro\Repositories\Contracts\FinanceStatementInterface;
use App\SmartMicro\Repositories\Contracts\ReportInterface;

use App\SmartMicro\Repositories\Contracts\ReportTypeInterface;
use Illuminate\Http\Request;

class ReportController extends ApiController
{
    /**
     * @var ReportInterface
     */
    protected $reportRepository, $load, $reportTypeRepository, $financeStatementRepository;

    /**
     * ReportController constructor.
     * @param ReportInterface $reportInterface
     * @param ReportTypeInterface $reportTypeRepository
     * @param FinanceStatementInterface $financeStatementRepository
     */
    public function __construct(ReportInterface $reportInterface, ReportTypeInterface $reportTypeRepository, FinanceStatementInterface $financeStatementRepository)
    {
        $this->reportRepository = $reportInterface;
        $this->financeStatementRepository = $financeStatementRepository;
        $this->reportTypeRepository = $reportTypeRepository;
        $this->load = [];
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->reportTypeRepository->listAll($this->formatFields($select));
        } else
            $data = ReportTypeResource::collection($this->reportTypeRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param ReportRequest $request
     * @return bool
     */
    public function store(ReportRequest $request)
    {
        $branchId = auth()->user()->branch_id;
        $startDate = null;
        $endDate = null;
        $reportType = null;
        $data = $request->all();

        if ( array_key_exists('branch_id', $data)) {
            $branchId = $data['branch_id'];
        }
        if ( array_key_exists('start_date', $data)) {
            $startDate = $data['start_date'];
        }
        if ( array_key_exists('end_date', $data)) {
            $endDate = $data['end_date'];
        }
        if ( array_key_exists('report_type', $data)) {
            $reportType = $data['report_type'];
        }

        switch ($reportType){
            case 'loans_due': {
                return $this->reportRepository->loansDue($branchId, $startDate, $endDate);
            }
            break;
            case 'loan_arrears': {
                return $this->reportRepository->loanArrears($branchId, $startDate, $endDate);
            }
                break;
            case 'loan_repayment': {
                return $this->reportRepository->loanRepayment($branchId);
            }
                break;
            default:{
                return false;
            }
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $report = $this->reportTypeRepository->getById($uuid);
        $params = request()->query('params');
        if (!$report) {
            return $this->respondNotFound('Report not found.');
        }
        return $report;
    }

}