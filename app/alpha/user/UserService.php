<?php

namespace app\alpha\user;

use framework\Logger;

class UserService
{
    public function getUserId(int $userid): int
    {
        return ($userid * 100) + 5;
    }

    public function handleInsert(array $parsedCanalData): bool
    {
        Logger::logInfo("result(UserService -> handleInsert) : success");
        return true;
    }

    public function handleUpdate(array $parsedCanalData): bool
    {
        Logger::logInfo("result(UserService -> handleUpdate) : success");
        return true;
    }

    public function handleDelete(array $parsedCanalData): bool
    {
        Logger::logInfo("result(UserService -> handleDelete) : success");
        return true;
    }
}