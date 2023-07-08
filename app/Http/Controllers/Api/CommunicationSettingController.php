<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 24/11/2019
 * Time: 17:40
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\CommunicationSettingRequest;
use App\Http\Resources\CommunicationSettingResource;
use App\SmartMicro\Repositories\Contracts\CommunicationSettingInterface;

use Illuminate\Http\Request;

class CommunicationSettingController extends ApiController
{
    /**
     * @var CommunicationSettingInterface
     */
    protected $communicationSettingRepository, $load, $journalRepository;

    /**
     * CommunicationSettingController constructor.
     * @param CommunicationSettingInterface $communicationSettingInterface
     */
    public function __construct(CommunicationSettingInterface $communicationSettingInterface)
    {
        $this->communicationSettingRepository = $communicationSettingInterface;
        $this->load = [];
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->communicationSettingRepository->listAll($this->formatFields($select));
        } else
            $data = CommunicationSettingResource::collection($this->communicationSettingRepository->getAllPaginate($this->load));

        return $this->respondWithData($data);
    }

    /**
     * @param CommunicationSettingRequest $request
     * @return array
     * @throws \Exception
     */
    public function store(CommunicationSettingRequest $request)
    {
        $save = $this->communicationSettingRepository->create($request->all());

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        }else{
            return $this->respondWithSuccess('Success !! CommunicationSetting has been created.');

        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $communicationSetting = $this->communicationSettingRepository->getById($uuid);

        if (!$communicationSetting) {
            return $this->respondNotFound('CommunicationSetting not found.');
        }
        return $this->respondWithData(new CommunicationSettingResource($communicationSetting));

    }

    /**
     * @param CommunicationSettingRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(CommunicationSettingRequest $request, $uuid)
    {
        $save = $this->communicationSettingRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else

            return $this->respondWithSuccess('Success !! CommunicationSetting has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->communicationSettingRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! CommunicationSetting has been deleted');
        }
        return $this->respondNotFound('CommunicationSetting not deleted');
    }
}