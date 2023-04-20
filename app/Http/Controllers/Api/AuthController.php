<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Traits\AuthUserTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use AuthUserTrait;

    /**
     * @var ActivityLogHelper
     */
    private ActivityLogHelper $activityLogHelper;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(ActivityLogHelper $activityLogHelper)
    {
        $this->activityLogHelper = $activityLogHelper;
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function loginUser(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->messages()->first(),
                'success' => false
            ], 422);
        }else {
            $credentials = request(['email', 'password']);

            if (! $token = auth()->attempt($credentials)) {
                return response()->json([
                    'message' => 'Unauthorized',
                    'success' => false
                ], 401);
            } elseif($this->getAuthUser()->email_verified_at == NULL) {
                return response()->json([
                    'message' => 'Email must be verified',
                    'success' => false
                ], 401);
            } else {
                $this->activityLogHelper->log($this->getAuthUser()->id, 'Login', 'User logged in', 'Successful'); //create activity log
                return $this->respondWithToken($token);
            }
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return UserResource
     */
    public function getUserProfile(): UserResource
    {
        return (new UserResource($this->getAuthUser()))->additional( [
            'message' => "Users profile fetched successfully",
            'success' => true
        ], 200);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logoutUser(): JsonResponse
    {
        auth()->logout();
        return response()->json([
            'message' => 'Logged out successfully',
            'success' => true
        ], 200);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refreshAuthToken(): JsonResponse
    {
        $newToken = auth()->refresh(true, true);
        return $this->respondWithToken($newToken);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => new UserResource($this->getAuthUser()),
            'success' => true
        ]);
    }

}
