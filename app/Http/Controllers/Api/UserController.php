<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 25/10/2018
 * Time: 11:28
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\SmartMicro\Repositories\Contracts\EmployeeInterface;
use App\SmartMicro\Repositories\Contracts\UserInterface;

use App\Traits\CommunicationMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController  extends ApiController
{
    /**
     * @var \App\SmartMicro\Repositories\Contracts\UserInterface
     */
    protected $userRepository, $employeeRepository, $load;

    /**
     * UserController constructor.
     * @param UserInterface $userInterface
     * @param EmployeeInterface $employeeRepository
     */
    public function __construct(UserInterface $userInterface, EmployeeInterface $employeeRepository)
    {
        $this->userRepository   = $userInterface;
        $this->employeeRepository   = $employeeRepository;
        $this->load = ['employee', 'role', 'branch'];
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($select = request()->query('list')) {
            return $this->userRepository->listAll($this->formatFields($select), ['role']);
        } else
            $data = UserResource::collection($this->userRepository->getAllPaginate($this->load));
        return $this->respondWithData($data);
    }

    /**
     * @param UserRequest $request
     * @return mixed
     */
    public function store(UserRequest $request)
    {
        $data = $request->all();

        if($request->hasFile('passport_photo')) {
            // Get filename with extension
            $filenameWithExt = $request->file('user_photo')->getClientOriginalName();

            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

            // Get just ext
            $extension = $request->file('user_photo')->getClientOriginalExtension();

            // Filename to store
            $fileNameToStore = $filename.'_'.time().'.'.$extension;

            // Upload Image
            $path = $request->file('user_photo')->storeAs('user_photos', $fileNameToStore);

            $data['user_photo'] = $fileNameToStore;
        }
        $save = $this->userRepository->create($data);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        }else{
            // New user email / sms
            CommunicationMessage::send('new_user_welcome', $save, $save);
            return $this->respondWithSuccess('Success !! User has been created.');
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $user = $this->userRepository->getById($uuid);

        if(!$user)
        {
            return $this->respondNotFound('User not found.');
        }
        return $this->respondWithData(new UserResource($user));

    }

    /**
     * @param UserRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(UserRequest $request, $uuid)
    {
        $save = $this->userRepository->update(array_filter($request->all()), $uuid);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        }else
            return $this->respondWithSuccess('Success !! User has been updated.');
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if($this->userRepository->delete($uuid)){
            return $this->respondWithSuccess('Success !! User has been deleted');
        }
        return $this->respondNotFound('User not deleted');
    }

    /**
     * @param Request $request
     * @return mixed|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function profilePic(Request $request)
    {
        $data = $request->all();
        if( array_key_exists('file_path', $data) ) {
            $file_path = $data['file_path'];
            $local_path = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR .'user_photos'.DIRECTORY_SEPARATOR. $file_path;
            return response()->file($local_path);
        }
        return $this->respondNotFound('file_path not provided');
    }
}