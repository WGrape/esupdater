<?php
/**
 * The elasticsearch service in common application.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

namespace app\common;

use framework\Logger;

class ESService
{
    public $host;
    public $port;
    public $userPassword;
    public $documentType;

    public $index;

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    public function __construct($index)
    {
        global $es;
        $this->host         = $es['host'];
        $this->port         = $es['port'];
        $this->userPassword = $es['user_password'];
        $this->documentType = $es['doc_type'];

        $this->index = $index;
    }

    public function isNeedToUpdate(array $changedFieldList, array $needToUpdateFieldList): bool
    {
        $result = array_intersect($changedFieldList, $needToUpdateFieldList);
        return !empty($result);
    }

    public function updateDoc($documentId, $updateList, $upsert = false): bool
    {
        $url     = "{$this->host}:{$this->port}/{$this->index}/{$this->documentType}/{$documentId}/_update";
        $message = json_encode([
            'docId'      => $documentId,
            'updateData' => $updateList,
            'url'        => $url,
        ], JSON_UNESCAPED_UNICODE);
        Logger::logInfo("updateDoc data : {$message}");

        $updateData = [
            'doc' => $updateList,
        ];
        if ($upsert) {
            $updateData['doc_as_upsert'] = true;
        }
        $data = json_encode($updateData);
        return $this->curlRequest($url, self::METHOD_POST, $data);
    }

    public function putDoc($documentId, $document): bool
    {
        $url     = "{$this->host}:{$this->port}/{$this->index}/{$this->documentType}/{$documentId}";
        $message = json_encode([
            'docId' => $documentId,
            'doc'   => $document,
            'url'   => $url,
        ], JSON_UNESCAPED_UNICODE);
        Logger::logInfo("putDoc data : {$message}");

        $data = json_encode($document);
        return $this->curlRequest($url, self::METHOD_PUT, $data);
    }

    public function isSuccess(int $httpCode): bool
    {
        return intval($httpCode) >= 200 && intval($httpCode) < 300;
    }

    public function curlRequest(string $url, string $method, string $data): bool
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (!empty($this->userPassword)) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->userPassword);
        }

        switch ($method) {
            case self::METHOD_POST:
                curl_setopt($ch, CURLOPT_POST, true);
            // can't break here
            case self::METHOD_GET:
            case self::METHOD_PUT:
            case self::METHOD_DELETE:
                $httpHeader = [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data)
                ];

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
        }

        $response  = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode  = intval(curl_getinfo($ch, CURLINFO_HTTP_CODE));
        curl_close($ch);

        $message = json_encode([
            'url'       => $url,
            'method'    => $method,
            'data'      => substr($data, 0, 2000),
            'curlError' => $curlError,
            'httpCode'  => $httpCode,
            'response'  => $response,
        ], JSON_UNESCAPED_UNICODE);
        Logger::logInfo("curlRequest data : {$message}");

        $isSuccess = $this->isSuccess($httpCode);
        if (!$isSuccess) {
            Logger::logError("curlRequest error");
        }
        return $isSuccess;
    }
}