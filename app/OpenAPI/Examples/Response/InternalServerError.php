<?php

namespace App\OpenAPI\Examples\Response;

use OpenApi\Attributes as OA;

#[OA\Examples(
    example: 'ResponseInternalServerError',
    summary: 'Internal Server Error',
    description: 'Response Internal Server Error',
    value: [
        'success' => false,
        'message' => 'Internal Server Error',
        'data' => null
    ]
)]
class InternalServerError
{
}
