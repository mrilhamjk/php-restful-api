<?php

namespace App\Util;

class Router
{
    public static array $routes = [];

    public static function get(
        string $path,
        string $controller,
        string $handler = "index",
        array $middleware = []
    ): void {
        self::$routes[] = [
            "method" => "GET",
            "path" => $path,
            "controller" => $controller,
            "handler" => $handler,
            "middleware" => $middleware
        ];
    }

    public static function post(
        string $path,
        string $controller,
        string $handler = "index",
        array $middleware = []
    ): void {
        self::$routes[] = [
            "method" => "POST",
            "path" => $path,
            "controller" => $controller,
            "handler" => $handler,
            "middleware" => $middleware
        ];
    }

    public static function put(
        string $path,
        string $controller,
        string $handler = "index",
        array $middleware = []
    ): void {
        self::$routes[] = [
            "method" => "PUT",
            "path" => $path,
            "controller" => $controller,
            "handler" => $handler,
            "middleware" => $middleware
        ];
    }

    public static function patch(
        string $path,
        string $controller,
        string $handler = "index",
        array $middleware = []
    ): void {
        self::$routes[] = [
            "method" => "PATCH",
            "path" => $path,
            "controller" => $controller,
            "handler" => $handler,
            "middleware" => $middleware
        ];
    }

    public static function delete(
        string $path,
        string $controller,
        string $handler = "index",
        array $middleware = []
    ): void {
        self::$routes[] = [
            "method" => "DELETE",
            "path" => $path,
            "controller" => $controller,
            "handler" => $handler,
            "middleware" => $middleware
        ];
    }

    public static function run(): void
    {
        header("Content-type: application/json");
        $method = $_SERVER["REQUEST_METHOD"];
        $path = $_SERVER["PATH_INFO"] ?? "/";
        foreach (self::$routes as $route) {
            $pattern = "#^{$route["path"]}$#";
            if (
                $method === $route["method"] &&
                preg_match($pattern, $path, $params)
            ) {
                foreach ($route["middleware"] as $middleware) {
                    $instance = new $middleware;
                    $instance->index();
                }
                if (
                    http_response_code() === 200 ||
                    http_response_code() === 201
                ) {
                    $file_contents = file_get_contents("php://input");
                    $data = json_decode($file_contents, true) ?? [];
                    $_POST = array_merge([...$_POST, ...$data]);
                    $controller = new $route["controller"];
                    $handler = $route["handler"];
                    array_shift($params);
                    call_user_func_array(
                        [$controller, $handler],
                        $params
                    );
                }
                return;
            }
        }
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Not Found"
        ]);
    }
}
