<?php

/**
 * Class MailfireRequest
 * @property MailfireCurlRequest curlRequest
 */

class MailfireRequest extends MailfireDi
{
    const API_BASE = 'https://$clientId.api.mailfire.io/v1/';
    const API2_BASE = 'https://$clientId.api2.mailfire.io/';

    protected $apiBase;
    protected $api2Base;

    private $curlRequest = null;
    private $lastCurlResult = null;

    /**
     * MailfireRequest constructor.
     * @param $di
     */
    public function __construct($di)
    {
        parent::__construct($di);
        $this->setCurlRequest(new MailfireCurlRequest());
        $this->setApiBase($this->clientId);
        $this->setApi2Base($this->clientId);
    }

    /**
     * @param MailfireCurlRequest $curlRequest
     */
    public function setCurlRequest(MailfireCurlRequest $curlRequest)
    {
        $this->curlRequest = $curlRequest;
    }

    /**
     * @param $resource
     * @param array $data
     * @return bool
     */
    public function receive($resource, array $data = array())
    {
        return $this->send($resource, 'GET', $data);
    }

    /**
     * @param $resource
     * @param array $data
     * @return bool
     */
    public function create($resource, array $data = array())
    {
        return $this->send($resource, 'POST', $data);
    }

    /**
     * @param $resource
     * @param array $data
     * @return bool
     */
    public function update($resource, array $data)
    {
        return $this->send($resource, 'PUT', $data);
    }

    /**
     * @param $resource
     * @param array $data
     * @return bool
     */
    public function delete($resource, $data = array())
    {
        return $this->send($resource, 'DELETE', $data);
    }

    /**
     * @return MailfireResponse last request result
     */
    public function getLastResponse()
    {
        return new MailfireResponse($this->lastCurlResult);
    }

    /**
     * @param string $resource
     * @param string $method
     * @param array $data
     * @param string $apiBase
     * @return bool
     * @throws Exception
     */
    private function send($resource, $method, $data = array())
    {
        $method = strtoupper($method);
        $uri = self::API_BASE . $resource;

        $headers = array();

        $headers[] = 'Authorization: Basic ' . base64_encode($this->clientId . ':' . sha1($this->clientKey));

        $result = $this->sendCurl($uri, $method, $data, $headers);
        $this->lastCurlResult = $result;
        if ($result['code'] != 200) {
            $debugData = array(
                'uri' => $uri,
                'method' => $method,
                'data' => $data,
                'headers' => $headers
            );
            $exception = new Exception('Request failed: ' . json_encode($result) .
                ' Request data: ' . json_encode($debugData));
            $this->errorHandler->handle($exception);
            return false;
        }
        $result = json_decode($result['result'], true);
        if (!$result) {
            return false;
        }
        if (isset($result['data'])) {
            return $result['data'];
        }
        return true;
    }

    /**
     * @param $uri
     * @param $method
     * @param $data
     * @param $headers
     * @return array
     */
    private function sendCurl($uri, $method, $data, $headers)
    {
        $this->curlRequest->setOption(CURLOPT_URL, $uri);
        if (count($data)) {
            $this->curlRequest->setOption(CURLOPT_POSTFIELDS, json_encode($data));
        }
        $this->curlRequest->setOption(CURLOPT_HTTPHEADER, $headers);
        $this->curlRequest->setOption(CURLOPT_RETURNTRANSFER, 1);
        $this->curlRequest->setOption(CURLOPT_CUSTOMREQUEST, $method);

        $result = $this->curlRequest->execute();
        $code = $this->curlRequest->getInfo(CURLINFO_HTTP_CODE);

        $this->curlRequest->reset();

        return array(
            'result' => $result,
            'code' => $code
        );
    }

    public function sendToApi2($resource, $method, $data = array())
    {
        $uri = self::API2_BASE . $resource;

        $headers = $this->getApi2Headers();

        $result = $this->sendCurl($uri, $method, $data, $headers);
        $this->lastCurlResult = $result;
        if (substr($result['code'], 0, 1) != 2) { //2xx
            $debugData = array(
                'uri' => $uri,
                'method' => $method,
                'data' => $data,
                'headers' => $headers
            );
            $exception = new Exception('Request failed: ' . json_encode($result) .
                ' Request data: ' . json_encode($debugData));
            $this->errorHandler->handle($exception);
            return false;
        }
        $result = json_decode($result['result'], true);
        if (!$result) {
            return false;
        }
        if (isset($result['data'])) {
            return $result['data'];
        }
        return true;
    }

    /**
     * @return array
     */
    private function getApi2Headers()
    {
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($this->clientId . ':' . sha1($this->clientKey))
        );

        return $headers;
    }

    public function setOption($name, $value, $permanentOption = false)
    {
        $this->curlRequest->setOption($name, $value, $permanentOption);
    }

    public function resetOptions()
    {
        $this->curlRequest->reset();
    }

    public function resetPermanentOptions()
    {
        $this->curlRequest->resetPermanentOptions();
    }

    protected function setApiBase($clientId)
    {
        $this->apiBase = str_replace('$clientId', $clientId, self::API_BASE);
    }

    protected function setApi2Base($clientId)
    {
        $this->api2Base = str_replace('$clientId', $clientId, self::API2_BASE);
    }

}
