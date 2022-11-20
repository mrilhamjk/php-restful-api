<?php

namespace App\Middleware;

use Throwable;
use App\Util\Authenticate;

class GuestMiddleware
{
    public function index(): void
    {
        try {
            Authenticate::verify();
            http_response_code(403);
            echo json_encode([
                "success" => false,
                "message" => "Forbidden"
            ]);
        } catch (Throwable $th) {
        }
    }
}
