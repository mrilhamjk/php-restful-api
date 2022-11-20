<?php

require_once __DIR__ . "/../vendor/autoload.php";

use App\Util\Router;
use App\Controller\{
    AuthController,
    UserController
};
use App\Middleware\{
    AuthMiddleware,
    GuestMiddleware
};

// ([0-9a-zA-Z\-]*)

Router::get("/", UserController::class, "index", [AuthMiddleware::class]);
Router::delete("/delete", UserController::class, "delete", [AuthMiddleware::class]);
Router::post("/signup", AuthController::class, "signup", [GuestMiddleware::class]);
Router::put("/signin", AuthController::class, "signin", [GuestMiddleware::class]);
Router::patch("/signout", AuthController::class, "signout", [AuthMiddleware::class]);

Router::run();
