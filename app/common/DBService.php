<?php
/**
 * The database service in common application.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

namespace app\common;

class DBService
{

    /**
     * The host for accessing database.
     *
     * @var string
     */
    private $host;

    /**
     * The port for accessing database.
     *
     * @var string
     */
    private $port;

    /**
     * The account name for accessing database.
     *
     * @var string
     */
    private $username;

    /**
     * The password for accessing database.
     *
     * @var string
     */
    private $password;

    /**
     * The database name for accessing database.
     *
     * @var string
     */
    private $database;

    /**
     * The charset for accessing database.
     *
     * @var string
     */
    private $charset;

    /**
     * The mysqli instance.
     *
     * @var \mysqli
     */
    private $mysqliObject;

    /**
     * Constructs a database service.
     *
     * @param string $host
     * @param string $port
     * @param string $username
     * @param string $password
     * @param string $database
     * @param string $charset
     */
    public function __construct(string $host, string $port, string $username, string $password, string $database, string $charset)
    {
        $this->host         = $host;
        $this->port         = $port;
        $this->username     = $username;
        $this->password     = $password;
        $this->database     = $database;
        $this->charset      = $charset;
        $this->mysqliObject = new \mysqli($this->host, $this->username, $this->password, $this->database, $this->port);
        $this->mysqliObject->set_charset($this->charset);
    }
}

