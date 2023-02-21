<?php

namespace App\Http\Controllers\API;

use App\Helpers\Log;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Show user profile.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function profile(Request $request): JsonResponse
    {
        try {
            $data = User::select(
                'id',
                'full_name',
                'email',
                'name AS company_name',
                'address AS address',
                'city AS city',
                'phone as contact',
                DB::raw('CONCAT("/file?source=dtp&path=", avatar_url) AS image')
            )->findOrFail(auth()->payload()['user']->id);

            return response()->json([
                'success' => true,
                'message' => 'Get Data Success',
                'data' => $data
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::exception($e, __METHOD__);

            throw $e;
        }
    }
}
