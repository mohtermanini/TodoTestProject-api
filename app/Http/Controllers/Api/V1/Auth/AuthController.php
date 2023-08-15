<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Actions\CreateAuthTokenAction;
use App\Http\Requests\Auth\StoreAuthRequest;

class AuthController extends Controller
{
    public function store(StoreAuthRequest $storeAuthRequest, AuthService $authService)
    {
        try {
            $user = $authService->getUserByCredentialsOrFail(
                $storeAuthRequest->email,
                $storeAuthRequest->password
            );
        } catch (\Exception $e) {
            abort(422, $e->getMessage());
        }

        $token = (new CreateAuthTokenAction)->execute($user, $storeAuthRequest->userAgent());

        return $this->responseCreated([
            'user' => new UserResource($user),
            'token' => $token
        ]);
    }

    public function destroy()
    {
        auth()->user()->currentAccessToken()->delete();
        return $this->responseDeleted();
    }
}