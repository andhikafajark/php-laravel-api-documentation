<?php

namespace App\OpenAPI\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    description: 'Schema Response'
)]
class Response
{
    #[OA\Property]
    public bool $success;

    #[OA\Property]
    public string $message;

    #[OA\Property]
    public ?object $data;
}
