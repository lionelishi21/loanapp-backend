<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 02/11/2019
 * Time: 22:54
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\EmailTemplateRequest;
use App\Http\Resources\EmailTemplateResource;
use App\Models\EmailTemplate;
use App\SmartMicro\Repositories\Contracts\EmailTemplateInterface;

use Illuminate\Http\Request;

class EmailTemplateController extends ApiController
{
    /**
     * @var EmailTemplateInterface
     */
    protected $emailTemplateRepository;

    /**
     * EmailTemplateController constructor.
     * @param EmailTemplateInterface $emailTemplateInterface
     */
    public function __construct(EmailTemplateInterface $emailTemplateInterface)
    {
        $this->emailTemplateRepository = $emailTemplateInterface;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->emailTemplateRepository->listAll($this->formatFields($select));
        } else
            $data = EmailTemplateResource::collection($this->emailTemplateRepository->getAllPaginate());

        return $this->respondWithData($data);
    }

    /**
     * @param EmailTemplateRequest $request
     * @return mixed
     */
    public function store(EmailTemplateRequest $request)
    {
        $save = $this->emailTemplateRepository->create($request->all());

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! EmailTemplate has been created.');

        }

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $emailTemplate = $this->emailTemplateRepository->getById($uuid);

        if (!$emailTemplate) {
            return $this->respondNotFound('EmailTemplate not found.');
        }
        return $this->respondWithData(new EmailTemplateResource($emailTemplate));

    }

    /**
     * @param EmailTemplateRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(EmailTemplateRequest $request, $uuid)
    {
        $save = $this->emailTemplateRepository->update($request->all(), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else

            return $this->respondWithSuccess('Success !! EmailTemplate has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->emailTemplateRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! EmailTemplate has been deleted');
        }
        return $this->respondNotFound('EmailTemplate not deleted');
    }
}