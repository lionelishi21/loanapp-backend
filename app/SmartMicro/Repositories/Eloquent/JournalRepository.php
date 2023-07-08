<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 28/08/2019
 * Time: 14:17
 */

namespace App\SmartMicro\Repositories\Eloquent;

use App\Models\Account;
use App\Models\Journal;
use App\SmartMicro\Repositories\Contracts\AccountInterface;
use App\SmartMicro\Repositories\Contracts\AccountLedgerInterface;
use App\SmartMicro\Repositories\Contracts\JournalInterface;

class JournalRepository extends BaseRepository implements JournalInterface
{
    protected $model, $accountLedgerRepository, $accountRepository;

    /**
     * JournalRepository constructor.
     * @param Journal $model
     * @param AccountLedgerInterface $accountLedgerRepository
     * @param AccountInterface $accountRepository
     */
    function __construct(Journal $model, AccountLedgerInterface $accountLedgerRepository, AccountInterface $accountRepository)
    {
        $this->model = $model;
        $this->accountLedgerRepository = $accountLedgerRepository;
        $this->accountRepository = $accountRepository;
    }

    /**
     * Active user's branch
     * @return mixed
     */
    private function branchId() {
        return auth()->check() ? auth('api')->user()->branch_id : null;
    }

    /**
     * Deposit account for a provided member
     * @param $memberId
     * @return mixed
     * @throws \Exception
     */
    private function memberDepositAccount($memberId) {
        $accountId = Account::where('account_name', $memberId)
                ->where('account_code', MEMBER_DEPOSIT_CODE)
                ->select('id')
                ->first()['id']; // xyc deposit a/c
        if(is_null($accountId))
            throw new \Exception('Null MemberDepositAccount');
        return $accountId;
    }

    /**
     * Loan account for a provided member
     * @param $loanId
     * @return mixed
     * @throws \Exception
     */
    private function memberLoanAccountId($loanId) {
        $accountId = Account::where('account_name', $loanId)
            ->where('account_code', LOAN_RECEIVABLE_CODE)
            ->select('id')
            ->first()['id']; // xyz loan a/c
        if(is_null($accountId))
            throw new \Exception('Null MemberLoanAccountId '. $accountId);
        return $accountId;
    }

    /**
     * Get account from active or provided branch
     * @param $accountName
     * @param $branchId
     * @return mixed
     * @throws \Exception
     */
    private function accountId($accountName, $branchId = null) {
        if(is_null($branchId) ) {
            $branchId = $this->branchId();
        }
        $accountId = Account::where('branch_id', $branchId)
            ->where('account_name', $accountName)
            ->select('id')->first()['id'];
        if(is_null($accountId))
            throw new \Exception('Null Account ID');
        return $accountId;
    }

    /**
     * XYZ A/C = Member Deposit Account
     * @param $loan
     * @param string $memberId
     * @return mixed
     */
    private function getMemberAccount($loan, $memberId = '') {
        if(!is_null($loan)){
            return Account::where('account_name', $loan['member_id'])
                ->select('id')
                ->first()['id']; // xyc a/c
        }else {
            return Account::where('account_name', $memberId)
                ->select('id')
                ->first()['id']; // xyc a/c
        }
    }

    /**
     * @param array $data
     * @return null
     */
    public function create(array $data)
    {
        try{
            $journalEntry = $this->model->create($data);
            // Create an entry into the ledger too.
            $this->accountLedgerRepository->ledgerEntry($journalEntry['id']);
        }catch (\Exception $exception){
            report($exception);
        }
        return null;
    }

    /**
     * Add cash capital to the system
     * @param $capitalData
     * @throws \Exception
     */
    public function capitalToCashEntry($capitalData){
        $data = [
            'narration'             => 'Capital to CASH Account',
            'amount'                => $capitalData['amount'],
            'transaction_id'        => $capitalData['id'],
            'debit_account_id'      => $this->accountId(CASH_ACCOUNT_NAME, $capitalData['branch_id']), // cash a/c
            'credit_account_id'     => $this->accountId(CAPITAL_ACCOUNT_NAME, $capitalData['branch_id']), // capital a/c
        ];
        $this->create($data);
    }

    /**
     * Add bank capital to the system
     * @param $capitalData
     * @throws \Exception
     */
    public function capitalToBankEntry($capitalData){
        $data = [
            'narration'             => 'Capital to BANK Account',
            'amount'                => $capitalData['amount'],
            'transaction_id'        => $capitalData['id'],
            'debit_account_id'      => $this->accountId(BANK_ACCOUNT_NAME, $capitalData['branch_id']), // bank a/c
            'credit_account_id'     => $this->accountId(CAPITAL_ACCOUNT_NAME, $capitalData['branch_id']), // capital a/c
        ];
        $this->create($data);
    }

    /**
     * Add mpesa capital to the system - remember to also load your mpesa shortcode separately (safaricom org portal)
     * @param $capitalData
     * @throws \Exception
     */
    public function capitalToMpesaEntry($capitalData){
        $data = [
            'narration'             => 'Capital to MPESA Account',
            'amount'                => $capitalData['amount'],
            'transaction_id'        => $capitalData['id'],
            'debit_account_id'      => $this->accountId(MPESA_ACCOUNT_NAME, $capitalData['branch_id']), // mpesa a/c
            'credit_account_id'     => $this->accountId(CAPITAL_ACCOUNT_NAME, $capitalData['branch_id']), // capital a/c
        ];
        $this->create($data);
    }

    /**
     * Disburse loan.
     * 1. Amount from our bank a/c to member LOAN a/c.
     *      cr bank : dr loan receivable (member loan a/c)
     *
     * 2. Also, the depositing of the same amount to member DEPOSIT a/c.
     *      cr deposits a/c : dr bank (for the new 'deposit')
     * @param $loan
     * @throws \Exception
     */
    function loanDisburseBank($loan){
        if ($loan !== null){
            $dataDisbursement = [
                'narration'             => 'Loan Disbursed (bank) #'.$loan['loan_reference_number'],
                'amount'                => $loan['amount_approved'],
                'transaction_id'        => $loan['id'],
                'debit_account_id'      => $this->memberLoanAccountId($loan['id']), // xyz loan a/c
                'credit_account_id'     => $this->accountId(BANK_ACCOUNT_NAME) // bank a/c
            ];
            $this->create($dataDisbursement);

            // The amount is deposited in member's deposit account
            $dataDeposit = [
                'narration'             => 'Deposit via Loan Disburse (bank) #'.$loan['loan_reference_number'],
                'amount'                => $loan['amount_approved'],
                'transaction_id'        => $loan['id'],
                'debit_account_id'      => $this->accountId(BANK_ACCOUNT_NAME), // bank a/c
                'credit_account_id'     => $this->memberDepositAccount($loan['member_id']), // xyz deposit a/c
            ];
            $this->create($dataDeposit);
        }
    }

    /**
     * Disburse loan.
     * 1. Amount from our cash a/c to member LOAN a/c.
     *      cr bank : dr loan receivable (member loan a/c)
     *
     * 2. Also, the depositing of the same amount to member DEPOSIT a/c.
     *      cr deposits a/c : dr bank (for the new 'deposit')
     * @param $loan
     * @throws \Exception
     */
    function loanDisburseCash($loan){
        if ($loan !== null){
            $dataDisbursement = [
                'narration'             => 'Loan Disbursed (cash) #'.$loan['loan_reference_number'],
                'amount'                => $loan['amount_approved'],
                'transaction_id'        => $loan['id'],
                'debit_account_id'      => $this->memberLoanAccountId($loan['id']), // xyz loan a/c
                'credit_account_id'     => $this->accountId(CASH_ACCOUNT_NAME) // cash a/c
            ];
            $this->create($dataDisbursement);


            // The amount is deposited in member's deposit account
            $dataDeposit = [
                'narration'             => 'Deposit via Loan Disburse (cash) #'.$loan['loan_reference_number'],
                'amount'                => $loan['amount_approved'],
                'transaction_id'        => $loan['id'],
                'debit_account_id'      => $this->accountId(CASH_ACCOUNT_NAME), // cash a/c
                'credit_account_id'     => $this->memberDepositAccount($loan['member_id']), // xyz deposit a/c
            ];
            $this->create($dataDeposit);
        }
    }

    /**
     * Disburse loan.
     * 1. Amount from our mpesa a/c to member LOAN a/c.
     *      cr bank : dr loan receivable (member loan a/c)
     *
     * 2. Also, the depositing of the same amount to member DEPOSIT a/c.
     *      cr deposits a/c : dr bank (for the new 'deposit')
     * @param $loan
     * @throws \Exception
     */
    function loanDisburseMpesa($loan){
        if ($loan !== null){
            $dataDisbursement = [
                'narration'             => 'Loan Disbursed (mpesa) #'.$loan['loan_reference_number'],
                'amount'                => $loan['amount_approved'],
                'transaction_id'        => $loan['id'],
                'debit_account_id'      => $this->memberLoanAccountId($loan['id']), // xyz loan a/c
                'credit_account_id'     => $this->accountId(MPESA_ACCOUNT_NAME) // mpesa a/c
            ];
            $this->create($dataDisbursement);

            // The amount is deposited in member's deposit account
            $dataDeposit = [
                'narration'             => 'Deposit via Loan Disburse (mpesa) #'.$loan['loan_reference_number'],
                'amount'                => $loan['amount_approved'],
                'transaction_id'        => $loan['id'],
                'debit_account_id'      => $this->accountId(MPESA_ACCOUNT_NAME), // mpesa a/c
                'credit_account_id'     => $this->memberDepositAccount($loan['member_id']), // xyz deposit a/c
            ];
            $this->create($dataDeposit);
        }
    }

    /**
     * Journal entry for the service fee deduction from member deposit account.
     * We credit an income account i.e service fee a/c
     * @param $loan
     * @return mixed|void
     * @throws \Exception
     */
    public function serviceFeeDemand($loan) {
        if ($loan !== null) {
            $data = [
                'narration' => 'Service Fee #' . $loan['loan_reference_number'],
                'amount' => $loan['service_fee'],
                'transaction_id' => $loan['id'],
                'debit_account_id'     => $this->memberDepositAccount($loan['member_id']), // xyz a/c a/c
                'credit_account_id' => $this->accountId(SERVICE_FEE_ACCOUNT_NAME), // service fee a/c
            ];
            $this->create($data);
        }
    }

    /**
     * Journal entry for interest due
     * @param $loan
     * @param $interestAmount
     * @param $interestDueId
     * @return mixed|void
     * @throws \Exception
     */
    public function interestDue($loan, $interestAmount, $interestDueId) {
        $data = [
            'narration'             => 'Interest Due #'.$loan['loan_reference_number'],
            'amount'                => $interestAmount,
            'transaction_id'        => $interestDueId,
            'debit_account_id'      => $this->memberLoanAccountId($loan['id']), // xyz a/c
            'credit_account_id'     => $this->accountId(INTEREST_ACCOUNT_NAME, $loan['branch_id']), // interest a/c
            'branch_id'             => $loan['branch_id'], // NOTE: There will be no logged in user for the scheduled calculations.
            'created_by'            => 'system'
        ];
        $this->create($data);
    }

    /**
     * @param $loan
     * @param $penaltyAmount
     * @param $penaltyDueId
     * @throws \Exception
     */
    public function penaltyDue($loan, $penaltyAmount, $penaltyDueId) {
        $data = [
            'narration'             => 'Penalty Due #'.$loan['loan_reference_number'],
            'amount'                => $penaltyAmount,
            'transaction_id'        => $penaltyDueId,
            'debit_account_id'      => $this->memberLoanAccountId($loan['id']), // xyz a/c
            'credit_account_id'     => $this->accountId(PENALTY_ACCOUNT_NAME, $loan['branch_id']), // penalty a/c
            'branch_id'             => $loan['branch_id'], // NOTE: There will be no logged in user for the scheduled calculations.
            'created_by'            => 'system'
        ];
        $this->create($data);
    }

    /**
     * @param $loan
     * @param $waivedAmount
     * @param $penaltyDueId
     * @throws \Exception
     */
    public function penaltyWaiver($loan, $waivedAmount, $penaltyDueId) {
        $data = [
            'narration'             => 'Penalty Waived #'.$loan['loan_reference_number'],
            'amount'                => $waivedAmount,
            'transaction_id'        => $penaltyDueId,
            'debit_account_id'      => $this->accountId(PENALTY_ACCOUNT_NAME, $loan['branch_id']), // penalty a/c
            'credit_account_id'     => $this->memberLoanAccountId($loan['id']), // xyz a/c
            'branch_id'             => $loan['branch_id'], // NOTE: There will be no logged in user for the scheduled calculations.
            'created_by'            => 'system'
        ];
        $this->create($data);
    }

    /**
     * Jounal Entry for a branch expenditure
     * @param $expense
     * @return mixed|void
     * @throws \Exception
     */
    public function expenseEntry($expense) {
        $data = [
            'narration'             => $expense['title'],
            'amount'                => $expense['amount'],
            'transaction_id'        => $expense['id'],
            'debit_account_id'      => $expense['category_id'], // expense a/c
            'credit_account_id'     => $this->accountId(BANK_ACCOUNT_NAME), // bank a/c
        ];
        $this->create($data);
    }

    /**
     * Reverse an expense. An example is during edits.
     * @param $expense
     * @return mixed|void
     * @throws \Exception
     */
    public function expenseReverse($expense){
        $data = [
            'narration'             => $expense['title'] . ' - (Edited)',
            'amount'                => $expense['amount'],
            'transaction_id'        => $expense['id'],
            'debit_account_id'      => $this->accountId(BANK_ACCOUNT_NAME), // bank a/c
            'credit_account_id'     => $expense['category_id'], // expense a/c
        ];
        $this->create($data);
    }

    /**
     * @param $expense
     * @return mixed|void
     * @throws \Exception
     */
    public function expenseDelete($expense){
        $data = [
            'narration'             => $expense['title'] . ' - (Deleted)',
            'amount'                => $expense['amount'],
            'transaction_id'        => $expense['id'],
            'debit_account_id'      => $this->accountId(BANK_ACCOUNT_NAME), // bank a/c
            'credit_account_id'     => $expense['category_id'], // expense a/c
        ];
        $this->create($data);
    }

    /**
     * Receive member deposit - mpesa
     * @param $paymentData
     * @throws \Exception
     */
    public function paymentReceivedEntryMpesa($paymentData){
        $data = [
            'narration'             => 'Mpesa Deposit #'.$paymentData['receipt_number'],
            'amount'                => $paymentData['amount'],
            'transaction_id'        => $paymentData['id'],
            'debit_account_id'      => $this->accountId(MPESA_ACCOUNT_NAME), // mpesa a/c
            'credit_account_id'     => $this->memberDepositAccount($paymentData->member_id), // xyz a/c
        ];
        $this->create($data);
    }

    /**
     * Receive member deposit - cash
     * @param $paymentData
     * @throws \Exception
     */
    public function paymentReceivedEntryCash($paymentData){
        $data = [
            'narration'             => 'Cash Deposit #'.$paymentData['receipt_number'],
            'amount'                => $paymentData['amount'],
            'transaction_id'        => $paymentData['id'],
            'debit_account_id'      => $this->accountId(CASH_ACCOUNT_NAME), // cash a/c
            'credit_account_id'     => $this->memberDepositAccount($paymentData->member_id), // xyz a/c
        ];
        $this->create($data);
    }

    /**
     * Receive member deposit - bank
     * @param $paymentData
     * @throws \Exception
     */
    public function paymentReceivedEntryBank($paymentData){
        $data = [
            'narration'             => 'Bank Deposit #'.$paymentData['receipt_number'],
            'amount'                => $paymentData['amount'],
            'transaction_id'        => $paymentData['id'],
            'debit_account_id'      => $this->accountId(BANK_ACCOUNT_NAME), // bank a/c
            'credit_account_id'     => $this->memberDepositAccount($paymentData->member_id), // xyz a/c
        ];
        $this->create($data);
    }

    /**
     * Loan repayment -  From deposit a/c
     * @param $loan
     * @param $amount
     * @throws \Exception
     */
    public function repayLoanPenalty($loan, $amount) {
        $data = [
            'narration'             => 'Repayment #'.$loan['loan_reference_number'].' (penalty)',
            'amount'                => $amount,
            'transaction_id'        => $loan['id'],
            'debit_account_id'      => $this->memberDepositAccount($loan['member_id']), // xyz deposit a/c
            'credit_account_id'     => $this->memberLoanAccountId($loan['id']), // xyz loan a/c
        ];
        $this->create($data);
    }

    /**
     * Loan repayment -  From deposit a/c
     * @param $loan
     * @param $amount
     * @throws \Exception
     */
    public function repayLoanInterest($loan, $amount) {
        $data = [
            'narration'             => 'Repayment #'.$loan['loan_reference_number'].' (interest)',
            'amount'                => $amount,
            'transaction_id'        => $loan['id'],
            'debit_account_id'      => $this->memberDepositAccount($loan['member_id']), // xyz deposit a/c
            'credit_account_id'     => $this->memberLoanAccountId($loan['id']), // xyz loan a/c
        ];
        $this->create($data);
    }

    /**
     * Loan repayment -  From deposit a/c
     * @param $loan
     * @param $amount
     * @throws \Exception
     */
    public function repayLoanPrincipal($loan, $amount) {
        $data = [
            'narration'             => 'Repayment #'.$loan['loan_reference_number'].' (principal)',
            'amount'                => $amount,
            'transaction_id'        => $loan['id'],
            'debit_account_id'      => $this->memberDepositAccount($loan['member_id']), // xyz deposit a/c
            'credit_account_id'     => $this->memberLoanAccountId($loan['id']), // xyz loan a/c
        ];
        $this->create($data);
    }

    /**
     * @param $withdrawalData
     * @return mixed|void
     * @throws \Exception
     */
    public function withdrawalEntryBank($withdrawalData){
        $balance = -1 * ($this->accountRepository->accountBalance($this->memberDepositAccount($withdrawalData->member_id)));

        if($balance < $withdrawalData['amount']) {
            throw new \Exception('Denied. Requested withdrawal amount exceeds available account balance.');
        }

        $data = [
            'narration'             => 'Bank Withdrawal #'.$withdrawalData['withdrawal_number'],
            'amount'                => $withdrawalData['amount'],
            'transaction_id'        => $withdrawalData['id'],
            'debit_account_id'      => $this->memberDepositAccount($withdrawalData->member_id), // deposit xyz a/c
            'credit_account_id'     => $this->accountId(BANK_ACCOUNT_NAME), // bank a/c
        ];
        $this->create($data);
    }

    /**
     * @param $withdrawalData
     * @return mixed|void
     * @throws \Exception
     */
    public function withdrawalEntryCash($withdrawalData){
        $balance = -1 * ($this->accountRepository->accountBalance($this->memberDepositAccount($withdrawalData->member_id)));

        if($balance < $withdrawalData['amount']) {
            throw new \Exception('Denied. Requested withdrawal amount exceeds available account balance.');
        }

        $data = [
            'narration'             => 'Cash Withdrawal #'.$withdrawalData['withdrawal_number'],
            'amount'                => $withdrawalData['amount'],
            'transaction_id'        => $withdrawalData['id'],
            'debit_account_id'      => $this->memberDepositAccount($withdrawalData->member_id), // deposit xyz a/c
            'credit_account_id'     => $this->accountId(CASH_ACCOUNT_NAME), // cash a/c
        ];
        $this->create($data);
    }

    /**
     * @param $withdrawalData
     * @return mixed|void
     * @throws \Exception
     */
    public function withdrawalEntryMpesa($withdrawalData){
        $balance = -1 * ($this->accountRepository->accountBalance($this->memberDepositAccount($withdrawalData->member_id)));

        if($balance < $withdrawalData['amount']) {
            throw new \Exception('Denied. Requested withdrawal amount exceeds available account balance.');
        }

        $data = [
            'narration'             => 'Mpesa Withdrawal #'.$withdrawalData['withdrawal_number'],
            'amount'                => $withdrawalData['amount'],
            'transaction_id'        => $withdrawalData['id'],
            'debit_account_id'      => $this->memberDepositAccount($withdrawalData->member_id), // deposit xyz a/c
            'credit_account_id'     => $this->accountId(MPESA_ACCOUNT_NAME), // mpesa a/c
        ];
        $this->create($data);
    }
}