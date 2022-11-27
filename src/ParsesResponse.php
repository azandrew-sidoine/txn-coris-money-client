<?php

namespace Drewlabs\Txn\Coris;

use Drewlabs\Curl\Converters\JSONDecoder;

trait ParsesResponse
{

    /**
     * Parse request string headers
     * 
     * @param mixed $list 
     * 
     * @return array 
     */
    private function parseHeaders($list)
    {
        $list = preg_split('/\r\n/', (string) ($list ?? ''), -1, PREG_SPLIT_NO_EMPTY);
        $httpHeaders = [];
        $httpHeaders['Request-Line'] = reset($list) ?? '';
        for ($i = 1; $i < count($list); $i++) {
            if (strpos($list[$i], ':') !== false) {
                list($key, $value) = array_map(function ($item) {
                    return $item ? trim($item) : null;
                }, explode(':', $list[$i], 2));
                $httpHeaders[$key] = $value;
            }
        }
        return $httpHeaders;
    }

    /**
     * Get request header caseless
     * 
     * @param array $headers 
     * @param string $name 
     * @return string
     */
    private function getHeader(array $headers, string $name)
    {
        if (empty($headers)) {
            return null;
        }
        $normalized = strtolower($name);
        foreach ($headers as $key => $header) {
            if (strtolower($key) === $normalized) {
                return implode(',', is_array($header) ? $header : [$header]);
            }
        }
        return null;
    }



    /**
     * Decode request response
     * 
     * @param string $response 
     * @param array $headers 
     * @return array
     * 
     * @throws JsonException 
     */
    private function decodeRequestResponse(string $response, array $headers = [])
    {
        $result = null;
        if (RegExp::matchJson($this->getHeader($headers, 'content-type'))) {
            $result = (new JSONDecoder)->decode($response);
        }
        // If the Content-Type header is not present in the response headers, we apply the try catch clause
        // To insure no error is thrown when decoding.
        if (null === $result) {
            try {
                $result = (new JSONDecoder)->decode($response) ?? [];
            } catch (\Throwable $e) {
                $result = [];
            }
        }
        return (array)($result ?? []);
    }
}
