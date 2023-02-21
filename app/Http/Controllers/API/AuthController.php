<?php

namespace App\Http\Controllers\API;

use App\Helpers\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Authenticate user login.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws BadRequestException|Exception
     *
     *
     */
    #[OA\Post(
        path: '/api/auth/login',
        description: 'Login to access RESTful API by email and password',
        summary: 'Login',
        requestBody: new OA\RequestBody(
            description: 'Login Request Body',
            required: true,
            content: [
                new OA\JsonContent(
                    examples: [
                        new OA\Examples(
                            example: 'Admin',
                            summary: 'Account Admin',
                            description: 'Account with Admin privilege',
                            value: [
                                'username' => 'admin@admin.com',
                                'password' => 'admin'
                            ]
                        )
                    ],
                    required: ['email', 'password'],
                    properties: [
                        new OA\Property(
                            property: 'email',
                            type: 'string',
                            maxLength: 255,
                            minLength: 1
                        ),
                        new OA\Property(
                            property: 'password',
                            type: 'string',
                            maxLength: 255,
                            minLength: 1
                        )
                    ]
                )
            ]
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'OK',
                content: [
                    new OA\JsonContent(
                        examples: [
                            new OA\Examples(
                                example: 'Success',
                                summary: 'Login Success',
                                description: 'Response Login Success',
                                value: [
                                    'success' => true,
                                    'message' => 'Login Success',
                                    'data' => [
                                        'access_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3RcL3dvcmtcL3RlbGtvbXNhdFwvbWNwYy10dnJpXC9wdWJsaWNcL2FwaVwvbG9naW4iLCJpYXQiOjE2NDE5NTE2MTYsImV4cCI6MTY0MTk1NTIxNiwibmJmIjoxNjQxOTUxNjE2LCJqdGkiOiJiVUtIdllNSVZYQUZmV0NTIiwic3ViIjoxLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3IiwidXNlciI6eyJpZCI6MSwidXNlcm5hbWUiOiJhZG1pbiIsIm5hbWUiOiJhZG1pbiIsImNyZWF0ZWRfYXQiOiIyMDIyLTAxLTExVDEyOjU5OjQzLjAwMDAwMFoiLCJ1cGRhdGVkX2F0IjoiMjAyMi0wMS0xMVQxMjo1OTo0My4wMDAwMDBaIn19.yva-99wyv_10doR_j4M2Laxj9PBjgWffRikV-SSOsh4',
                                        'token_type' => 'Bearer',
                                        'expires_in' => 0
                                    ]
                                ]
                            )
                        ],
                        ref: '#/components/schemas/Response'
                    )
                ]
            ),
            new OA\Response(
                response: 400,
                description: 'Bad Request',
                content: [
                    new OA\JsonContent(
                        examples: [
                            new OA\Examples(
                                example: 'Incorrect Username or Password',
                                summary: 'Incorrect Username or Password',
                                description: 'Response Incorrect Username or Password',
                                value: [
                                    'success' => false,
                                    'message' => 'Incorrect Username or Password',
                                    'data' => null
                                ]
                            )
                        ],
                        ref: '#/components/schemas/Response'
                    )
                ]
            ),
            new OA\Response(
                response: 422,
                description: 'Unprocessable Entity',
                content: [
                    new OA\JsonContent(
                        examples: [
                            new OA\Examples(
                                example: 'Invalid Input',
                                summary: 'Invalid Input',
                                description: 'Response Invalid Input',
                                value: [
                                    'success' => false,
                                    'message' => 'Unprocessable Entity',
                                    'data' => [
                                        'email' => ['The email field is required'],
                                        'password' => ['The password field is required'],
                                    ]
                                ]
                            )
                        ],
                        ref: '#/components/schemas/Response'
                    )
                ]
            ),
            new OA\Response(
                response: 500,
                description: 'Internal Server Error',
                content: [
                    new OA\JsonContent(
                        examples: [
                            new OA\Examples(
                                example: 'Internal Server Error',
                                ref: '#/components/examples/ResponseInternalServerError'
                            )
                        ],
                        ref: '#/components/schemas/Response'
                    )
                ]
            )
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $credential = $request->only(['username', 'password']);

            $user = User::where('username', $credential['username'])->first();

            if (!$user) {
                throw new BadRequestException('Incorrect Username or Password');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Login Success',
                'data' => null
//                    [
//                    'access_token' => $token,
//                    'token_type' => 'Bearer',
//                    'expires_in' => intval(env('JWT_TTL')),
//                ]
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();

            Log::exception($e, __METHOD__);

            throw $e;
        }
    }

    /**
     * Logout user.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws BadRequestException|Exception
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            if (!$session = auth()->user()->sessions()->where('jwt_jti', auth()->payload()['jti'])->first()) {
                throw new BadRequestException('Invalid JWT Token');
            }

            $session->delete();

            auth()->logout();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Logout Success',
                'data' => null
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();

            Log::exception($e, __METHOD__);

            throw $e;
        }
    }
}
