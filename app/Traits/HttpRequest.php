<?php
/**
 * Summary
 * Description http request wrapper
 *
 * @package
 * @author    Wang Xi <wangxi@yoyohr.com>
 * @version
 * Date     2016/11/10
 */
namespace App\Traits;

use Illuminate\Http\Request;
use Exception;
use GuzzleHttp\Client;
use Log;

trait HttpRequest
{


    protected $httpHeader = [];
    protected $httpBody = [];
    protected $urlPrefix = '';

    public function setUrlPrefix($prefix = '')
    {
        $this->urlPrefix = empty($prefix) ? getenv('TRACER_REQUEST_URL') : $prefix;
        return $this;
    }

    /**
     * @param Request $request
     * @param         $url
     * @param array $data
     * @param string $method http method
     * @param bool $force_auth 是否校验登录
     * @return \Psr\Http\Message\StreamInterface
     * @throws Exception
     */
    protected function req(Request $request, $url, $data = [], $method = 'GET', $force_auth = false, $Content_Type = '')
    {
        try {
            if (strtolower($method) == 'get') {
                $url = $this->_normalizeUrl($request, $url);
            }
            $client = new Client();
            $options = [
                'headers' => $this->httpHeader,
                'form_params' => $this->httpBody
            ];
            if ($method == 'POST' || $method == 'PUT'|| $method == 'DELETE') {
                $this->httpHeader = array_merge($this->httpHeader, ['Content-Type' => 'application/json']);
                $options = [
                    'headers' => $this->httpHeader,
                    'json' => $this->httpBody
                ];
            }
            if (!empty($Content_Type)) {
                $this->httpHeader = array_merge($this->httpHeader, ['Content-Type' => $Content_Type]);
                if (strtolower($Content_Type) == 'application/json') {
                    $options = [
                        'headers' => $this->httpHeader,
                        'json' => $this->httpBody
                    ];
                }
            }
            if (empty($this->urlPrefix)) {
                $this->setUrlPrefix();
            }
//            return $this->httpHeader;
            $req_url = rtrim($this->urlPrefix, '/') . '/' . ltrim($url, '/');
            $res = $client->request($method, $req_url, $options);

            return $res->getBody();
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return response('message', $e->getMessage());
        }
    }

    private function _normalizeUrl(Request $request, $url = '')
    {
        $fullurl = $request->fullUrl();
        if (false !== strpos($fullurl, '?')) {
            $url = $url . '?' . explode("?", $fullurl)[1];
        }
        return $url;
    }

    public function doGet($request, $url)
    {
        return $this->req($request, $url);
    }

    public function doPost($request, $url, $data = [], $Content_Type = '')
    {
        return $this->req($request, $url, $data, 'POST', true, $Content_Type);
    }

    public function doPut($request, $url, $data = [])
    {
        return $this->req($request, $url, $data, 'PUT', true);
    }

    public function doDelete($request, $url)
    {
        return $this->req($request, $url, [], 'DELETE', true);
    }

}