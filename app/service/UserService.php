<?php

namespace App\Service;

use Throwable;
use App\Model\UserModel;
use App\Util\{
    Authenticate,
    CustomError
};

class UserService
{
    public UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index(): array
    {
        $result = [];
        try {
            $userLoggedIn = Authenticate::verify();
            unset($userLoggedIn["user_password"]);
            unset($userLoggedIn["user_token"]);
            $result["user"] = $userLoggedIn;
            $result["code"] = 200;
            $result["success"] = true;
            $result["message"] = "Get user successful";
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

    public function delete(): array
    {
        $result = [];
        try {
            $userLoggedIn = Authenticate::verify();
            $userDeleted = $this->userModel
                ->delete($userLoggedIn["user_id"]);
            if ($userDeleted) {
                if (isset($_COOKIE["X-PRA-TOKEN"])) {
                    unset($_COOKIE["X-PRA-TOKEN"]);
                    setcookie("X-PRA-TOKEN", "", time() - 120);
                }
                $result["code"] = 200;
                $result["success"] = true;
                $result["message"] = "Delete user successful";
            } else throw new CustomError("Delete user failed");
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
