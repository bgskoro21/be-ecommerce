<?php

namespace App\Http\Controllers;

use App\Helpers\ManageFileStorage;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\MessageResource;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserTokenResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = new User($data);
        $user->password = Hash::make($data['password']);
        $user->save();

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function me(): UserResource
    {
        return new UserResource(auth()->user());
    }

    public function login(UserLoginRequest $request): UserTokenResource
    {
        $data = $request->validated();

        if(!$token = auth()->attempt($data))
        {
            throw new HttpResponseException(response([
                "errors" => [
                    "messages" => [
                        "username or password wrong"
                    ]
                ]
                    ]), 401);
        }

        return new UserTokenResource($token);
    }

    public function refresh(): UserTokenResource
    {
        $token = Auth::refresh();
        return new UserTokenResource($token);
    }

    public function logout(): MessageResource
    {
        auth()->logout();
        return new MessageResource("Successfully logged out");
    }

    public function search(Request $request): UserCollection
    {
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);

        $users = User::latest()->filter(request(['name','email']))->paginate(perPage:$size, page:$page)->withQueryString();

        return new UserCollection($users);
    }

    public function delete(int $id): MessageResource
    {
        $user = User::find($id);
        
        if(!$user)
        {
            throw new HttpResponseException(response: response()->json([
                "errors" => [
                    "message" => [
                        "User not found"
                    ]
                ]
                    ])->setStatusCode(404));
        }

        ManageFileStorage::delete('public/images/'.$user->profile_picture);
        $user->delete();
        return new MessageResource("Successfully deleted user!");
    }

    public function updateUser(int $id, UserUpdateRequest $request): UserResource
    {
        $data = $request->validated();
        $user = User::find($id);

        if(!$user)
        {
            throw new HttpResponseException(response: response()->json([
                "errors" => [
                    "message" => [
                        "User not found"
                    ]
                ]
                    ])->setStatusCode(404));
        }

        $file = $request->file('profile_picture');
        $imageName = null;
        if($file)
        {
            ManageFileStorage::delete('public/images/'.$user->profile_picture);
            $imageName = time().'_'.str_replace(" ", "_", $data['name']).'.'.$file->getClientOriginalExtension();
            Storage::disk('local')->put('public/images/'.$imageName, file_get_contents($file));
        }

        $user->fill($data);
        $user->profile_picture = $imageName;
        $user->save();

        return new UserResource($user);
    }
}
