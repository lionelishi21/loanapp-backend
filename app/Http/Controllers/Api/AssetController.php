<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 11/07/2019
 * Time: 13:23
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\AssetRequest;
use App\Http\Resources\AssetResource;
use App\SmartMicro\Repositories\Contracts\AssetInterface;

use Illuminate\Http\Request;

class AssetController extends ApiController
{
    /**
     * @var AssetInterface
     */
    protected $assetRepository;

    /**
     * AssetController constructor.
     * @param AssetInterface $assetInterface
     */
    public function __construct(AssetInterface $assetInterface)
    {
        $this->assetRepository = $assetInterface;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->assetRepository->listAll($this->formatFields($select));
        } else
            $data = AssetResource::collection($this->assetRepository->getAllPaginate());
        return $this->respondWithData($data);
    }

    /**
     * @param AssetRequest $request
     * @return mixed
     */
    public function store(AssetRequest $request)
    {
        $save = $this->assetRepository->create($request->all());

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! Asset has been created.');
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $asset = $this->assetRepository->getById($uuid);

        if (!$asset) {
            return $this->respondNotFound('Asset not found.');
        }
        return $this->respondWithData(new AssetResource($asset));
    }

    /**
     * @param AssetRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(AssetRequest $request, $uuid)
    {
        $save = $this->assetRepository->update($request->all(), $uuid);
        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! Asset has been updated.');
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->assetRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! Asset has been deleted');
        }
        return $this->respondNotFound('Asset not deleted');
    }
}