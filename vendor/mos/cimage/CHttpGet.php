<?php
/**
 * Get a image from a remote server using HTTP GET and If-Modified-Since.
 *
 */
class CHttpGet
{
    private $request  = array();
    private $response = array();



    /**
    * Constructor
    *
    */
    public function __construct()
    {
        $this->request['header'] = array();
    }



    /**
     * Build an encoded url.
     *
     * @param string $baseUrl This is the original url which will be merged.
     * @param string $merge   Thse parts should be merged into the baseUrl,
     *                        the format is as parse_url.
     *
     * @return string $url as the modified url.
     */
    public function buildUrl($baseUrl, $merge)
    {
        $parts = parse_url($baseUrl);
        $parts = array_merge($parts, $merge);

        $url  = $parts['scheme'];
        $url .= "://";
        $url .= $parts['host'];
        $url .= isset($parts['port'])
            ? ":" . $parts['port']
            : "" ;
        $url .= $parts['path'];

        return $url;
    }



    /**
     * Set the url for the request.
     *
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $parts = parse_url($url);
        
        $path = "";
        if (isset($parts['path'])) {
            $pathParts = explode('/', $parts['path']);
            unset($pathParts[0]);
            foreach ($pathParts as $value) {
                $path .= "/" . rawurlencode($value);
            }
        }
        $url = $this->buildUrl($url, array("path" => $path));

        $this->request['url'] = $url;
        return $this;
    }



    /**
     * Set custom header field for the request.
     *
     * @param string $field
     * @param string $value
     *
     * @return $this
     */
    public function setHeader($field, $value)
    {
        $this->request['header'][] = "$field: $value";
        return $this;
    }



    /**
     * Set header fields for the request.
     *
     * @param string $field
     * @param string $value
     *
     * @return $this
     */
    public function parseHeader()
    {
        //$header = explode("\r\n", rtrim($this->response['headerRaw'], "\r\n"));
        
        $rawHeaders = rtrim($this->response['headerRaw'], "\r\n");
        # Handle multiple responses e.g. with redirections (proxies too)
        $headerGroups = explode("\r\n\r\n", $rawHeaders);
        # We're only interested in the last one
        $header = explode("\r\n", end($headerGroups));

        $output = array();

        if ('HTTP' === substr($header[0], 0, 4)) {
            list($output['version'], $output['status']) = explode(' ', $header[0]);
            unset($header[0]);
        }

        foreach ($header as $entry) {
            $pos = strpos($entry, ':');
            $output[trim(substr($entry, 0, $pos))] = trim(substr($entry, $pos + 1));
        }

        $this->response['header'] = $output;
        return $this;
    }



    /**
     * Perform the request.
     *
     * @param boolean $debug set to true to dump headers.
     *
     * @throws Exception when curl fails to retrieve url.
     *
     * @return boolean
     */
    public function doGet($debug = false)
    {
        $options = array(
            CURLOPT_URL             => $this->request['url'],
            CURLOPT_HEADER          => 1,
            CURLOPT_HTTPHEADER      => $this->request['header'],
            CURLOPT_AUTOREFERER     => true,
            CURLOPT_RETURNTRANSFER  => true,
            CURLINFO_HEADER_OUT     => $debug,
            CURLOPT_CONNECTTIMEOUT  => 5,
            CURLOPT_TIMEOUT         => 5,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_MAXREDIRS       => 2,
        );

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);

        if (!$response) {
            throw new Exception("Failed retrieving url, details follows: " . curl_error($ch));
        }

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $this->response['headerRaw'] = substr($response, 0, $headerSize);
        $this->response['body']      = substr($response, $headerSize);

        $this->parseHeader();

        if ($debug) {
            $info = curl_getinfo($ch);
            echo "Request header<br><pre>", var_dump($info['request_header']), "</pre>";
            echo "Response header (raw)<br><pre>", var_dump($this->response['headerRaw']), "</pre>";
            echo "Response header (parsed)<br><pre>", var_dump($this->response['header']), "</pre>";
        }

        curl_close($ch);
        return true;
    }



    /**
     * Get HTTP code of response.
     *
     * @return integer as HTTP status code or null if not available.
     */
    public function getStatus()
    {
        return isset($this->response['header']['status'])
            ? (int) $this->response['header']['status']
            : null;
    }



    /**
     * Get file modification time of response.
     *
     * @return int as timestamp.
     */
    public function getLastModified()
    {
        return isset($this->response['header']['Last-Modified'])
            ? strtotime($this->response['header']['Last-Modified'])
            : null;
    }



    /**
     * Get content type.
     *
     * @return string as the content type or null if not existing or invalid.
     */
    public function getContentType()
    {
        $type = isset($this->response['header']['Content-Type'])
            ? $this->response['header']['Content-Type']
            : '';

        return preg_match('#[a-z]+/[a-z]+#', $type)
            ? $type
            : null;
    }



    /**
     * Get file modification time of response.
     *
     * @param mixed $default as default value (int seconds) if date is
     *                       missing in response header.
     *
     * @return int as timestamp or $default if Date is missing in
     *             response header.
     */
    public function getDate($default = false)
    {
        return isset($this->response['header']['Date'])
            ? strtotime($this->response['header']['Date'])
            : $default;
    }



    /**
     * Get max age of cachable item.
     *
     * @param mixed $default as default value if date is missing in response
     *                       header.
     *
     * @return int as timestamp or false if not available.
     */
    public function getMaxAge($default = false)
    {
        $cacheControl = isset($this->response['header']['Cache-Control'])
            ? $this->response['header']['Cache-Control']
            : null;

        $maxAge = null;
        if ($cacheControl) {
            // max-age=2592000
            $part = explode('=', $cacheControl);
            $maxAge = ($part[0] == "max-age")
                ? (int) $part[1]
                : null;
        }

        if ($maxAge) {
            return $maxAge;
        }

        $expire = isset($this->response['header']['Expires'])
            ? strtotime($this->response['header']['Expires'])
            : null;

        return $expire ? $expire : $default;
    }



    /**
     * Get body of response.
     *
     * @return string as body.
     */
    public function getBody()
    {
        return $this->response['body'];
    }
}
