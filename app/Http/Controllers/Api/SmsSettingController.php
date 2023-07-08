<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 27/10/2019
 * Time: 13:36
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\SmsSettingRequest;
use App\Http\Resources\SmsSettingResource;
use App\SmartMicro\Repositories\Contracts\SmsSettingInterface;

use Illuminate\Http\Request;

class SmsSettingController extends ApiController
{
    /**
     * @var SmsSettingInterface
     */
    protected $smsSettingRepository;

    /**
     * SmsSettingController constructor.
     * @param SmsSettingInterface $smsSettingInterface
     */
    public function __construct(SmsSettingInterface $smsSettingInterface)
    {
        $this->smsSettingRepository = $smsSettingInterface;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $smsSetting = $this->smsSettingRepository->first();

        if (!$smsSetting) {
            return null;

        }

        return $this->respondWithData(new SmsSettingResource($smsSetting));
    }

    /**
     * @param SmsSettingRequest $request
     * @return mixed
     */
    public function store(SmsSettingRequest $request)
    {
        $save = $this->smsSettingRepository->create($request->all());

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! SmsSetting has been created.');

        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $smsSetting = $this->smsSettingRepository->getById($uuid);

        if (!$smsSetting) {
            return $this->respondNotFound('SmsSetting not found.');
        }
        return $this->respondWithData(new SmsSettingResource($smsSetting));

    }

    /**
     * @param SmsSettingRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(SmsSettingRequest $request, $uuid)
    {
        $save = $this->smsSettingRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! SmsSetting has been updated.');
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->smsSettingRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! SmsSetting has been deleted');
        }
        return $this->respondNotFound('SmsSetting not deleted');
    }
}