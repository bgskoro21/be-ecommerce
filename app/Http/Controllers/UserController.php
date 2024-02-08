<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\MessageResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserTokenResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
}
