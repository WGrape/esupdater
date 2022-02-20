<?php
/**
 * The service of user event module in alpha application.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

namespace app\alpha\user;

class UserService
{
    /**
     * Update different indexes when insert.
     * @param array $parsedCanalData
     * @return bool
     */
    public function doInsert(array $parsedCanalData): bool
    {
        $success = true;

        if (!$this->updateAIndexWhenInsert($parsedCanalData)) {
            $success = false;
        }

        if (!$this->updateBIndexWhenInsert($parsedCanalData)) {
            $success = false;
        }

        // update some other indexes...

        return $success;
    }

    /**
     * Update different indexes when update.
     * @param array $parsedCanalData
     * @return bool
     */
    public function doUpdate(array $parsedCanalData): bool
    {
        $success = true;

        if (!$this->updateAIndexWhenUpdate($parsedCanalData)) {
            $success = false;
        }

        if (!$this->updateBIndexWhenUpdate($parsedCanalData)) {
            $success = false;
        }

        // update some other indexes...

        return $success;
    }

    /**
     * Update different indexes when delete.
     * @param array $parsedCanalData
     * @return bool
     */
    public function doDelete(array $parsedCanalData): bool
    {
        $success = true;

        if (!$this->updateAIndexWhenDelete($parsedCanalData)) {
            $success = false;
        }

        if (!$this->updateBIndexWhenDelete($parsedCanalData)) {
            $success = false;
        }

        // update some other indexes...

        return $success;
    }

    /**
     * Update a index: event of insert
     * @param array $parsedCanalData
     * @return bool
     */
    public function updateAIndexWhenInsert(array $parsedCanalData): bool
    {
        return true;
    }

    /**
     * Update b index: event of insert
     * @param array $parsedCanalData
     * @return bool
     */
    public function updateBIndexWhenInsert(array $parsedCanalData): bool
    {
        return true;
    }

    /**
     * Update a index: event of update
     * @param array $parsedCanalData
     * @return bool
     */
    public function updateAIndexWhenUpdate(array $parsedCanalData): bool
    {
        return true;
    }

    /**
     * Update b index: event of update
     * @param array $parsedCanalData
     * @return bool
     */
    public function updateBIndexWhenUpdate(array $parsedCanalData): bool
    {
        return true;
    }

    /**
     * Update a index: event of delete
     * @param array $parsedCanalData
     * @return bool
     */
    public function updateAIndexWhenDelete(array $parsedCanalData): bool
    {
        return true;
    }

    /**
     * Update b index: event of delete
     * @param array $parsedCanalData
     * @return bool
     */
    public function updateBIndexWhenDelete(array $parsedCanalData): bool
    {
        return true;
    }

    /**
     * Business method: Get userid.
     * @param int $userid
     * @return int
     */
    public function getUserId(int $userid): int
    {
        return ($userid * 100) + 5;
    }
}
