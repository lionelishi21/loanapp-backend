<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 02/08/2019
 * Time: 10:43
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\AccountStatusRequest;
use App\Http\Resources\AccountStatusResource;
use App\SmartMicro\Repositories\Contracts\AccountStatusInterface;

use Illuminate\Http\Request;

class AccountStatusController extends ApiController
{
    /**
     * @var AccountStatusInterface
     */
    protected $accountStatusRepository;

    /**
     * AccountStatusController constructor.
     * @param AccountStatusInterface $accountStatusInterface
     */
    public function __construct(AccountStatusInterface $accountStatusInterface)
    {
        $this->accountStatusRepository = $accountStatusInterface;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->accountStatusRepository->listAll($this->formatFields($select));
        } else
            $data = AccountStatusResource::collection($this->accountStatusRepository->getAllPaginate());

        return $this->respondWithData($data);
    }

    /**
     * @param AccountStatusRequest $request
     * @return mixed
     */
    public function store(AccountStatusRequest $request)
    {
        $save = $this->accountStatusRepository->create($request->all());

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! AccountStatus has been created.');

        }

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $accountStatus = $this->accountStatusRepository->getById($uuid);

        if (!$accountStatus) {
            return $this->respondNotFound('AccountStatus not found.');
        }
        return $this->respondWithData(new AccountStatusResource($accountStatus));

    }

    /**
     * @param AccountStatusRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(AccountStatusRequest $request, $uuid)
    {
        $save = $this->accountStatusRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else

            return $this->respondWithSuccess('Success !! AccountStatus has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->accountStatusRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! AccountStatus has been deleted');
        }
        return $this->respondNotFound('AccountStatus not deleted');
    }
}