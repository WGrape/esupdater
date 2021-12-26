<?php

namespace app\alpha\user;

use app\alpha\user;

class UserHandler
{
    public function onInsert(array $parsedCanalData): bool
    {
        return (new UserService())->handleInsert($parsedCanalData);
    }

    public function onUpdate(array $parsedCanalData): bool
    {
        return (new UserService())->handleUpdate($parsedCanalData);
    }

    public function onDelete(array $parsedCanalData): bool
    {
        return (new UserService())->handleDelete($parsedCanalData);
    }
}