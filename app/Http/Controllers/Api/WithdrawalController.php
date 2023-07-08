<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 22/01/2020
 * Time: 18:08
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\WithdrawalRequest;
use App\Http\Resources\WithdrawalResource;
use App\SmartMicro\Repositories\Contracts\JournalInterface;
use App\SmartMicro\Repositories\Contracts\PaymentMethodInterface;
use App\SmartMicro\Repositories\Contracts\WithdrawalInterface;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends ApiController
{
    /**
     * @var WithdrawalInterface
     */
    protected $withdrawalRepository, $load, $journalRepository, $paymentMethodRepository;

    /**
     * WithdrawalController constructor.
     * @param WithdrawalInterface $withdrawalInterface
     * @param JournalInterface $journalRepository
     * @param PaymentMethodInterface $paymentMethodRepository
     */
    public function __construct(WithdrawalInterface $withdrawalInterface, JournalInterface $journalRepository,
                                PaymentMethodInterface $paymentMethodRepository)
    {
        $this->withdrawalRepository = $withdrawalInterface;
        $this->journalRepository = $journalRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->load = ['branch', 'member', 'paymentMethod'];
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->withdrawalRepository->listAll($this->formatFields($select));
        } else
            $data = WithdrawalResource::collection($this->withdrawalRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param WithdrawalRequest $request
     * @return array|mixed
     * @throws \Exception
     */
    public function store(WithdrawalRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $data = $request->all();

            $disburseMethodName = '';
            $disburseMethod = $this->paymentMethodRepository->getWhere('id', $data['method_id']);
            if(isset($disburseMethod)) {
                $disburseMethodName = $disburseMethod['name'];
            }

            if(array_key_exists('mpesa_fields', $data)){
                $data['mpesa_number'] = $data['mpesa_fields']['mpesa_number'];
                $data['first_name'] = $data['mpesa_fields']['first_name'];
                $data['last_name'] = $data['mpesa_fields']['last_name'];
            }

            if(array_key_exists('bank_fields', $data)){
                $data['cheque_number'] = $data['bank_fields']['cheque_number'];
                $data['bank_name'] = $data['bank_fields']['bank_name'];
                $data['bank_branch'] = $data['bank_fields']['bank_branch'];
                $data['cheque_date'] = $data['bank_fields']['cheque_date'];
            }

            $withdrawal = $this->withdrawalRepository->create($data);

            // journal entry
            switch ($disburseMethodName){
                case 'BANK':
                    $this->journalRepository->withdrawalEntryBank($withdrawal);
                    break;
                case 'CASH':
                    $this->journalRepository->withdrawalEntryCash($withdrawal);
                    break;
                default:
                    break;
            }
            DB::commit();

            return $this->respondWithSuccess('Success !! Withdrawal has been created.');

        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $withdrawal = $this->withdrawalRepository->getById($uuid);

        if (!$withdrawal) {
            return $this->respondNotFound('Withdrawal not found.');
        }
        return $this->respondWithData(new WithdrawalResource($withdrawal));
    }

    /**
     * @param WithdrawalRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(WithdrawalRequest $request, $uuid)
    {
        $save = $this->withdrawalRepository->update($request->all(), $uuid);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! Withdrawal has been updated.');
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->withdrawalRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! Withdrawal has been deleted');
        }
        return $this->respondNotFound('Withdrawal not deleted');
    }
}