<?php

namespace App\OpenAPI\Examples\Response;

use OpenApi\Attributes as OA;

#[OA\Examples(
    example: 'ResponseNotFound',
    summary: 'Not Found',
    description: 'Response Not Found',
    value: [
        'success' => false,
        'message' => 'Not Found',
        'data' => null
    ]
)]
class NotFound
{
}
