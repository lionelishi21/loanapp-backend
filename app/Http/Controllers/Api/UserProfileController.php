<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 02/11/2019
 * Time: 23:43
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\UserProfileRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\SmartMicro\Repositories\Contracts\UserInterface;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends ApiController
{
    /**
     * @var UserInterface
     */
    protected $userRepository;

    /**
     * UserProfileController constructor.
     * @param UserInterface $userInterface
     */
    public function __construct(UserInterface $userInterface)
    {
        $this->userRepository = $userInterface;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $user = $this->userRepository->getById($user->id);

        if(!$user)
        {
            return $this->respondNotFound('User not found.');
        }
        return $this->respondWithData(new UserResource($user));
    }

    /**
     * @param UserRequest $request
     * @return mixed
     */
    public function store(UserRequest $request)
    {
        return null;
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $user = Auth::user();
        if(isset($user)){
            return $this->respondWithData($user);
        }
        return $this->respondNotFound();
    }

    /**
     * @param UserProfileRequest $request
     * @param $id
     * @return array|mixed
     */
    public function update(UserProfileRequest $request, $id)
    {
        $user = Auth::user();
       $doNotUpdate = [
            'branch'        => 1,
            'branch_id'     => 1,
            'role'          => 1,
            'role_id'       => 1,
            'confirmed'     => 1
        ];

        $data = array_diff_key($request->all(), $doNotUpdate);

        $save = $this->userRepository->update(array_filter($data), $user->id);

        if(!is_null($save) && $save['error']){
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! User has been updated.');
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        return null;
    }

    public function forgotPassword() {
        // TODO
    }

    /**
     * @param Request $request
     */
    public function uploadPhoto(Request $request) {
        $data = $request->all();
        // Upload logo
        if($request->hasFile('photo')) {
            $filenameWithExt = $request->file('photo')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('photo')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            $path = $request->file('photo')->storeAs('profile_photos', $fileNameToStore);
            $data['photo'] = $fileNameToStore;
        }
        // also, delete previous image file from server
        $this->userRepository->update(array_filter($data), $data['id']);
    }

    /**
     * @param Request $request
     * @return mixed|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function fetchPhoto(Request $request)
    {
        $data = $request->all();
        $user = auth()->user();

        $file_path = $user->photo;
        if( array_key_exists('file_path', $data) && $file_path == null ) {
            $file_path = $data['file_path'];
        }
        $local_path = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR .'profile_photos'.DIRECTORY_SEPARATOR. $file_path;
        return response()->file($local_path);
    }
}