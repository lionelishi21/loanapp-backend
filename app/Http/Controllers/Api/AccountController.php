<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 02/08/2019
 * Time: 10:43
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\AccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Models\GeneralSetting;
use App\SmartMicro\Repositories\Contracts\AccountInterface;

use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;

class AccountController extends ApiController
{
    /**
     * @var AccountInterface
     */
    protected $accountRepository, $load, $accountLedger;

    /**
     * AccountController constructor.
     * @param AccountInterface $accountInterface
     */
    public function __construct(AccountInterface $accountInterface)
    {
        $this->accountRepository = $accountInterface;
        $this->load = ['accountType', 'member', 'loan'];
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {

        if ($select = request()->query('list')) {
            return $this->accountRepository->listAll($this->formatFields($select), ['member']);
        }
        $data = $this->accountRepository->getAllPaginate($this->load);

        $data->map(function($item) {
            $item['accountBalance'] =  $this->formatMoney($this->accountRepository->accountBalance($item['id']));
            return $item;
        });
        return $this->respondWithData(AccountResource::collection($data));
    }

    /**
     * @param AccountRequest $request
     * @return mixed
     */
    public function store(AccountRequest $request)
    {
        $save = $this->accountRepository->create($request->all());

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! Account has been created.');
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $account = $this->accountRepository->getById($uuid);

        if (!$account) {
            return $this->respondNotFound('Account not found.');
        }

        $statement = $this->accountRepository->fetchAccountStatement($uuid);

        $account['statement'] = $statement;
        return $this->respondWithData(new AccountResource($account));
    }

    /**
     * @param AccountRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(AccountRequest $request, $uuid)
    {
        $save = $this->accountRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else

            return $this->respondWithSuccess('Success !! Account has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->accountRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! Account has been deleted');
        }
        return $this->respondNotFound('Account not deleted');
    }

    /**
     * @param Request $request
     * @return mixed|null
     */
    public function generalAccountStatement(Request $request) {
        $data = $request->all();
        $uuid = $data['id'];

        // If 'pdf', means we download the statement
        if(isset($data['pdf']) && $data['pdf'] == true){
            $request['type'] = 'general';
            return $this->downloadAccountStatement($request);
        }

        $account = $this->accountRepository->getById($uuid);

        if (isset($account))
            $account['statement'] = $this->accountRepository->fetchAccountStatement($account->id);

        return $this->respondWithData(new AccountResource($account));
    }

    /**
     * Fetch statement given a $memberId
     * @param Request $request
     * @return mixed
     */
    public function depositAccountStatement(Request $request){
        $data = $request->all();
        $memberId = $data['id'];

        // If 'pdf', means we download the statement
        if(isset($data['pdf']) && $data['pdf'] == true){
            $request['type'] = 'member';
           return $this->downloadAccountStatement($request);
        }

        $account = Account::where('account_name', $memberId)
            ->where('account_code', MEMBER_DEPOSIT_CODE)
            ->first();

        if (isset($account))
            $account['statement'] = $this->accountRepository->fetchAccountStatement($account->id);

        return $this->respondWithData(new AccountResource($account));
    }

    /**
     * Fetch statement given a $loanId
     * @param Request $request
     * @return mixed
     */
    public function loanAccountStatement(Request $request) {
        $data = $request->all();
        $loanId = $data['id'];

        // If 'pdf', means we download the statement
        if(isset($data['pdf']) && $data['pdf'] == true){
            $request['type'] = 'loan';
            return $this->downloadAccountStatement($request);
        }

        $account = Account::where('account_name', $loanId)
            ->where('account_code', LOAN_RECEIVABLE_CODE)
            ->first();

        if (isset($account))
            $account['statement'] = $this->accountRepository->fetchAccountStatement($account->id);

        return $this->respondWithData(new AccountResource($account));
    }

    /**
     * @param Request $request
     * @return mixed|null
     */
    private function downloadAccountStatement(Request $request) {
        $account = $this->getAccount($request->all());

        $setting = GeneralSetting::first();
        $file_path = $setting->logo;
        $local_path = '';
        if($file_path != '')
            $local_path = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR .'logos'.DIRECTORY_SEPARATOR. $file_path;

        $setting->logo_url = $local_path;

        if (isset($account)){
            $rawStatement = $this->accountRepository->fetchAccountStatement($account->id);

            $account['statement'] =  $rawStatement;

            $pageData = AccountResource::make($account)->toArray($request);

            $pdf = PDF::loadView('reports.account-statement', compact('pageData', 'setting'));

            return $pdf->download('statement.pdf');
        }

        return null;
    }

    /**
     * Special cases for member deposit and loan accounts as compared to other accounts
     * @param $data
     * @return mixed
     */
    private function getAccount($data) {

        switch ($data['type']){
            case  'member' :
                return Account::where('account_name', $data['id'])
                    ->where('account_code', MEMBER_DEPOSIT_CODE)
                    ->first();
                break;
            case  'loan' :
                return Account::with('loan')
                    ->where('account_name', $data['id'])
                    ->where('account_code', LOAN_RECEIVABLE_CODE)
                    ->first();
                break;
            default :
                return $this->accountRepository->getById($data['id']);
                break;
        }

    }
}