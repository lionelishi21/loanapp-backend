<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 27/08/2019
 * Time: 16:05
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\ExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\SmartMicro\Repositories\Contracts\ExpenseInterface;

use App\SmartMicro\Repositories\Contracts\JournalInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends ApiController
{
    /**
     * @var ExpenseInterface
     */
    protected $expenseRepository, $load, $journalRepository;

    /**
     * ExpenseController constructor.
     * @param ExpenseInterface $expenseInterface
     * @param JournalInterface $journalRepository
     */
    public function __construct(ExpenseInterface $expenseInterface, JournalInterface $journalRepository)
    {
        $this->expenseRepository = $expenseInterface;
        $this->journalRepository = $journalRepository;
        $this->load = ['category'];
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->expenseRepository->listAll($this->formatFields($select));
        } else
            $data = ExpenseResource::collection($this->expenseRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param ExpenseRequest $request
     * @return array
     * @throws \Exception
     */
    public function store(ExpenseRequest $request)
    {
        // Transaction start
        DB::beginTransaction();
        try
        {
            $newExpense = $this->expenseRepository->create($request->all());
            // Journal entries for the expense
            $this->journalRepository->expenseEntry($newExpense);
            DB::commit();
            return $this->respondWithSuccess('Success !! Expense has been created.');
        }catch (\Exception $e) {
            DB::rollback();
            throw $e;
            // return $this->respondNotSaved($e);
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $expense = $this->expenseRepository->getById($uuid);

        if (!$expense) {
            return $this->respondNotFound('Expense not found.');
        }
        return $this->respondWithData(new ExpenseResource($expense));

    }

    /**
     * @param ExpenseRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(ExpenseRequest $request, $uuid)
    {
        $original = $this->expenseRepository->getById($uuid);

        $data = $request->all();
        $updatedRecord = $this->expenseRepository->update($data, $uuid);

        if(!is_null($updatedRecord) && $updatedRecord['error']){
            return $this->respondNotSaved($updatedRecord['message']);
        } else {
            if ($data['amount'] != $original['amount']){
                // 1. Journal entry for old expense (reversal)
                $this->journalRepository->expenseReverse($original);
                // 2. Journal entry for the new expense
                $this->journalRepository->expenseEntry($data);
            }
            return $this->respondWithSuccess('Success !! Expense has been updated.');
        }

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        $original = $this->expenseRepository->getById($uuid);

        if ($this->expenseRepository->delete($uuid)) {
            // Reverse Journal entry
            $this->journalRepository->expenseDelete($original);
            return $this->respondWithSuccess('Success !! Expense has been deleted');
        }
        return $this->respondNotFound('Expense not deleted');
    }
}