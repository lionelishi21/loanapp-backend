<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 11/07/2019
 * Time: 13:26
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\AssetPhotoRequest;
use App\Http\Resources\AssetPhotoResource;
use App\SmartMicro\Repositories\Contracts\AssetPhotoInterface;

use Illuminate\Http\Request;

class AssetPhotoController extends ApiController
{
    /**
     * @var AssetPhotoInterface
     */
    protected $assetPhotoRepository;

    /**
     * AssetPhotoController constructor.
     * @param AssetPhotoInterface $assetPhotoInterface
     */
    public function __construct(AssetPhotoInterface $assetPhotoInterface)
    {
        $this->assetPhotoRepository = $assetPhotoInterface;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->assetPhotoRepository->listAll($this->formatFields($select));
        } else
            $data = AssetPhotoResource::collection($this->assetPhotoRepository->getAllPaginate());
        return $this->respondWithData($data);
    }

    /**
     * @param AssetPhotoRequest $request
     * @return mixed
     */
    public function store(AssetPhotoRequest $request)
    {
        $save = $this->assetPhotoRepository->create($request->all());

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! AssetPhoto has been created.');
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $assetPhoto = $this->assetPhotoRepository->getById($uuid);

        if (!$assetPhoto) {
            return $this->respondNotFound('AssetPhoto not found.');
        }
        return $this->respondWithData(new AssetPhotoResource($assetPhoto));
    }

    /**
     * @param AssetPhotoRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(AssetPhotoRequest $request, $uuid)
    {
        $save = $this->assetPhotoRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! AssetPhoto has been updated.');
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->assetPhotoRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! AssetPhoto has been deleted');
        }
        return $this->respondNotFound('AssetPhoto not deleted');
    }
}