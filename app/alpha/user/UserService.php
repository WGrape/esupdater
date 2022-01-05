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
     */
    public function doInsert(array $parsedCanalData)
    {
        $this->updateAIndexWhenInsert($parsedCanalData);

        $this->updateBIndexWhenInsert($parsedCanalData);

        // update some other indexes...
    }

    /**
     * Update different indexes when update.
     * @param array $parsedCanalData
     */
    public function doUpdate(array $parsedCanalData)
    {
        $this->updateAIndexWhenUpdate($parsedCanalData);

        $this->updateBIndexWhenUpdate($parsedCanalData);

        // update some other indexes...
    }

    /**
     * Update different indexes when delete.
     * @param array $parsedCanalData
     */
    public function doDelete(array $parsedCanalData)
    {
        $this->updateAIndexWhenDelete($parsedCanalData);

        $this->updateBIndexWhenDelete($parsedCanalData);

        // update some other indexes...
    }

    /**
     * Update a index: event of insert
     * @param array $parsedCanalData
     */
    public function updateAIndexWhenInsert(array $parsedCanalData)
    {

    }

    /**
     * Update b index: event of insert
     * @param array $parsedCanalData
     */
    public function updateBIndexWhenInsert(array $parsedCanalData)
    {

    }

    /**
     * Update a index: event of update
     * @param array $parsedCanalData
     */
    public function updateAIndexWhenUpdate(array $parsedCanalData)
    {

    }

    /**
     * Update b index: event of update
     * @param array $parsedCanalData
     */
    public function updateBIndexWhenUpdate(array $parsedCanalData)
    {

    }

    /**
     * Update a index: event of delete
     * @param array $parsedCanalData
     */
    public function updateAIndexWhenDelete(array $parsedCanalData)
    {

    }

    /**
     * Update b index: event of delete
     * @param array $parsedCanalData
     */
    public function updateBIndexWhenDelete(array $parsedCanalData)
    {

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
