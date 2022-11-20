<?php

namespace App\Controller;

use App\Service\UserService;

class UserController
{
    public UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function index(): void
    {
        $result = $this->userService->index();
        $code = $result["code"];
        http_response_code($code);
        echo json_encode([
            "success" => $result["success"],
            "message" => $result["message"],
            "user" => $result["user"] ?? null
        ]);
    }

    public function delete(): void
    {
        $result = $this->userService->delete();
        $code = $result["code"];
        http_response_code($code);
        echo json_encode([
            "success" => $result["success"],
            "message" => $result["message"]
        ]);
    }
}
