<?php

/**
 * Class MailfireRequest
 * @property MailfireCurlRequest curlRequest
 */

class MailfireRequest extends MailfireDi
{
    const API_BASE = 'https://api.mailfire.io/v1/';
    const API2_BASE = 'https://api2.mailfire.io/';
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
        $sign = $this->getSign($uri, $method, $data);
        if (false === $sign) {
            return false;
        }
        $headers[] = 'Authorization: Sign ' . $sign;
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

    /**
     * @param $uri
     * @param $method
     * @param $data
     * @return string
     */
    private function getSign($uri, $method, $data)
    {
        $data['request_method'] = $method;
        $data['request_uri'] = $uri;
        ksort($data);

        if (PHP_VERSION_ID >= 50400) {
            $unescaped = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            //There is no JSON_UNESCAPED_SLASHES and JSON_UNESCAPED_UNICODE in php5.3
            $unescaped = $this->jsonEncodeUnescapedUnicode($data);
            //simulate JSON_UNESCAPED_SLASHES
            $unescaped = str_replace('\\/', '/', $unescaped);
        }

        $sign = hash_hmac('sha256', $unescaped, $this->clientKey);

        $signData = json_encode(array('client_id' => $this->clientId, 'sign' => $sign));
        return base64_encode($signData);
    }

    /**
     * Emulate JSON_UNESCAPED_UNICODE
     * @param $arr
     * @return bool|string
     * @throws Exception
     */
    private function jsonEncodeUnescapedUnicode($arr)
    {
        if (!function_exists('mb_encode_numericentity')) {
            $exception = new Exception('Ext mbstring is required');
            $this->errorHandler->handle($exception);
            return false;
        }
        //convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
        array_walk_recursive($arr, function (&$item, $key) {
            if (is_string($item)) {
                $item = mb_encode_numericentity($item, array(0x80, 0xffff, 0, 0xffff), 'UTF-8');
            }
        });
        return mb_decode_numericentity(json_encode($arr), array(0x80, 0xffff, 0, 0xffff), 'UTF-8');

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

}
