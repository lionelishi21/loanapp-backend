<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 27/08/2019
 * Time: 16:04
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\ExpenseCategoryRequest;
use App\Http\Resources\ExpenseCategoryResource;
use App\Models\AccountType;
use App\SmartMicro\Repositories\Contracts\AccountInterface;
use App\SmartMicro\Repositories\Contracts\ExpenseCategoryInterface;

use Illuminate\Http\Request;

class ExpenseCategoryController extends ApiController
{
    /**
     * @var ExpenseCategoryInterface
     */
    protected $expenseCategoryRepository, $accountRepository;

    /**
     * ExpenseCategoryController constructor.
     * @param ExpenseCategoryInterface $expenseCategoryInterface
     * @param AccountInterface $accountRepository
     */
    public function __construct(ExpenseCategoryInterface $expenseCategoryInterface, AccountInterface $accountRepository)
    {
        $this->expenseCategoryRepository = $expenseCategoryInterface;
        $this->accountRepository = $accountRepository;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->accountRepository->listAccounts(EXPENSE_CATEGORY_CODE, $this->formatFields($select));
        } else
        $data = ExpenseCategoryResource::collection($this->accountRepository->filterAccounts(EXPENSE_CATEGORY_CODE));

        return $this->respondWithData($data);
    }

    /**
     * @param ExpenseCategoryRequest $request
     * @return mixed
     */
    public function store(ExpenseCategoryRequest $request)
    {
        $expenseTypeId = AccountType::where('name', EXPENSE)->select('id')->first()['id'];
        $data = [
            'account_name' => ucwords($request->account_name),
            'account_type_id' => $expenseTypeId,
            'account_code' => EXPENSE_CATEGORY_CODE,
        ];
        $save = $this->accountRepository->create($data);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! ExpenseCategory has been created.');
        }

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $expenseCategory = $this->accountRepository->getById($uuid);

        if (!$expenseCategory) {
            return $this->respondNotFound('ExpenseCategory not found.');
        }
        return $this->respondWithData(new ExpenseCategoryResource($expenseCategory));

    }

    /**
     * @param ExpenseCategoryRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(ExpenseCategoryRequest $request, $uuid)
    {
        $save = $this->accountRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else

            return $this->respondWithSuccess('Success !! ExpenseCategory has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->accountRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! ExpenseCategory has been deleted');
        }
        return $this->respondNotFound('ExpenseCategory not deleted');
    }
}
