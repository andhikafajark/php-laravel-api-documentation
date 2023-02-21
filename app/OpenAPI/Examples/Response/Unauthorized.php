<?php

namespace App\OpenAPI\Examples\Response;

use OpenApi\Attributes as OA;

#[OA\Examples(
    example: 'ResponseUnauthorized',
    summary: 'Unauthorized',
    description: 'Response Unauthorized',
    value: [
        'success' => false,
        'message' => 'Unauthorized',
        'data' => null
    ]
)]
class Unauthorized
{
}
