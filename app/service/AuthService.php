<?php

namespace App\Service;

use Throwable;
use App\Model\UserModel;
use App\Util\{
    Authenticate,
    CustomError
};

class AuthService
{
    public UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function signup(): array
    {
        $result = [];
        try {
            $fullname = $_POST["fullname"] ?? null;
            $username = $_POST["username"] ?? null;
            $password = $_POST["password"] ?? null;
            if (!isset($fullname) || $fullname === "") {
                throw new CustomError("Name is required", 422);
            } else if (!isset($username) || $username === "") {
                throw new CustomError("Username is required", 422);
            } else if (!isset($password) || $password === "") {
                throw new CustomError("Password is required", 422);
            } else if ($this->userModel->getByUsername($username)) {
                throw new CustomError("Username is not available", 422);
            } else if (strlen($password) < 5) {
                throw new CustomError("Password length min is 5", 422);
            }

            $password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );
            $userCreated = $this->userModel->create([
                "user_fullname" => $fullname,
                "user_username" => $username,
                "user_password" => $password,
                "user_token" => null
            ]);
            if ($userCreated) {
                $user = $this->userModel
                    ->getByUsername($username);
                unset($user["user_password"]);
                unset($user["user_token"]);
                $result["user"] = $user;
                $result["code"] = 201;
                $result["success"] = true;
                $result["message"] = "Signup successful";
            } else throw new CustomError("Signup failed");
        } catch (CustomError $ce) {
            $result["code"] = $ce->getStatusCode();
            $result["success"] = false;
            $result["message"] = $ce->getMessage();
        } catch (Throwable $th) {
            $result["code"] = 400;
            $result["success"] = false;
            $result["message"] = $th->getMessage();
        }
        return $result;
    }

    public function signin(): array
    {
        $result = [];
        try {
            $username = $_POST["username"] ?? null;
            $password = $_POST["password"] ?? null;
            if (!isset($username) || $username === "") {
                throw new CustomError("Username is required", 422);
            } else if (!isset($password) || $password === "") {
                throw new CustomError("Password is required", 422);
            }

            $userExist = $this->userModel->getByUsername($username);
            if (!$userExist) throw new CustomError("Username not found", 404);
            $passwordMatch = password_verify($password, $userExist["user_password"]);
            if (!$passwordMatch) throw new CustomError("Password does not match", 400);
            $token = Authenticate::sign([
                "user_fullname" => $userExist["user_fullname"],
                "user_username" => $userExist["user_username"]
            ]);
            $userExist["user_token"] = $token;
            $userUpdated = $this->userModel
                ->update($userExist);
            if ($userUpdated) {
                $user = $this->userModel
                    ->getByUsername($username);
                unset($user["user_password"]);
                unset($user["user_token"]);
                $result["user"] = $user;
                $result["code"] = 200;
                $result["success"] = true;
                $result["message"] = "Signin successful";
            } else throw new CustomError("Signin failed");
        } catch (CustomError $ce) {
            $result["code"] = $ce->getStatusCode();
            $result["success"] = false;
            $result["message"] = $ce->getMessage();
        } catch (Throwable $th) {
            $result["code"] = 400;
            $result["success"] = false;
            $result["message"] = $th->getMessage();
        }
        return $result;
    }

    public function signout(): array
    {
        $result = [];
        try {
            $userLoggedIn = Authenticate::verify();
            $userLoggedIn["user_token"] = null;
            $userUpdated = $this->userModel
                ->update($userLoggedIn);
            if ($userUpdated) {
                if (isset($_COOKIE["X-PRA-TOKEN"])) {
                    unset($_COOKIE["X-PRA-TOKEN"]);
                    setcookie("X-PRA-TOKEN", "", time() - 120);
                }
                $result["code"] = 200;
                $result["success"] = true;
                $result["message"] = "Signout successful";
            } else throw new CustomError("Signout failed");
        } catch (CustomError $ce) {
            $result["code"] = $ce->getStatusCode();
            $result["success"] = false;
            $result["message"] = $ce->getMessage();
        } catch (Throwable $th) {
            $result["code"] = 400;
            $result["success"] = false;
            $result["message"] = $th->getMessage();
        }
        return $result;
    }
}
