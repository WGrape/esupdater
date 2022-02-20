<?php
/**
 * The handler of user event module in alpha application.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

namespace app\alpha\user;

class UserHandler
{
    /**
     * The event callback when table of user trigger insert event.
     * @param array $parsedCanalData
     * @return bool
     */
    public function onInsert(array $parsedCanalData): bool
    {
        return (new UserService())->doInsert($parsedCanalData);
    }

    /**
     * The event callback when table of user trigger update event.
     * @param array $parsedCanalData
     * @return bool
     */
    public function onUpdate(array $parsedCanalData): bool
    {
        return (new UserService())->doUpdate($parsedCanalData);
    }

    /**
     * The event callback when table of user trigger delete event.
     * @param array $parsedCanalData
     * @return bool
     */
    public function onDelete(array $parsedCanalData): bool
    {
        return (new UserService())->doDelete($parsedCanalData);
    }
}
