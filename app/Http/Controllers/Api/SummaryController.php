<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 26/09/2019
 * Time: 13:11
 */

namespace App\Http\Controllers\Api;

use App\Models\FailedLogin;
use App\Models\GeneralSetting;
use App\Models\Loan;
use App\Models\LoanPrincipalRepayment;
use App\Models\LoginEvent;
use App\SmartMicro\Repositories\Contracts\BranchInterface;
use App\SmartMicro\Repositories\Contracts\LoanApplicationInterface;
use App\SmartMicro\Repositories\Contracts\LoanInterface;
use App\SmartMicro\Repositories\Contracts\LoanPrincipalRepaymentInterface;
use App\SmartMicro\Repositories\Contracts\PaymentInterface;
use App\SmartMicro\Repositories\Contracts\UserInterface;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;

class SummaryController extends ApiController
{
    protected $branchRepository, $userRepository, $load, $loanRepository, $loanApplicationRepository,
        $paymentsRepo, $loanPrincipalRepaymentInterface;

    /**
     * SummaryController constructor.
     * @param BranchInterface $branchRepository
     * @param UserInterface $userRepository
     * @param LoanInterface $loanRepository
     * @param LoanApplicationInterface $loanApplicationRepository
     * @param LoanPrincipalRepaymentInterface $loanPrincipalRepaymentInterface
     * @param PaymentInterface $paymentsRepo
     */
    public function __construct(BranchInterface $branchRepository, UserInterface $userRepository, LoanInterface $loanRepository,
                                LoanApplicationInterface $loanApplicationRepository,
                                LoanPrincipalRepaymentInterface $loanPrincipalRepaymentInterface,
                                PaymentInterface $paymentsRepo)
    {
        $this->branchRepository = $branchRepository;
        $this->userRepository = $userRepository;
        $this->loanRepository = $loanRepository;
        $this->loanApplicationRepository = $loanApplicationRepository;
        $this->paymentsRepo = $paymentsRepo;
        $this->loanPrincipalRepaymentInterface = $loanPrincipalRepaymentInterface;

        $this->load = ['assets', 'employees', 'loans', 'loanApplications', 'members', 'payments', 'users'];
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        // branches summary
        $branches = $this->branchRepository->getAll();
        $users = $this->userRepository->getAll();
        $branchesCount = count($branches);

        $branchId = auth('api')->user()->branch_id;

        $data = [];

                $x = new \stdClass();
                $x = [];

                $currentBranch = $this->branchRepository->getById($branchId, $this->load);

                // Admin only
                $x['count_branches'] = count($branches);
                $x['current_branch'] = $currentBranch;
                $x['count_users'] = count($users);

                $x['count_assets'] = count($currentBranch->assets);
                $x['count_employees'] = count($currentBranch->employees);
                $x['count_loans'] = count($currentBranch->loans);
                $x['count_loan_applications'] = count($currentBranch->loanApplications);
                $x['count_members'] = count($currentBranch->members);
                $x['count_payments'] = count($currentBranch->payments);

                // active loans
                $activeLoans = $this->loanRepository->getActiveLoansPerBranch($currentBranch->id);
                $x['active_loans'] = $activeLoans;
                $x['count_loans'] = count($activeLoans);
                $x['loans_sum'] = $this->formatMoney($this->loanRepository->getSum('amount_approved'));

                // For all branches
                // All loans
                $allActiveLoans = $this->loanRepository->getAllActiveLoans();
                $x['count_loans'] = count($allActiveLoans);
                $x['loans_sum'] = $this->formatMoney($allActiveLoans->sum('amount_approved'));

                $today = date('Y-m-d');

                // Loans due today
                $loanDueToday = $this->loanRepository->dueOnDate($today);
                $x['loans_due_today'] = array_slice($loanDueToday, 0, 2);
                $x['count_loans_due_today'] = count($loanDueToday);

                // Overdue Loans
                $loansOverDue = $this->loanRepository->overDue();
                $x['loans_over_due'] = array_slice($loansOverDue, 0, 2);
                $x['count_loans_over_due'] = count($loansOverDue);

                // Total loan amount overdue
                $total = 0;
                foreach ($loansOverDue as $loan){
                    $total = $total + (float) $loan->totalDue;
                }
                $x['total_amount_over_due'] = $this->formatMoney($total);

                // loan Applications
                $pendingApplications = $this->loanApplicationRepository->getAllPending(['member', 'loanType']);
                $x['count_pending_applications'] = count($pendingApplications);
                $x['applications_sum'] = $this->formatMoney($pendingApplications->sum('amount_applied'));

                $latestLogins = LoginEvent::latest()->limit(5)->get();
                $latestFailedLogins = FailedLogin::latest()->limit(5)->get();
          return $x;
    }



    /**
     * @param Request $request
     * @return mixed
     */
    public function downloadLoansDueTodayReport(Request $request){
        $today = date('Y-m-d');
        $loans['data'] = $this->dueToday();

        // Settings
        $setting = GeneralSetting::first();
        $file_path = $setting->logo;
        $local_path = '';
        if($file_path != '')
            $local_path = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR .'logos'.DIRECTORY_SEPARATOR. $file_path;
        $setting->logo_url = $local_path;

        // Generate PDF
        $pdf = PDF::loadView('reports.due-today', compact('loans', 'setting', 'today'));

        return $pdf->download('statement.pdf');
    }


    /**
     * @param Request $request
     * @return mixed
     */
    public function downloadLoansOverdueReport(Request $request){
        $today = date('d-m-Y');
        $loans['data'] = $this->overDueToday();

        // Settings
        $setting = GeneralSetting::first();
        $file_path = $setting->logo;
        $local_path = '';
        if($file_path != '')
            $local_path = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR .'logos'.DIRECTORY_SEPARATOR. $file_path;
        $setting->logo_url = $local_path;

        // Generate PDF
        $pdf = PDF::loadView('reports.over-due', compact('loans', 'setting', 'today'));

        return $pdf->download('statement.pdf');
    }

    /**
     * @return mixed
     */
    private function dueToday(){
        $today = date('Y-m-d');
        return $this->loanRepository->dueOnDate($today);
    }

    /**
     * @return mixed
     */
    private function overDueToday(){
        return $this->loanRepository->overDue();
    }
}