<?php
/**
 * Created by PhpStorm.
 * Loan: kevin
 * Date: 26/10/2018
 * Time: 12:17
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\Loan;
use App\SmartMicro\Repositories\Contracts\InterestTypeInterface;
use App\SmartMicro\Repositories\Contracts\JournalInterface;
use App\SmartMicro\Repositories\Contracts\LoanInterestRepaymentInterface;
use App\SmartMicro\Repositories\Contracts\LoanInterface;
use App\SmartMicro\Repositories\Contracts\LoanPenaltyInterface;
use App\SmartMicro\Repositories\Contracts\LoanPrincipalRepaymentInterface;
use App\SmartMicro\Repositories\Contracts\PenaltyTypeInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LoanRepository extends BaseRepository implements LoanInterface {

    protected $model, $interestTypeRepository, $loanInterestRepayment, $loanPrincipalRepayment,
        $journalRepository, $penaltyTypeRepository, $penaltyRepository;

    /**
     * LoanRepository constructor.
     * @param Loan $model
     * @param InterestTypeInterface $interestTypeRepository
     * @param LoanInterestRepaymentInterface $loanInterestRepayment
     * @param LoanPrincipalRepaymentInterface $loanPrincipalRepayment
     * @param JournalInterface $journalRepository
     * @param PenaltyTypeInterface $penaltyTypeRepository
     * @param LoanPenaltyInterface $penaltyRepository
     */
    function __construct(Loan $model, InterestTypeInterface $interestTypeRepository,
                         LoanInterestRepaymentInterface $loanInterestRepayment, LoanPrincipalRepaymentInterface $loanPrincipalRepayment,
                         JournalInterface $journalRepository, PenaltyTypeInterface $penaltyTypeRepository, LoanPenaltyInterface $penaltyRepository)
    {
        $this->model = $model;
        $this->interestTypeRepository   = $interestTypeRepository;
        $this->loanInterestRepayment   = $loanInterestRepayment;
        $this->loanPrincipalRepayment   = $loanPrincipalRepayment;
        $this->journalRepository   = $journalRepository;
        $this->penaltyTypeRepository   = $penaltyTypeRepository;
        $this->penaltyRepository   = $penaltyRepository;
    }

    /**
     * Produce json data amortization for reducing balance method
     * @param $balance
     * @param $totalPeriods
     * @param $rate
     * @param null $startDate
     * @param null $frequency
     * @return array|\Illuminate\Http\JsonResponse|mixed
     */
    public function printReducingBalance($balance, $totalPeriods, $rate, $startDate = null, $frequency = null) {

        $payment = $this->calculateReducingBalancePayment($balance, $totalPeriods, $rate);

        $totPayment   = 0;
        $totInterest  = 0;
        $totPrincipal = 0;

        $data = [];

        $count = 0;
        do {
            $count++;

            $interest = (float) $this->formatAmount($balance * $rate/100);

            // what portion of payment applies to principal?
            $principal =  (float) $this->formatAmount($payment - $interest);

            // watch out for balance < payment
            if ($balance < $payment) {
                $principal = $balance;
                $payment = $this->formatAmount($interest + $principal);
            }

            // reduce balance by principal paid
            $balance = (float)$this->formatAmount($balance - $principal);

            // watch for rounding error that leaves a tiny balance
            if ($balance < 0) {
                $principal = $principal + $balance;
                $interest = $this->formatAmount($interest - $balance);
                $balance   = 0;
            }

            $x = new \stdClass();
            $x->count = $count;
            $x->due_date = $this->formatDate($this->calculateDueDate($startDate, $frequency, $count));
            $x->payment = formatMoney($payment);
            $x->interest = formatMoney($interest);
            $x->principal = formatMoney($principal);
            $x->balance = formatMoney($balance);

            $data[] = $x;

            $totPayment   = $this->formatAmount($totPayment + $payment);
            $totInterest = $this->formatAmount($totInterest + $interest);

            $totPrincipal = $this->formatAmount($totPrincipal + $principal);

            if ($payment < $interest) {
                return response()->json('Payment < Interest amount - rate is too high, or payment is too low');
            }

        } while ($balance > 0);

        $x = new \stdClass();
        $x->count = 'Total: ';
        $x->due_date = '';
        $x->payment = formatMoney($totPayment);
        $x->interest = formatMoney($totInterest);
        $x->principal = formatMoney($totPrincipal);
        $x->balance = '';

        $data[] = $x;

        return $data;
    }

    /**
     * Produce json data amortization for fixed / straight line method
     * @param $balance
     * @param $totalPeriods
     * @param $rate
     * @param null $startDate
     * @param null $frequency
     * @return array|\Illuminate\Http\JsonResponse|mixed
     */
    public function printFixedInterest($balance, $totalPeriods, $rate, $startDate = null, $frequency = null) {
        $totPayment   = 0;
        $totInterest  = 0;
        $totPrincipal = 0;

        $interestAmount = (float) $this->formatAmount($balance * ( $rate/100 ));

        $payment = (float) $this->calculateStraightLinePayment($balance, $totalPeriods, $rate );

        $count = 0;
        $data = [];
        do {
            $count++;

            // what portion of payment applies to principal?
            $principal = $this->formatAmount($payment - $interestAmount);

            // watch out for balance < payment
            if ($balance < $payment) {
                $principal = $balance;
                $payment   = $this->formatAmount($interestAmount + $principal);
            }

            // reduce balance by principal paid
            $balance = (float) $this->formatAmount($balance - $principal);

            // watch for rounding error that leaves a tiny balance
            if ($balance < 0) {
                $principal = $this->formatAmount($principal + $balance);
                $interestAmount  = $this->formatAmount($interestAmount - $balance);
                $balance   = 0;
            }

            $x = new \stdClass();
            $x->count = $count;
            $x->due_date = $this->formatDate($this->calculateDueDate($startDate, $frequency, $count));
            $x->payment = formatMoney($payment);
            $x->interest = formatMoney($interestAmount);
            $x->principal = formatMoney($principal);
            $x->balance = formatMoney($balance);

            $data[] = $x;

            $totPayment   = $this->formatAmount($totPayment + $payment);
            $totInterest  = $this->formatAmount($totInterest + $interestAmount);
            $totPrincipal = $this->formatAmount($totPrincipal + $principal);

            if ($payment < $interestAmount) {
                return response()->json('Payment < Interest amount - rate is too high, or payment is too low');
            }

        } while ($balance > 0);

        $x = new \stdClass();
        $x->count = 'Total: ';
        $x->due_date = '';
        $x->payment = formatMoney($totPayment);
        $x->interest = formatMoney($totInterest);
        $x->principal = formatMoney($totPrincipal);
        $x->balance = '';

        $data[] = $x;

        return $data;
    }

    /**
     * @param $startDate
     * @param $frequency
     * @param $count
     * @return Carbon|null
     */
    private function calculateDueDate($startDate, $frequency, $count) {

        if(null != $startDate && null != $frequency && null != $count){

            switch ($frequency){
                case 'monthly': {
                    return Carbon::create($startDate)
                        ->addMonthsNoOverflow($count);
                }
                    break;
                case 'weekly': {
                    return Carbon::create($startDate)
                        ->addWeeks($count);
                }
                    break;
                case 'daily': {
                    return Carbon::create($startDate)
                        ->addDays($count);
                }
                    break;
                case 'one_time': {
                    return $startDate;
                }
                    break;
                default: {
                    return $startDate;
                }
            }
        }
        return null;
    }

    /**
     * Calculate periodical payment amount using reducing balance method
     * @param $loanAmount
     * @param $totalPeriods
     * @param $interest
     * @return float|int
     */
    public function calculateReducingBalancePayment($loanAmount, $totalPeriods, $interest) {

        $interest = (float)$interest / 100;    // convert to a percentage

        $value1 = (float)$interest * pow((1 + $interest), $totalPeriods);
        $value2 = (float)pow((1 + $interest), $totalPeriods) - 1;

        $payment = +($loanAmount * ($value1 / $value2));

        $payment = (float) $this->formatAmount($payment);
        return $payment;
    }

    /**
     * Calculate periodical payment amount using straight line / fixed rate interest
     * @param $loanAmount
     * @param $totalPeriods
     * @param $rate
     * @return float
     */
    public function calculateStraightLinePayment($loanAmount, $totalPeriods, $rate) {

        $interestAmount = (float)( $rate / 100 ) * $loanAmount;

        $principalPerPeriod = (float) $loanAmount / $totalPeriods;

        $payment = $principalPerPeriod + $interestAmount;

        return (float) $this->formatAmount($payment);
    }

    /**
     * Format currency to two decimal places
     * @param $number
     * @return float
     */
    public function formatAmount ($number) {
        return (float) number_format($number, 2, '.', '');
    }

    /**
     * @param $balance
     * @param $totalPeriods
     * @param $rate
     * @param $counter
     * @return array|null
     */
    public function calculatePeriodicalReducingBalancePayment($balance, $totalPeriods, $rate, $counter)
    {
        $payment = $this->calculateReducingBalancePayment($balance, $totalPeriods, $rate);

        $count = 0;
        do {
            $count++;

            $interest = (float) $this->formatAmount($balance * $rate/100);

            // what portion of payment applies to principal?
            $principal =  (float) $this->formatAmount($payment - $interest);

            // watch out for balance < payment
            if ($balance < $payment) {
                $principal = $balance;
                $payment = $this->formatAmount($interest + $principal);
            }

            // reduce balance by principal paid
            $balance = (float)$this->formatAmount($balance - $principal);

            // watch for rounding error that leaves a tiny balance
            if ($balance < 0) {
                $principal = $principal + $balance;
                $interest = $this->formatAmount($interest - $balance);
                $balance   = 0;
            }

            if($count == $counter) {
                return $data = [
                    'interest' => $interest,
                    'payment' => $payment,
                    'principal' => $principal,
                    'count' => $count
                ];
            }

        } while ($balance > 0);
        return null;
    }

    /**
     * @param $balance
     * @param $totalPeriods
     * @param $rate
     * @param $counter
     * @return array|null
     */
    public function calculatePeriodicalFixedInterest($balance, $totalPeriods, $rate, $counter) {

        $interestAmount = (float) $this->formatAmount($balance * ( $rate/100 ));

        $payment = (float) $this->calculateStraightLinePayment($balance, $totalPeriods, $rate );

        $count = 0;
        do {
            $count++;

            // what portion of payment applies to principal?
            $principal = $this->formatAmount($payment - $interestAmount);

            // watch out for balance < payment
            if ($balance < $payment) {
                $principal = $balance;
                $payment   = $this->formatAmount($interestAmount + $principal);
            }

            // reduce balance by principal paid
            $balance = (float) $this->formatAmount($balance - $principal);

            // watch for rounding error that leaves a tiny balance
            if ($balance < 0) {
                $principal = $this->formatAmount($principal + $balance);
                $interestAmount  = $this->formatAmount($interestAmount - $balance);
                $balance   = 0;
            }

            if($count == $counter) {
                return $data = [
                    'interest' => $interestAmount,
                    'payment' => $payment,
                    'principal' => $principal,
                    'count' => $count
                ];
            }
        } while ($balance > 0);
        return null;
    }

    /**
     * An active loan has positive sum of penalties, interests or principal due.
     * @param $memberId
     * @param array $load
     * @return Loan|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getActiveLoan($memberId, $load = array()) {
        $loans = $this->memberLoans($memberId, $load);
        foreach ($loans as $loan){
            $penalty = $this->pendingPenalty($loan['id']);
            $interest = $this->pendingInterest($loan['id']);
            $principal = $this->pendingPrincipal($loan['id']);

            $pending = $penalty + $interest + $principal;
            if ($pending > 0)
                return $loan;
        }
        return null;
    }

    /**
     * @param array $load
     * @return Loan[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAllActiveLoans($load = array()) {
        return $this->model->with($load)
            ->where('closed_on', null)->get();
    }

    /**
     * @param $branchId
     * @param array $load
     * @return Loan[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|mixed
     */
    public function getActiveLoansPerBranch($branchId, $load = array()){
        return $this->model->with($load)
            ->where('branch_id', $branchId)
            ->where('end_date', null)->get();
    }

    /**
     * Takes a date, calculates due interest and principal for loans whose next_repayment_date matches the given date
     * @param $date
     * @return mixed|void
     * @throws \Exception
     */
    public function calculateLoanRepaymentDue($date) {
        // Active loans due on the given date
        $loans =  $this->model
                    ->with(['paymentFrequency'])
                    ->where('next_repayment_date', $date)
                    ->where('end_date', null)
                    ->get();

        if (isset($loans)){
            foreach ($loans as $loan) {
                DB::beginTransaction();
                try
                {
                    if(null !== $loan){
                        // Its the first period of repayment so get details from the loan itself
                        $loanAmount = $loan['amount_approved'];
                        $totalPeriods = $loan['repayment_period'];
                        $rate = $loan['interest_rate'];

                        $periodCounter = 0;

                        $totalPrincipal = DB::table('loan_principal_repayments')
                            ->select(DB::raw('SUM(amount) as totalPrincipal, COUNT(period_count) as counter'))
                            ->where('loan_id', $loan['id'])->get();

                        if (isset($totalPrincipal)){
                            foreach ($totalPrincipal->toArray() as $principal) {
                                if (null !== $principal) {
                                    $periodCounter = $principal->counter;
                                }
                            }
                        }

                        // Check if Loan Principal balance has been reduced by direct transactions
                        $totalPrincipalReduction = DB::table('transactions')
                            ->select(DB::raw('SUM(amount) as totalPrincipalReduction'))
                            ->where('transaction_type', 'balance_reduction')
                            ->where('loan_id', $loan['id'])
                            ->get();

                        if (isset($totalPrincipalReduction)){
                            foreach ($totalPrincipalReduction->toArray() as $totalReduction) {
                                if (null !== $totalReduction) {
                                    $loanAmount = $loanAmount - $totalReduction->totalPrincipalReduction;
                                }
                            }
                        }

                        $loanInterestType = $this->interestTypeRepository->getWhere('id', $loan->interest_type_id)->name;
                        switch ($loanInterestType){
                            case 'reducing_balance':{
                                $payment = $this->calculatePeriodicalReducingBalancePayment($loanAmount, $totalPeriods, $rate, $periodCounter+1)['payment'];
                                $interest = $this->calculatePeriodicalReducingBalancePayment($loanAmount, $totalPeriods, $rate, $periodCounter+1)['interest'];
                                $principal = $this->calculatePeriodicalReducingBalancePayment($loanAmount, $totalPeriods, $rate, $periodCounter+1)['principal'];
                                $count = $this->calculatePeriodicalReducingBalancePayment($loanAmount, $totalPeriods, $rate, $periodCounter+1)['count'];
                            }
                                break;
                            case 'fixed': {
                                $payment = $this->calculatePeriodicalFixedInterest($loanAmount, $totalPeriods, $rate, $periodCounter+1)['payment'];
                                $interest = $this->calculatePeriodicalFixedInterest($loanAmount, $totalPeriods, $rate, $periodCounter+1)['interest'];
                                $principal = $this->calculatePeriodicalFixedInterest($loanAmount, $totalPeriods, $rate, $periodCounter+1)['principal'];
                                $count = $this->calculatePeriodicalFixedInterest($loanAmount, $totalPeriods, $rate, $periodCounter+1)['count'];
                            }
                                break;
                            default: {
                                $payment = 0;
                                $interest = 0;
                                $principal = 0;
                                $count = 0;
                            }
                        }

                        $dueDate = $this->calculateDueDate($loan['start_date'],
                            $loan->paymentFrequency->name,
                            $count
                        );

                        // Due interest repayment entry
                        $interestDue = $this->loanInterestRepayment->create([
                            'branch_id'     => $loan['branch_id'],
                            'loan_id'       => $loan['id'],
                            'period_count'  => $count,
                            'due_date'      => $dueDate,
                            'amount'        => $interest,
                            'paid_on'       => null
                        ]);

                        // Journal entry for the interest due
                        $this->journalRepository->interestDue($loan, $interest, $interestDue->id);

                        // Due principal repayment entry
                        $this->loanPrincipalRepayment->create([
                            'branch_id'     => $loan['branch_id'],
                            'loan_id'       => $loan['id'],
                            'period_count'  => $count,
                            'due_date'      => $dueDate,
                            'amount'        => $principal,
                            'paid_on'       => null
                        ]);

                        // Update loan for future due date
                        $next_repayment_date = $this->calculateDueDate($loan['next_repayment_date'], $loan->paymentFrequency->name, 1);
                        Loan::where('id', $loan['id'])->update([
                            'next_repayment_date' => $next_repayment_date
                        ]);

                        // We have come to the end of periodic payments
                        if($periodCounter+1 == $totalPeriods){
                            Loan::where('id', $loan['id'])->update([
                                'end_date' => $next_repayment_date,
                                'next_repayment_date' => null
                            ]);
                        }
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                    throw $e;
                }
            }
        }
    }

    /**
     * Calculate and charge penalty for any loan overdue for a given date
     * @param $date
     * @throws \Exception
     */
    public function calculatePenalties($date) {
        // I know interest is paid first. So fetching those loans with pending principal amounts serves this
        $loans = $this->model
            ->with(['penaltyFrequency'])
            ->join('loan_principal_repayments', 'loans.id', '=', 'loan_principal_repayments.loan_id')
            ->select('loans.id', 'loans.penalty_type_id', 'loans.penalty_value',
                'loans.penalty_frequency_id', 'loans.member_id', 'loans.branch_id', 'loans.loan_reference_number')
            ->where('loan_principal_repayments.paid_on', null)
            ->get();

        if (isset($loans)){
            foreach ($loans as $loan) {
                $branchId = $loan->branch_id;
                $loanId = $loan->id;
                DB::beginTransaction();
                try
                {
                    if(null !== $loan){
                        // fetch overdue Principal
                        $overDuePrincipal = DB::table('loan_principal_repayments')
                            ->select(DB::raw('SUM(amount) as total'))
                            ->where('loan_id', $loanId)
                            ->whereDate ('due_date', '<', $date)
                            ->first()->total;

                        // fetch overdue Interest
                        $overDueInterest = DB::table('loan_interest_repayments')
                            ->select(DB::raw('SUM(amount) as total'))
                            ->where('loan_id', $loanId)
                            ->whereDate ('due_date', '<', $date)
                            ->first()->total;

                        // Calculate penalties
                        if (isset($overDuePrincipal) && $overDuePrincipal > 0 || (isset($overDueInterest) && $overDueInterest > 0)) {

                            $penaltyAmount = $this->penaltyAmount($date, $loan, $overDuePrincipal, $overDueInterest);
                            if ($penaltyAmount > 0) {
                                // Due penalty entry (loan_penalties)
                                $penaltyDue = $this->penaltyRepository->create([
                                    'branch_id'     => $branchId,
                                    'loan_id'       => $loanId,
                                    'period_count'  => 0,
                                    'due_date'      => $date,
                                    'amount'        => $penaltyAmount,
                                    'paid_on'       => null
                                ]);
                                // Journal entry for Due penalty
                                $this->journalRepository->penaltyDue($loan, $penaltyAmount, $penaltyDue->id);
                            }
                        }
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                    throw $e;
                }
            }
        }
    }

    /**
     * @param $date
     * @param $loan
     * @param $overDuePrincipal
     * @param $overDueInterest
     * @return float|int
     */
    private function penaltyAmount($date, $loan, $overDuePrincipal, $overDueInterest){
        $loanId = $loan->id;
        $loanPenaltyTypeId = $loan->penalty_type_id;
        $loanPenaltyValue = $loan->penalty_value;

        $loanPenaltyFrequencyName = null;
        if(isset($loan) && isset($loan->penaltyFrequency))
            $loanPenaltyFrequencyName = $loan->penaltyFrequency->name;

        // Determine the date of the last penalty for this loan
        $latestPenalty = $this->penaltyRepository->getLatestWhere(1, 'loan_id', $loanId);
        if (!is_null($latestPenalty)){
            $latestDueDate = $latestPenalty->due_date;
        }else
             $latestDueDate = null;

        if(null != $latestDueDate && null != $loanPenaltyFrequencyName){
            switch ($loanPenaltyFrequencyName){
                case 'monthly': {
                    $periodDifference = Carbon::create($latestDueDate)
                        ->diffInMonths($date);
                }
                    break;
                case 'weekly': {
                    $periodDifference =  Carbon::create($latestDueDate)
                        ->diffInWeeks($date);
                }
                    break;
                case 'daily': {
                    $periodDifference =  Carbon::create($latestDueDate)
                        ->diffInDays($date);
                }
                    break;
                case 'one_time': {
                    $periodDifference =  0;
                }
                    break;
                default: {
                    $periodDifference =  -1;
                }
            }
        }else
            $periodDifference =  -1;

        $penaltyAmount = 0;
        if($periodDifference > 0 || $latestDueDate == null) {
            $loanPenalty = null;
            if(!is_null($loanPenaltyTypeId))
                $loanPenalty = $this->penaltyTypeRepository->getWhere('id', $loanPenaltyTypeId);

            $loanPenaltyType = '';
            if(!is_null($loanPenalty))
                $loanPenaltyType = $this->penaltyTypeRepository->getWhere('id', $loanPenaltyTypeId)->name;

            switch ($loanPenaltyType){
                case 'fixed_value':{
                    $penaltyAmount = $loan->penalty_value;
                }
                    break;
                case 'principal_due_percentage':{
                    $penaltyAmount = ($loanPenaltyValue / 100) * $overDuePrincipal;
                }
                    break;
                case 'principal_plus_interest_due_percentage':{
                    $penaltyAmount = ($loanPenaltyValue / 100) *  ($overDuePrincipal + $overDueInterest);
                }
                    break;
                default: {
                    $penaltyAmount = 0;
                }
            }
        }
        return $penaltyAmount;
    }

    /**
     * @param $loanId
     * @return mixed
     */
    public function paidAmount($loanId) {
        return DB::table('transactions')
            ->select(DB::raw('COALESCE(sum(transactions.amount), 0.0) as totalPaid'))
            ->where('loan_id', $loanId)
            ->where(function($query) {
                $query->where('transaction_type', 'balance_reduction')
                    ->orWhere('transaction_type', 'principal_payment');
            })
            ->first()->totalPaid;
    }

    /**
     * Loans due on given date / today
     * @param string $date
     * @return array
     */
    public function dueOnDate($date = ''){
        if(is_null($date) || $date == '')
            $date = date('Y-m-d');

        $penalties = DB::table(DB::raw("(SELECT
                      loan_penalties.id,
                      loan_penalties.loan_id,
                      loan_penalties.paid_on,
                      loan_penalties.due_date,
                      loan_penalties.amount from loan_penalties GROUP BY loan_penalties.id
                      )as t1"))
            ->where('t1.paid_on', '=', null)
            ->where('t1.due_date', '=', $date)
            ->select(DB::raw(
                't1.loan_id as loan_id,
                    COALESCE(sum(t2.paid), 0.0) as paidPenalty,
                    COALESCE(sum(t1.amount), 0.0) - COALESCE(sum(t2.paid), 0.0) as pendingPenalty,
                    COALESCE(sum(t1.amount), 0.0) as totalPenalty'
            ))
            ->groupBy("t1.loan_id")
            ->leftJoin(DB::raw("(SELECT
                      transactions.loan_penalties_id,
                      COALESCE(sum(transactions.amount), 0.0) as paid,
                      transactions.amount from transactions GROUP BY transactions.loan_penalties_id
                      )as t2"),function($join){
                $join->on('t1.id', '=', 't2.loan_penalties_id');
            })
            ->get();

        $interests = DB::table(DB::raw("(SELECT
                      loan_interest_repayments.id,
                      loan_interest_repayments.loan_id,
                      loan_interest_repayments.paid_on,
                      loan_interest_repayments.due_date,
                      loan_interest_repayments.amount from loan_interest_repayments GROUP BY loan_interest_repayments.id
                      )as t1"))
            ->where('t1.paid_on', '=', null)
            ->where('t1.due_date', '=', $date)
            ->select(DB::raw(
                't1.loan_id as loan_id,
                    COALESCE(sum(t2.paid), 0.0) as paidInterest,
                    COALESCE(sum(t1.amount), 0.0) - COALESCE(sum(t2.paid), 0.0) as pendingInterest,
                    COALESCE(sum(t1.amount), 0.0) as totalInterest'
            ))
            ->groupBy("t1.loan_id")
            ->leftJoin(DB::raw("(SELECT
                      transactions.loan_interest_repayments_id,
                      COALESCE(sum(transactions.amount), 0.0) as paid,
                      transactions.amount from transactions GROUP BY transactions.loan_interest_repayments_id
                      )as t2"),function($join){
                $join->on('t1.id', '=', 't2.loan_interest_repayments_id');
            })
            ->get();

        $principals = DB::table(DB::raw("(SELECT
                      loan_principal_repayments.id,
                      loan_principal_repayments.loan_id,
                      loan_principal_repayments.paid_on,
                      loan_principal_repayments.due_date,
                      loan_principal_repayments.amount from loan_principal_repayments GROUP BY loan_principal_repayments.id
                      )as t1"))
            ->where('t1.paid_on', '=', null)
            ->where('t1.due_date', '=', $date)

            ->select(DB::raw(
                't1.loan_id as loan_id,
                    COALESCE(sum(t2.paid), 0.0) as paidPrincipal,
                    COALESCE(sum(t1.amount), 0.0) - COALESCE(sum(t2.paid), 0.0) as pendingPrincipal,
                    COALESCE(sum(t1.amount), 0.0) as totalPrincipal'
            ))
            ->groupBy("t1.loan_id")
            ->leftJoin(DB::raw("(SELECT
                      transactions.loan_principal_repayments_id,
                      COALESCE(sum(transactions.amount), 0.0) as paid,
                      transactions.amount from transactions GROUP BY transactions.loan_principal_repayments_id
                      )as t2"),function($join){
                $join->on('t1.id', '=', 't2.loan_principal_repayments_id');
            })
            ->get();

        $dues = $penalties->merge($interests);
        $allDue = $dues->merge($principals)->groupBy('loan_id');

        $data = [];


        foreach ($allDue as $key => $value){
            $x = new \stdClass();
            $x->loan_id = $key;
            // Fetch Loan for extra data
            $loan =  $this->model
                ->with(['member', 'loanOfficer', 'loanType', 'paymentFrequency', 'loanType', 'interestType'])
                ->where('id', $x->loan_id)
                ->first();

            if(!is_null($loan)){
                $loan = $loan->toArray();

                $x->branch_id = $loan['branch_id'];
                $x->loan_reference_number = $loan['loan_reference_number'];

                $x->loan_type_id = $loan['loan_type_id'];
                $x->loan_type_name = $loan['loan_type']['name'];

                $x->payment_frequency = $loan['payment_frequency']['name'];
                $x->interest_rate = $loan['interest_rate'];
                $x->interest_type = $loan['interest_type']['name'];
                $x->repayment_period = $loan['repayment_period'];

                $x->loan_officer_first_name = $loan['loan_officer']['first_name'];
                $x->member_id = $loan['member_id'];
                $x->member_first_name = $loan['member']['first_name'];
                $x->member_last_name = $loan['member']['last_name'];
                $x->member_phone = $loan['member']['phone'];

                $x->loan_officer_id = $loan['loan_officer_id'];
                $x->loan_officer_first_name = $loan['loan_officer']['first_name'];

                $x->paidPenalty = '0.00';
                $x->pendingPenalty = '0.00';
                $x->totalPenalty = '0.00';

                $x->paidInterest = '0.00';
                $x->pendingInterest = '0.00';
                $x->totalInterest = '0.00';

                $x->paidPrincipal = '0.00';
                $x->pendingPrincipal = '0.00';
                $x->totalPrincipal = '0.00';

                $totalDue = 0;

                foreach ($value as $due){
                    if(property_exists($due, 'paidPenalty')){
                        $x->paidPenalty = formatMoney($due->paidPenalty);
                        $x->pendingPenalty = formatMoney($due->pendingPenalty);
                        $x->totalPenalty = formatMoney($due->totalPenalty);
                        $totalDue = $totalDue + $due->pendingPenalty;
                    }
                    if(property_exists($due, 'paidInterest')){
                        $x->paidInterest = formatMoney($due->paidInterest);
                        $x->pendingInterest = formatMoney($due->pendingInterest);
                        $x->totalInterest = formatMoney($due->totalInterest);
                        $totalDue = $totalDue + $due->pendingInterest;
                    }
                    if(property_exists($due, 'paidPrincipal')){
                        $x->paidPrincipal = formatMoney($due->paidPrincipal);
                        $x->pendingPrincipal = formatMoney($due->pendingPrincipal);
                        $x->totalPrincipal = formatMoney($due->totalPrincipal);
                        $totalDue = $totalDue + $due->pendingPrincipal;
                    }
                    $x->totalDue = formatMoney($totalDue);
                }
                $data[] = $x;
            }
        }

        return $data;
    }

    /**
     * Loans overdue as of today
     */
    public function overDue() {
        $date = date('Y-m-d');
        $penalties = DB::table(DB::raw("(SELECT
                      loan_penalties.id,
                      loan_penalties.loan_id,
                      loan_penalties.paid_on,
                      loan_penalties.due_date,
                      loan_penalties.amount from loan_penalties GROUP BY loan_penalties.id
                      )as t1"))
            ->where('t1.paid_on', '=', null)
            ->where('t1.due_date', '<', $date)
            ->select(DB::raw(
                't1.loan_id as loan_id,
                    t1.due_date as penaltyDueDate,
                    COALESCE(sum(t2.paid), 0.0) as paidPenalty,
                    COALESCE(sum(t1.amount), 0.0) - COALESCE(sum(t2.paid), 0.0) as pendingPenalty,
                    COALESCE(sum(t1.amount), 0.0) as totalPenalty'
            ))
            ->groupBy("t1.loan_id")
            ->leftJoin(DB::raw("(SELECT
                      transactions.loan_penalties_id,
                      COALESCE(sum(transactions.amount), 0.0) as paid,
                      transactions.amount from transactions GROUP BY transactions.loan_penalties_id
                      )as t2"),function($join){
                $join->on('t1.id', '=', 't2.loan_penalties_id');
            })
            ->get();

        $interests = DB::table(DB::raw("(SELECT
                      loan_interest_repayments.id,
                      loan_interest_repayments.loan_id,
                      loan_interest_repayments.paid_on,
                      loan_interest_repayments.due_date,
                      loan_interest_repayments.amount from loan_interest_repayments GROUP BY loan_interest_repayments.id
                      )as t1"))
            ->where('t1.paid_on', '=', null)
            ->where('t1.due_date', '<', $date)
            ->select(DB::raw(
                't1.loan_id as loan_id,
                    t1.due_date as interestDueDate,
                    COALESCE(sum(t2.paid), 0.0) as paidInterest,
                    COALESCE(sum(t1.amount), 0.0) - COALESCE(sum(t2.paid), 0.0) as pendingInterest,
                    COALESCE(sum(t1.amount), 0.0) as totalInterest'
            ))
            ->groupBy("t1.loan_id")
            ->leftJoin(DB::raw("(SELECT
                      transactions.loan_interest_repayments_id,
                      COALESCE(sum(transactions.amount), 0.0) as paid,
                      transactions.amount from transactions GROUP BY transactions.loan_interest_repayments_id
                      )as t2"),function($join){
                $join->on('t1.id', '=', 't2.loan_interest_repayments_id');
            })
            ->get();

        $principals = DB::table(DB::raw("(SELECT
                      loan_principal_repayments.id,
                      loan_principal_repayments.loan_id,
                      loan_principal_repayments.paid_on,
                      loan_principal_repayments.due_date,
                      loan_principal_repayments.amount from loan_principal_repayments GROUP BY loan_principal_repayments.id
                      )as t1"))
            ->where('t1.paid_on', '=', null)
            ->where('t1.due_date', '<', $date)

            ->select(DB::raw(
                't1.loan_id as loan_id,
                    t1.due_date as principalDueDate,
                    COALESCE(sum(t2.paid), 0.0) as paidPrincipal,
                    COALESCE(sum(t1.amount), 0.0) - COALESCE(sum(t2.paid), 0.0) as pendingPrincipal,
                    COALESCE(sum(t1.amount), 0.0) as totalPrincipal'
            ))
            ->groupBy("t1.loan_id")
            ->leftJoin(DB::raw("(SELECT
                      transactions.loan_principal_repayments_id,
                      COALESCE(sum(transactions.amount), 0.0) as paid,
                      transactions.amount from transactions GROUP BY transactions.loan_principal_repayments_id
                      )as t2"),function($join){
                $join->on('t1.id', '=', 't2.loan_principal_repayments_id');
            })
            ->get();

        $dues = $penalties->merge($interests);
        $allDue = $dues->merge($principals)->groupBy('loan_id');

        $data = [];


        foreach ($allDue as $key => $value){
            $x = new \stdClass();
            $x->loan_id = $key;
            // Fetch Loan for extra data
            $loan =  $this->model
                ->with(['member', 'loanType', 'paymentFrequency', 'loanType', 'interestType', 'loanOfficer'])
                ->where('id', $x->loan_id)
                ->first();

            if(!is_null($loan)){
                $loan = $loan->toArray();

                $x->branch_id = $loan['branch_id'];
                $x->loan_reference_number = $loan['loan_reference_number'];

                $x->loan_type_id = $loan['loan_type_id'];
                $x->loan_type_name = $loan['loan_type']['name'];

                $x->payment_frequency = $loan['payment_frequency']['name'];
                $x->interest_rate = $loan['interest_rate'];
                $x->interest_type = $loan['interest_type']['name'];
                $x->repayment_period = $loan['repayment_period'];

                $x->loan_officer_first_name = $loan['loan_officer']['first_name'];
                $x->member_id = $loan['member_id'];
                $x->member_first_name = $loan['member']['first_name'];
                $x->member_last_name = $loan['member']['last_name'];
                $x->member_phone = $loan['member']['phone'];

                $x->loan_officer_id = $loan['loan_officer_id'];
                $x->loan_officer_first_name = $loan['loan_officer']['first_name'];

                $x->penaltyDueDate = '';
                $x->paidPenalty = '0.00';
                $x->pendingPenalty = '0.00';
                $x->totalPenalty = '0.00';

                $x->interestDueDate = '';
                $x->paidInterest = '0.00';
                $x->pendingInterest = '0.00';
                $x->totalInterest = '0.00';

                $x->principalDueDate = '';
                $x->paidPrincipal = '0.00';
                $x->pendingPrincipal = '0.00';
                $x->totalPrincipal = '0.00';

                $totalDue = 0;

                foreach ($value as $due){
                    if(property_exists($due, 'paidPenalty')){
                        $x->penaltyDueDate = $this->formatDate($due->penaltyDueDate);
                        $x->paidPenalty = formatMoney($due->paidPenalty);
                        $x->pendingPenalty = formatMoney($due->pendingPenalty);
                        $x->totalPenalty = formatMoney($due->totalPenalty);
                        $totalDue = $totalDue + $due->pendingPenalty;
                    }
                    if(property_exists($due, 'paidInterest')){
                        $x->interestDueDate = $this->formatDate($due->interestDueDate);
                        $x->paidInterest = formatMoney($due->paidInterest);
                        $x->pendingInterest = formatMoney($due->pendingInterest);
                        $x->totalInterest = formatMoney($due->totalInterest);
                        $totalDue = $totalDue + $due->pendingInterest;
                    }
                    if(property_exists($due, 'paidPrincipal')){
                        $x->principalDueDate = $this->formatDate($due->principalDueDate);
                        $x->paidPrincipal = formatMoney($due->paidPrincipal);
                        $x->pendingPrincipal = formatMoney($due->pendingPrincipal);
                        $x->totalPrincipal = formatMoney($due->totalPrincipal);
                        $totalDue = $totalDue + $due->pendingPrincipal;
                    }
                    $x->totalDue = formatMoney($totalDue);
                }
                $data[] = $x;
            }
        }
        return $data;
    }

    /**
     * For a loan, calculate pending penalty  amount
     * @param $loanId
     * @return mixed
     */
    public function pendingPenalty($loanId) {

        return DB::table(DB::raw("(SELECT
                      loan_penalties.id,
                      loan_penalties.loan_id,
                      loan_penalties.paid_on,
                      loan_penalties.due_date,
                      loan_penalties.amount from loan_penalties GROUP BY loan_penalties.id
                      )as t1"))
            ->where('t1.paid_on', '=', null)
            ->where('t1.loan_id', '=', $loanId)
            ->select(DB::raw(
                't1.loan_id as loan_id,
                    t1.due_date as penaltyDueDate,
                    COALESCE(sum(t2.paid), 0.0) as paidPenalty,
                    COALESCE(sum(t1.amount), 0.0) - COALESCE(sum(t2.paid), 0.0) as pendingPenalty,
                    COALESCE(sum(t1.amount), 0.0) as totalPenalty'
            ))
            ->leftJoin(DB::raw("(SELECT
                      transactions.loan_penalties_id,
                      COALESCE(sum(transactions.amount), 0.0) as paid,
                      transactions.amount from transactions GROUP BY transactions.loan_penalties_id
                      )as t2"),function($join){
                $join->on('t1.id', '=', 't2.loan_penalties_id');
            })
            ->first()->pendingPenalty;
    }

    /**
     * For a loan calculate pending interest amount
     * @param $loanId
     * @return mixed
     */
    public function pendingInterest($loanId) {
        return DB::table(DB::raw("(SELECT
                      loan_interest_repayments.id,
                      loan_interest_repayments.loan_id,
                      loan_interest_repayments.paid_on,
                      loan_interest_repayments.amount from loan_interest_repayments GROUP BY loan_interest_repayments.id
                      )as t1"))
            ->where('t1.paid_on', '=', null)
            ->where('t1.loan_id', '=', $loanId)
            ->select(DB::raw(
                't1.loan_id as loan_id,
                    COALESCE(sum(t2.paid), 0.0) as paidInterest,
                    COALESCE(sum(t1.amount), 0.0) - COALESCE(sum(t2.paid), 0.0) as pendingInterest,
                    COALESCE(sum(t1.amount), 0.0) as totalInterest'
            ))
            ->leftJoin(DB::raw("(SELECT
                      transactions.loan_interest_repayments_id,
                      COALESCE(sum(transactions.amount), 0.0) as paid,
                      transactions.amount from transactions GROUP BY transactions.loan_interest_repayments_id
                      )as t2"),function($join){
                $join->on('t1.id', '=', 't2.loan_interest_repayments_id');
            })
            ->first()->pendingInterest;
    }

    /**
     * For a loan, calculate pending principal amount
     * @param $loanId
     * @return mixed
     */
    public function pendingPrincipal($loanId) {
        return DB::table(DB::raw("(SELECT
                      loan_principal_repayments.id,
                      loan_principal_repayments.loan_id,
                      loan_principal_repayments.paid_on,
                      loan_principal_repayments.due_date,
                      loan_principal_repayments.amount from loan_principal_repayments GROUP BY loan_principal_repayments.id
                      )as t1"))
            ->where('t1.paid_on', '=', null)
            ->where('t1.loan_id', '=', $loanId)
            ->select(DB::raw(
                't1.loan_id as loan_id,
                    t1.due_date as principalDueDate,
                    COALESCE(sum(t2.paid), 0.0) as paidPrincipal,
                    COALESCE(sum(t1.amount), 0.0) - COALESCE(sum(t2.paid), 0.0) as pendingPrincipal,
                    COALESCE(sum(t1.amount), 0.0) as totalPrincipal'
            ))
            ->leftJoin(DB::raw("(SELECT
                      transactions.loan_principal_repayments_id,
                      COALESCE(sum(transactions.amount), 0.0) as paid,
                      transactions.amount from transactions GROUP BY transactions.loan_principal_repayments_id
                      )as t2"),function($join){
                $join->on('t1.id', '=', 't2.loan_principal_repayments_id');
            })
            ->first()->pendingPrincipal;
    }

    /**
     * Total Loan calculated due amount
     * @param $loanId
     * @return mixed
     */
    public function totalPendingAmount($loanId){
        $penalty = $this->pendingPenalty($loanId);
        $interest = $this->pendingInterest($loanId);
        $principal = $this->pendingPrincipal($loanId);

        return $penalty + $interest + $principal;
    }

    /**
     * Loans For a member - active or in past
     * @param $memberId
     * @param array $load
     * @return Loan[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|mixed
     */
    public function memberLoans($memberId, $load = array()) {
        return $this->model->with($load)->where('member_id', $memberId)->get();
    }


    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function loansWithPendingPrincipal() {
        return $this->model
            ->with(['penaltyFrequency'])
            ->join('loan_principal_repayments', 'loans.id', '=', 'loan_principal_repayments.loan_id')
            ->select('loans.id', 'loans.penalty_type_id', 'loans.penalty_value',
                'loans.penalty_frequency_id', 'loans.member_id', 'loans.branch_id', 'loans.loan_reference_number')
            ->where('loan_principal_repayments.paid_on', null)
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function loansWithPendingInterest() {
        return $this->model
            ->join('loan_interest_repayments', 'loans.id', '=', 'loan_interest_repayments.loan_id')
            ->select('loans.id', 'loans.penalty_type_id', 'loans.penalty_value',
                'loans.penalty_frequency_id', 'loans.member_id', 'loans.branch_id', 'loans.loan_reference_number')
            ->where('loan_interest_repayments.paid_on', null)
            ->get();
    }

}