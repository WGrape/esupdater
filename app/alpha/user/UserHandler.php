<?php
/**
 * The handler of user event module in alpha application.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

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