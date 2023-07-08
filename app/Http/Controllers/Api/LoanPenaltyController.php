<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 21/09/2019
 * Time: 10:49
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoanPenaltyRequest;
use App\Http\Requests\WaiverRequest;
use App\Http\Resources\LoanPenaltyResource;
use App\Models\LoanPenalty;
use App\SmartMicro\Repositories\Contracts\JournalInterface;
use App\SmartMicro\Repositories\Contracts\LoanPenaltyInterface;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanPenaltyController extends ApiController
{
    /**
     * @var LoanPenaltyInterface
     */
    protected $loanPenaltyRepository, $load, $journalRepository;

    /**
     * LoanPenaltyController constructor.
     * @param LoanPenaltyInterface $loanPenaltyInterface
     * @param JournalInterface $journalInterface
     */
    public function __construct(LoanPenaltyInterface $loanPenaltyInterface, JournalInterface $journalInterface)
    {
        $this->loanPenaltyRepository = $loanPenaltyInterface;
        $this->load = ['loan'];
        $this->journalRepository   = $journalInterface;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->loanPenaltyRepository->listAll($this->formatFields($select));
        }
        $data = $this->loanPenaltyRepository->getAllPaginate($this->load);

        $data->map(function($item) {
            $item['balance'] =  $this->formatMoney($item['amount'] - $this->loanPenaltyRepository->paidAmount($item['id']));
            $item['paid_amount'] =  $this->formatMoney($this->loanPenaltyRepository->paidAmount($item['id']));
            return $item;
        });

        return $this->respondWithData(LoanPenaltyResource::collection($data));
    }

    /**
     * @param LoanPenaltyRequest $request
     * @return mixed
     */
    public function store(LoanPenaltyRequest $request)
    {
        LoanPenalty::create($request->all());
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $loanPenalty = $this->loanPenaltyRepository->getById($uuid);

        if (!$loanPenalty) {
            return $this->respondNotFound('LoanPenalty not found.');
        }
        return $this->respondWithData(new LoanPenaltyResource($loanPenalty));

    }

    /**
     * @param LoanPenaltyRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(LoanPenaltyRequest $request, $uuid)
    {
        $save = $this->loanPenaltyRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else

            return $this->respondWithSuccess('Success !! LoanPenalty has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->loanPenaltyRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! LoanPenalty has been deleted');
        }
        return $this->respondNotFound('LoanPenalty not deleted');
    }

    /**
     * @param WaiverRequest $request
     * @return array
     * @throws \Exception
     */
    public function waive(WaiverRequest $request){
        $data = $request->all();
        DB::beginTransaction();
        try
        {
            // the waiver transaction
            if(array_key_exists('waiver_amount', $data) && !is_null($data['waiver_amount']) && $data['waiver_amount'] > 0)
                $this->loanPenaltyRepository->waivePenalty($data['id'], $data['waiver_amount'], $data['loan_id']);

            // the waiver journal entry
            if(array_key_exists('waiver_amount', $data) && !is_null($data['waiver_amount']) && $data['waiver_amount'] > 0)
                $this->journalRepository->penaltyWaiver($data['loan'], $data['waiver_amount'], $data['id']);

            DB::commit();
            return $this->respondWithSuccess('Success !! Penalty Waived successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}