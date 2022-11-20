<?php

namespace App\Util;

use Error;
use Throwable;
use App\Config\Config;
use App\Model\UserModel;
use Firebase\JWT\{JWT, Key};

class Authenticate
{
    public static function sign(array $payload): string
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + 120;
        $payload["iat"] = $issuedAt;
        $payload["exp"] = $expirationTime;
        $token = JWT::encode(
            $payload,
            Config::SECRET_KEY,
            "HS256"
        );
        setcookie(
            "X-PRA-TOKEN",
            $token,
            $expirationTime,
            "",
            "",
            false,
            true
        );
        return $token;
    }

    public static function verify(): array
    {
        try {
            if (!isset($_COOKIE["X-PRA-TOKEN"])) {
                throw new Error("Unauthorized");
            }
            $token = $_COOKIE["X-PRA-TOKEN"];
            $userModel = new UserModel();
            $userExist = $userModel->getByToken($token);
            if (!$userExist) throw new Error("Unauthorized");
            $key = new Key(Config::SECRET_KEY, "HS256");
            $decoded = (array) JWT::decode($token, $key);
            $fnMatch = $decoded["user_fullname"] === $userExist["user_fullname"];
            $unMatch = $decoded["user_username"] === $userExist["user_username"];
            if (!$fnMatch || !$unMatch) throw new Error("Unauthorized");
            return $userExist;
        } catch (Throwable $th) {
            if (isset($_COOKIE["X-PRA-TOKEN"])) {
                unset($_COOKIE["X-PRA-TOKEN"]);
                setcookie("X-PRA-TOKEN", "", time() - 120);
            }
            throw $th;
        }
    }
}
