<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 27/10/2018
 * Time: 12:39
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\GuarantorRequest;
use App\Http\Resources\GuarantorResource;
use App\SmartMicro\Repositories\Contracts\GuarantorInterface;

use Illuminate\Http\Request;

class GuarantorController  extends ApiController
{
    /**
     * @var \App\SmartMicro\Repositories\Contracts\GuarantorInterface
     */
    protected $guarantorRepository, $load;

    /**
     * GuarantorController constructor.
     * @param GuarantorInterface $guarantorInterface
     */
    public function __construct(GuarantorInterface $guarantorInterface)
    {
        $this->guarantorRepository   = $guarantorInterface;
        $this->load = ['member', 'createdBy', 'branch', 'loanApplication'];
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->guarantorRepository->listAll($this->formatFields($select));
        } else
        $data = GuarantorResource::collection($this->guarantorRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param GuarantorRequest $request
     * @return mixed
     */
    public function store(GuarantorRequest $request)
    {
        $save = $this->guarantorRepository->create($request->all());

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        }else{
            return $this->respondWithSuccess('Success !! Guarantor has been created.');

        }

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $guarantor = $this->guarantorRepository->getById($uuid);

        if(!$guarantor)
        {
            return $this->respondNotFound('Guarantor not found.');
        }
        return $this->respondWithData(new GuarantorResource($guarantor));

    }

    /**
     * @param GuarantorRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(GuarantorRequest $request, $uuid)
    {
        $save = $this->guarantorRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        }else

            return $this->respondWithSuccess('Success !! Guarantor has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if($this->guarantorRepository->delete($uuid)){
            return $this->respondWithSuccess('Success !! Guarantor has been deleted');
        }
        return $this->respondNotFound('Guarantor not deleted');
    }
}