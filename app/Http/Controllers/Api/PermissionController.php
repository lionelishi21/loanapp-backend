<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PermissionRequest;
use App\Http\Resources\PermissionResource;
use App\SmartMicro\Repositories\Contracts\PermissionInterface;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController  extends ApiController
{
    /**
     * @var \App\SmartMicro\Repositories\Contracts\PermissionInterface
     */
    protected $permissionRepository;

    /**
     * PermissionController constructor.
     * @param PermissionInterface $permissionInterface
     */
    public function __construct(PermissionInterface $permissionInterface)
    {
        $this->permissionRepository   = $permissionInterface;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->permissionRepository->listAll($this->formatFields($select));
        } else
            $data = PermissionResource::collection($this->permissionRepository->getAllPaginate());

        return $this->respondWithData($data);
    }

    /**
     * @param PermissionRequest $request
     * @return mixed
     */
    public function store(PermissionRequest $request)
    {
        $save = $this->permissionRepository->create($request->all());

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        }else{
            return $this->respondWithSuccess('Success !! Permission has been created.');

        }

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $permission = $this->permissionRepository->getById($uuid);

        if(!$permission)
        {
            return $this->respondNotFound('Permission not found.');
        }
        return $this->respondWithData(new PermissionResource($permission));

    }

    /**
     * @param PermissionRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(PermissionRequest $request, $uuid)
    {
        $save = $this->permissionRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        }else

            return $this->respondWithSuccess('Success !! Permission has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if($this->permissionRepository->delete($uuid)){
            return $this->respondWithSuccess('Success !! Permission has been deleted');
        }
        return $this->respondNotFound('Permission not deleted');
    }

    /**
     * @return mixed
     */
    public function me()
    {
        $permission = Auth::permission();
        if(isset($permission))
            return $permission;
        return $this->respondNotFound();
    }
}