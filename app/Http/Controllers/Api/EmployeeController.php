<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 27/10/2018
 * Time: 11:11
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\SmartMicro\Repositories\Contracts\EmployeeInterface;

use Illuminate\Http\Request;

class EmployeeController  extends ApiController
{
    /**
     * @var EmployeeInterface
     */
    protected $employeeRepository;

    /**
     * EmployeeController constructor.
     * @param EmployeeInterface $employeeInterface
     */
    public function __construct(EmployeeInterface $employeeInterface)
    {
        $this->employeeRepository   = $employeeInterface;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->employeeRepository->listAll($this->formatFields($select));
        } else
            $data = EmployeeResource::collection($this->employeeRepository->getAllPaginate());

        return $this->respondWithData($data);
    }

    /**
     * @param EmployeeRequest $request
     * @return mixed
     */
    public function store(EmployeeRequest $request)
    {
        $save = $this->employeeRepository->create($request->all());

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        }else{
            return $this->respondWithSuccess('Success !! Employee has been created.');

        }

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $employee = $this->employeeRepository->getById($uuid);

        if(!$employee)
        {
            return $this->respondNotFound('Employee not found.');
        }
        return $this->respondWithData(new EmployeeResource($employee));

    }

    /**
     * @param EmployeeRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(EmployeeRequest $request, $uuid)
    {
        $save = $this->employeeRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        }else

            return $this->respondWithSuccess('Success !! Employee has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if($this->employeeRepository->delete($uuid)){
            return $this->respondWithSuccess('Success !! Employee has been deleted');
        }
        return $this->respondNotFound('Employee not deleted');
    }
}