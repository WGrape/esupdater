<?php

namespace app\alpha\controllers\user;

use app\alpha\services\user\UserService;
use app\core\controllers\BaseController;

class UserController extends BaseController
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