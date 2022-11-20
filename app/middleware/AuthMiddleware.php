<?php

namespace App\Middleware;

use Throwable;
use App\Util\Authenticate;

class AuthMiddleware
{
    public function index(): void
    {
        try {
            Authenticate::verify();
        } catch (Throwable $th) {
            http_response_code(401);
            echo json_encode([
                "success" => false,
                "message" => "Unauthorized"
            ]);
        }
    }
}
