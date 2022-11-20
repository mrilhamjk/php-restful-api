<?php

namespace App\Model;

use DateTime;
use App\Util\Database;

class UserModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getByUsername(string $user_username): mixed
    {
        $this->db->query("SELECT * FROM users
         WHERE user_username=:user_username");
        $this->db->bind("user_username", $user_username);
        return $this->db->fetch();
    }

    public function getByToken(string $user_token): mixed
    {
        $this->db->query("SELECT * FROM users
         WHERE user_token=:user_token");
        $this->db->bind("user_token", $user_token);
        return $this->db->fetch();
    }

    public function getAll(): array
    {
        $this->db->query("SELECT * FROM users");
        return $this->db->fetchAll();
    }

    public function create(array $data): int
    {
        $dateTime = new DateTime();
        $data["user_created_at"] = $dateTime->format("d-m-Y H:i:s");
        $data["user_updated_at"] = $dateTime->format("d-m-Y H:i:s");
        $this->db->query("INSERT INTO users VALUES
        (null, :user_fullname, :user_username, :user_password,
        :user_token, :user_created_at, :user_updated_at)");
        $this->db->bind("user_fullname", $data["user_fullname"]);
        $this->db->bind("user_username", $data["user_username"]);
        $this->db->bind("user_password", $data["user_password"]);
        $this->db->bind("user_token", $data["user_token"]);
        $this->db->bind("user_created_at", $data["user_created_at"]);
        $this->db->bind("user_updated_at", $data["user_updated_at"]);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function update(array $data): int
    {
        $dateTime = new DateTime();
        $data["user_updated_at"] = $dateTime->format("d-m-Y H:i:s");
        $this->db->query("UPDATE users SET user_fullname=:user_fullname,
        user_username=:user_username, user_password=:user_password,
        user_token=:user_token, user_created_at=:user_created_at,
        user_updated_at=:user_updated_at WHERE user_id=:user_id");
        $this->db->bind("user_fullname", $data["user_fullname"]);
        $this->db->bind("user_username", $data["user_username"]);
        $this->db->bind("user_password", $data["user_password"]);
        $this->db->bind("user_token", $data["user_token"]);
        $this->db->bind("user_created_at", $data["user_created_at"]);
        $this->db->bind("user_updated_at", $data["user_updated_at"]);
        $this->db->bind("user_id", $data["user_id"]);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function delete(int|string $user_id): int
    {
        $this->db->query("DELETE FROM users
         WHERE user_id=:user_id");
        $this->db->bind("user_id", $user_id);
        $this->db->execute();
        return $this->db->rowCount();
    }
}
