<?php
/**
 * The handler of Account event module in alpha application.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

namespace app\alpha\account;

class AccountHandler
{
    /**
     * The event callback when table of Account trigger insert event.
     * @param array $parsedCanalData
     * @return bool
     */
    public function onInsert(array $parsedCanalData): bool
    {
        return (new AccountService())->doInsert($parsedCanalData);
    }

    /**
     * The event callback when table of Account trigger update event.
     * @param array $parsedCanalData
     * @return bool
     */
    public function onUpdate(array $parsedCanalData): bool
    {
        return (new AccountService())->doUpdate($parsedCanalData);
    }

    /**
     * The event callback when table of Account trigger delete event.
     * @param array $parsedCanalData
     * @return bool
     */
    public function onDelete(array $parsedCanalData): bool
    {
        return (new AccountService())->doDelete($parsedCanalData);
    }
}
