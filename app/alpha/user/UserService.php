<?php
/**
 * The service of user event module in alpha application.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

namespace app\alpha\user;

use framework\Logger;

class UserService
{
    /**
     * Get userid of user.
     *
     * @param int $userid
     *
     * @return int
     */
    public function getUserId(int $userid): int
    {
        return ($userid * 100) + 5;
    }

    /**
     * Do insert business things.
     *
     * @param array $parsedCanalData
     *
     * @return bool
     */
    public function doInsert(array $parsedCanalData): bool
    {
        Logger::logInfo("result(UserService -> doInsert) : success");
        return true;
    }

    /**
     * Do update business things.
     *
     * @param array $parsedCanalData
     *
     * @return bool
     */
    public function doUpdate(array $parsedCanalData): bool
    {
        Logger::logInfo("result(UserService -> doUpdate) : success");
        return true;
    }

    /**
     * Do delete business things.
     *
     * @param array $parsedCanalData
     *
     * @return bool
     */
    public function doDelete(array $parsedCanalData): bool
    {
        Logger::logInfo("result(UserService -> doDelete) : success");
        return true;
    }
}