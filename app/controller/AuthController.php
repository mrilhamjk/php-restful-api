<?php

namespace App\Controller;

use App\Service\AuthService;

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function signup(): void
    {
        $result = $this->authService->signup();
        $code = $result["code"];
        http_response_code($code);
        echo json_encode([
            "success" => $result["success"],
            "message" => $result["message"],
            "user" => $result["user"] ?? null
        ]);
    }

    public function signin(): void
    {
        $result = $this->authService->signin();
        $code = $result["code"];
        http_response_code($code);
        echo json_encode([
            "success" => $result["success"],
            "message" => $result["message"],
            "user" => $result["user"] ?? null
        ]);
    }

    public function signout(): void
    {
        $result = $this->authService->signout();
        $code = $result["code"];
        http_response_code($code);
        echo json_encode([
            "success" => $result["success"],
            "message" => $result["message"]
        ]);
    }
}
