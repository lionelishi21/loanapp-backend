<?php
/**
 * Created by PhpStorm.
 * Loan: kevin
 * Date: 26/10/2018
 * Time: 12:18
 */

namespace App\Http\Controllers\Api;

use App\Events\Loan\LoanNextPeriodChecked;
use App\Http\Requests\LoanCalculationRequest;
use App\Http\Requests\LoanRequest;
use App\Http\Resources\LoanResource;
use App\Jobs\ProcessMpesaBulkPayment;
use App\Models\GeneralSetting;
use App\SmartMicro\Repositories\Contracts\FinanceStatementInterface;
use App\SmartMicro\Repositories\Contracts\InterestTypeInterface;
use App\SmartMicro\Repositories\Contracts\JournalInterface;
use App\SmartMicro\Repositories\Contracts\LoanApplicationInterface;
use App\SmartMicro\Repositories\Contracts\LoanInterestRepaymentInterface;
use App\SmartMicro\Repositories\Contracts\LoanInterface;

use App\SmartMicro\Repositories\Contracts\LoanPrincipalRepaymentInterface;
use App\SmartMicro\Repositories\Contracts\LoanTypeInterface;
use App\SmartMicro\Repositories\Contracts\MemberInterface;
use App\SmartMicro\Repositories\Contracts\MpesaScheduledDisbursementInterface;
use App\SmartMicro\Repositories\Contracts\PaymentMethodInterface;
use App\SmartMicro\Repositories\Contracts\SmsSendInterface;
use App\Traits\CommunicationMessage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Barryvdh\DomPDF\Facade as PDF;

class LoanController  extends ApiController
{
    /**
     * @var LoanInterface
     */
    protected $loanRepository, $loanApplicationRepository, $interestTypeRepository, $memberRepository,
        $journalRepository, $load, $loanInterestRepayment, $loanPrincipalRepayment, $financeStatement,
        $smsSend, $paymentMethodRepository, $mpesaScheduledDisbursementRepo, $loanTypeRepository;

    /**
     * LoanController constructor.
     * @param LoanInterface $loanInterface
     * @param LoanApplicationInterface $loanApplicationInterface
     * @param JournalInterface $journalInterface
     * @param LoanInterestRepaymentInterface $loanInterestRepayment
     * @param LoanPrincipalRepaymentInterface $loanPrincipalRepayment
     * @param InterestTypeInterface $interestTypeRepository
     * @param FinanceStatementInterface $financeStatement
     * @param SmsSendInterface $smsSend
     * @param MemberInterface $memberRepository
     * @param PaymentMethodInterface $paymentMethodRepository
     * @param MpesaScheduledDisbursementInterface $mpesaScheduledDisbursementRepo
     * @param LoanTypeInterface $loanTypeRepository
     */
    public function __construct(LoanInterface $loanInterface, LoanApplicationInterface $loanApplicationInterface,
    JournalInterface $journalInterface, LoanInterestRepaymentInterface $loanInterestRepayment,
    LoanPrincipalRepaymentInterface $loanPrincipalRepayment, InterestTypeInterface $interestTypeRepository,
    FinanceStatementInterface $financeStatement, SmsSendInterface $smsSend,
    MemberInterface $memberRepository, PaymentMethodInterface $paymentMethodRepository,
    MpesaScheduledDisbursementInterface $mpesaScheduledDisbursementRepo, LoanTypeInterface $loanTypeRepository
    )
    {
        $this->loanRepository   = $loanInterface;
        $this->loanApplicationRepository   = $loanApplicationInterface;
        $this->journalRepository   = $journalInterface;

        $this->loanInterestRepayment   = $loanInterestRepayment;
        $this->loanPrincipalRepayment   = $loanPrincipalRepayment;
        $this->interestTypeRepository   = $interestTypeRepository;
        $this->financeStatement   = $financeStatement;
        $this->smsSend   = $smsSend;
        $this->memberRepository   = $memberRepository;
        $this->paymentMethodRepository   = $paymentMethodRepository;
        $this->mpesaScheduledDisbursementRepo   = $mpesaScheduledDisbursementRepo;
        $this->loanTypeRepository   = $loanTypeRepository;

        $this->load = ['loanType', 'member', 'interestType', 'paymentFrequency', 'loanOfficer'];
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->loanRepository->listAll($this->formatFields($select));
        }
        $data = $this->loanRepository->getAllPaginate($this->load);

        $data->map(function($item) {
            $item['balance'] =  $this->formatMoney($item['amount_approved'] - $this->loanRepository->paidAmount($item['id']));
            $item['paid_amount'] =  $this->formatMoney($this->loanRepository->paidAmount($item['id']));
            return $item;
        });

        return $this->respondWithData(LoanResource::collection($data));
    }

    /**
     * @param LoanRequest $request
     * @return array|mixed
     * @throws \Exception
     */
    public function store(LoanRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $user = auth('api')->user();
            $data = $request->all();

            $serviceFee = 0;
            if (isset($data['service_fee']))
                $serviceFee = $data['service_fee'];

            if (isset($data['amount_approved'])){
                $data['disburse_amount'] = ($data['amount_approved']) - $serviceFee;
            }

            if(array_key_exists('mpesa_fields', $data)){
                $data['mpesa_number'] = mpesaNumber($data['mpesa_fields']['mpesa_number']);
                $data['mpesa_first_name'] = $data['mpesa_fields']['mpesa_first_name'];
            }

            $disburseMethodName = '';
            $disburseMethod = $this->paymentMethodRepository->getWhere('id', $data['disburse_method_id']);
            if(isset($disburseMethod)) {
                $disburseMethodName = $disburseMethod['name'];
            }

            // Create new Loan
            $newLoan = $this->loanRepository->create($data);

            // Update loan application as already reviewed
            if($user && $newLoan) {
                $updateData = [
                    'reviewed_by_user_id' => $user->id,
                    'approved_on' => Carbon::now(),
                    'rejected_on' => null,
                    'reviewed_on' => Carbon::now(),
                ];
                $this->loanApplicationRepository->update($updateData, $data['loan_application_id']);
            }

            // 1. Journal entry for loan issue
            switch ($disburseMethodName){
                case 'BANK':
                    $this->journalRepository->loanDisburseBank($newLoan);
                    break;
                case 'CASH':
                    $this->journalRepository->loanDisburseCash($newLoan);
                    break;
                case 'MPESA':
                    $this->journalRepository->loanDisburseMpesa($newLoan);
                    break;
                default:
                    break;
            }

            // 2.  Journal entry for deduction of service fee
            if (array_key_exists('service_fee', $data) && $data['service_fee'] > 0) {
                $this->journalRepository->serviceFeeDemand($newLoan);
            }

            DB::commit();
            // Calculate loan dues immediately after loan is issued
            event(new LoanNextPeriodChecked());

            // Loan amount is now deposited into the member deposit account.
            // Initiate an automated withdrawal from the deposit account to the member mpesa account
            $disburseMethod = $this->paymentMethodRepository->getWhere('id', $newLoan['disburse_method_id']);
            if(isset($disburseMethod) && $disburseMethod['name'] == 'MPESA'){

                // Record for scheduled mpesa disbursement
                $mpesaDisburse = $this->mpesaScheduledDisbursementRepo->create([
                    'mpesa_number'  => $newLoan['mpesa_number'],
                    'amount'        => $newLoan['disburse_amount'],
                    'member_id'     => $newLoan['member_id']
                ]);

                // Schedule mpesa disbursement
                if (isset($mpesaDisburse))
                    ProcessMpesaBulkPayment::dispatch($mpesaDisburse);
            }

            // New loan email / sms
            $member = $this->memberRepository->getWhere('id', $newLoan['member_id']);
            if(!is_null($member) && !is_null($newLoan))
                CommunicationMessage::send('loan_application_approved', $member, $newLoan);

            return $this->respondWithSuccess('Success !! Loan has been created.');

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $loan = $this->loanRepository->getById($uuid, $this->load);

        if(!$loan) {
            return $this->respondNotFound('Loan not found.');
        }

        $loanAmount = $loan->amount_approved;
        $totalPeriods = $loan->repayment_period;
        $rate = $loan->interest_rate;
        $startDate = $loan->start_date;
        $frequency = $loan->paymentFrequency->name;

        switch ($loan->interestType->name) {
            case 'reducing_balance':
                {
                    $amortization = $this->loanRepository
                        ->printReducingBalance($loanAmount, $totalPeriods, $rate, $startDate, $frequency);
                }
                break;
            case 'fixed':
                {
                   $amortization = $this->loanRepository
                       ->printFixedInterest($loanAmount, $totalPeriods, $rate, $startDate, $frequency);
                }
                break;
            default:
                {
                    $amortization = [];
                }
        }

        $loan['amortization'] = $amortization;
        return $this->respondWithData(new LoanResource($loan));
    }

    /**
     * @param LoanRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(LoanRequest $request, $uuid)
    {
        $save = $this->loanRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        }else
            return $this->respondWithSuccess('Success !! Loan has been updated.');
    }

    /**
     * Remove an item
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        return $this->respondNotFound('Loan can not be deleted');
    }


    /**
     * Loan Calculator
     * @param LoanCalculationRequest $request
     * @return mixed
     */
    public function calculatorReport(LoanCalculationRequest $request) {
        $data = $request->all();
        $load = ['paymentFrequency', 'interestType'];

        $amount = $data['amount'];
        $startDate = $data['start_date'];

        $loanType = $this->loanTypeRepository->getById($data['loan_type_id'], $load);

        if (isset($loanType)){
            $totalPeriods = $loanType->repayment_period;
            $rate = $loanType->interest_rate;
            $frequency = $loanType->paymentFrequency->name;
            $interest_type = $loanType->interestType->name;

            $data['loan_type'] = $loanType->name;
            $data['period'] = $totalPeriods;
            $data['service_fee'] = $loanType->service_fee;
            $data['rate'] = $rate;
            $data['frequency_display'] = $loanType->paymentFrequency->display_name;
            $data['interest_type_display'] = $loanType->interestType->display_name;

            switch ($interest_type) {
                case 'reducing_balance':
                    {
                        $amortization = $this->loanRepository
                            ->printReducingBalance($amount, $totalPeriods, $rate, $startDate, $frequency);
                    }
                    break;
                case 'fixed':
                    {
                        $amortization = $this->loanRepository
                            ->printFixedInterest($amount, $totalPeriods, $rate, $startDate, $frequency);
                    }
                    break;
                default:
                    {
                        $amortization = [];
                    }
            }

            // Download Calculator result as pdf
            if($data['pdf'] == true){

                $setting = GeneralSetting::first();
                $file_path = $setting->logo;
                $local_path = '';
                if($file_path != '')
                    $local_path = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR .'logos'.DIRECTORY_SEPARATOR. $file_path;
                $setting->logo_url = $local_path;

                // Generate PDF
                $pdf = PDF::loadView('reports.calculator', compact('amortization', 'data', 'setting'));
                return $pdf->download('amortization.pdf');

            }else{
                return $amortization;
            }
        }
        return $this->respondNotFound('Loan Type not found.');
    }


    /**
     * @param Request $request
     * @return mixed
     */
    public function amortizationReport(Request $request){
        $data = $request->all();

        // If 'pdf', means we download the report
        if(isset($data['pdf']) && $data['pdf'] == true){
            return $this->downloadAmortizationReport($request);
        }

        $loan = $this->loanRepository->getById($data['id'], $this->load);

        if(!$loan) {
            return $this->respondNotFound('Loan not found.');
        }

        $loanAmount = $loan->amount_approved;
        $totalPeriods = $loan->repayment_period;
        $rate = $loan->interest_rate;
        $startDate = $loan->start_date;
        $frequency = $loan->paymentFrequency->name;

        switch ($loan->interestType->name) {
            case 'reducing_balance':
                {
                    $amortization = $this->loanRepository
                        ->printReducingBalance($loanAmount, $totalPeriods, $rate, $startDate, $frequency);
                }
                break;
            case 'fixed':
                {
                    $amortization = $this->loanRepository
                        ->printFixedInterest($loanAmount, $totalPeriods, $rate, $startDate, $frequency);
                }
                break;
            default:
                {
                    $amortization = [];
                }
        }

        $loan['amortization'] = $amortization;
        return $this->respondWithData(new LoanResource($loan));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function downloadAmortizationReport(Request $request){
        $data = $request->all();

        // Loan Data
        $loan = $this->loanRepository->getById($data['id'], $this->load);
        if(!$loan) {
            return $this->respondNotFound('Loan not found.');
        }
        $loanAmount = $loan->amount_approved;
        $totalPeriods = $loan->repayment_period;
        $rate = $loan->interest_rate;
        $startDate = $loan->start_date;
        $frequency = $loan->paymentFrequency->name;
        switch ($loan->interestType->name) {
            case 'reducing_balance':
                {
                    $amortization = $this->loanRepository
                        ->printReducingBalance($loanAmount, $totalPeriods, $rate, $startDate, $frequency);
                }
                break;
            case 'fixed':
                {
                    $amortization = $this->loanRepository
                        ->printFixedInterest($loanAmount, $totalPeriods, $rate, $startDate, $frequency);
                }
                break;
            default:
                {
                    $amortization = [];
                }
        }
        $loan['amortization'] = $amortization;

        // Settings
        $setting = GeneralSetting::first();
        $file_path = $setting->logo;
        $local_path = '';
        if($file_path != '')
            $local_path = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR .'logos'.DIRECTORY_SEPARATOR. $file_path;
        $setting->logo_url = $local_path;

        // Generate PDF
        $pdf = PDF::loadView('reports.amortization', compact('loan', 'setting'));

        return $pdf->download('amortization.pdf');
    }
}