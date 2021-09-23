<?php
/**
 * Project ip-helper
 * Created by PhpStorm
 * User: 713uk13m <dev@nguyenanhung.com>
 * Copyright: 713uk13m <dev@nguyenanhung.com>
 * Date: 09/20/2021
 * Time: 22:13
 */

namespace nguyenanhung\Libraries\IP;

use Exception;
use IPv4\SubnetCalculator;
use IPLib\Factory;

if (!class_exists('nguyenanhung\Libraries\IP\IP')) {
    /**
     * Class IP
     *
     * @package   nguyenanhung\Libraries\IP
     * @author    713uk13m <dev@nguyenanhung.com>
     * @copyright 713uk13m <dev@nguyenanhung.com>
     */
    class IP
    {
        use Version;

        /** @var bool Cấu hình class có nhận IP theo HA Proxy hay không */
        protected $haProxyStatus;

        /**
         * IP constructor.
         *
         * @author   : 713uk13m <dev@nguyenanhung.com>
         * @copyright: 713uk13m <dev@nguyenanhung.com>
         */
        public function __construct()
        {
        }

        /**
         * Function setHaProxy
         *
         * @param bool $haProxyStatus
         *
         * @return $this
         * @author   : 713uk13m <dev@nguyenanhung.com>
         * @copyright: 713uk13m <dev@nguyenanhung.com>
         * @time     : 08/08/2020 44:11
         */
        public function setHaProxy(bool $haProxyStatus = false): IP
        {
            $this->haProxyStatus = $haProxyStatus;

            return $this;
        }

        /**
         * Function getIpAddress
         *
         * @param bool $convertToInteger
         *
         * @return bool|int|string
         * @author   : 713uk13m <dev@nguyenanhung.com>
         * @copyright: 713uk13m <dev@nguyenanhung.com>
         * @time     : 08/08/2020 43:50
         */
        public function getIpAddress(bool $convertToInteger = false)
        {
            if ($this->haProxyStatus === true) {
                $ip = $this->getIpByHaProxy($convertToInteger);
            } else {
                $ip = $this->getRawIpAddress($convertToInteger);
            }

            return $ip;
        }

        /**
         * Function getIpByHaProxy
         *
         * @param bool $convertToInteger
         *
         * @return false|int|string
         * @author   : 713uk13m <dev@nguyenanhung.com>
         * @copyright: 713uk13m <dev@nguyenanhung.com>
         * @time     : 09/07/2021 58:38
         */
        public function getIpByHaProxy(bool $convertToInteger = false)
        {
            $key = 'HTTP_X_FORWARDED_FOR';
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if ($this->ipValidate($ip)) {
                        if ($convertToInteger === true) {
                            return ip2long($ip);
                        }

                        return $ip;
                    }
                }
            }

            return false;
        }

        /**
         * Function getRawIpAddress
         *
         * @param bool $convertToInteger
         *
         * @return false|int|string
         * @author   : 713uk13m <dev@nguyenanhung.com>
         * @copyright: 713uk13m <dev@nguyenanhung.com>
         * @time     : 09/07/2021 58:45
         */
        public function getRawIpAddress(bool $convertToInteger = false)
        {
            $ip_keys = array(
                0 => 'HTTP_CF_CONNECTING_IP',
                1 => 'HTTP_X_FORWARDED_FOR',
                2 => 'HTTP_X_FORWARDED',
                3 => 'HTTP_X_IPADDRESS',
                4 => 'HTTP_X_CLUSTER_CLIENT_IP',
                5 => 'HTTP_FORWARDED_FOR',
                6 => 'HTTP_FORWARDED',
                7 => 'HTTP_CLIENT_IP',
                8 => 'HTTP_IP',
                9 => 'REMOTE_ADDR'
            );
            foreach ($ip_keys as $key) {
                if (array_key_exists($key, $_SERVER) === true) {
                    foreach (explode(',', $_SERVER[$key]) as $ip) {
                        $ip = trim($ip);
                        if ($this->ipValidate($ip)) {
                            if ($convertToInteger === true) {
                                return ip2long($ip);
                            }

                            return $ip;
                        }
                    }
                }
            }

            return false;
        }

        /**
         * Function ipInRange
         *
         * @param string $ip_address
         * @param string $network_range
         *
         * @return bool|string|null
         * @author   : 713uk13m <dev@nguyenanhung.com>
         * @copyright: 713uk13m <dev@nguyenanhung.com>
         * @time     : 09/20/2021 07:43
         */
        public function ipInRange(string $ip_address = '', string $network_range = '')
        {
            $ip_address    = trim($ip_address);
            $network_range = trim($network_range);
            if (empty($ip_address) || empty($network_range)) {
                return null;
            }
            try {
                $address = Factory::parseAddressString($ip_address);
                $range   = Factory::parseRangeString($network_range);
                if ($address === null || $range === null) {
                    return null;
                }

                return $address->matches($range);
            } catch (Exception $e) {
                $result = 'Error File: ' . $e->getFile() . ' - Line: ' . $e->getLine() . ' - Code: ' . $e->getCode() . ' - Message: ' . $e->getMessage();
                if (function_exists('log_message')) {
                    log_message('error', 'Error Message: ' . $e->getMessage());
                    log_message('error', 'Error Trace As String: ' . $e->getTraceAsString());
                }

                return $result;
            }
        }

        /**
         * Function ipCalculator
         *
         * @param $ip
         * @param $network_size
         *
         * @return array|string
         * @author   : 713uk13m <dev@nguyenanhung.com>
         * @copyright: 713uk13m <dev@nguyenanhung.com>
         * @time     : 08/08/2020 47:09
         */
        public function ipCalculator($ip, $network_size)
        {
            try {
                $result = new SubnetCalculator($ip, $network_size);

                return $result->getSubnetArrayReport();
            } catch (Exception $e) {
                $message = 'Error File: ' . $e->getFile() . ' - Line: ' . $e->getLine() . ' - Code: ' . $e->getCode() . ' - Message: ' . $e->getMessage();
                if (function_exists('log_message')) {
                    log_message('error', 'Error Message: ' . $e->getMessage());
                    log_message('error', 'Error Trace As String: ' . $e->getTraceAsString());
                }

                return $message;
            }
        }

        /**
         * Function ipValidate
         *
         * @param $ip
         *
         * @return bool
         * @author   : 713uk13m <dev@nguyenanhung.com>
         * @copyright: 713uk13m <dev@nguyenanhung.com>
         * @time     : 08/08/2020 47:25
         */
        public function ipValidate($ip): bool
        {
            return !(filter_var($ip, FILTER_VALIDATE_IP) === false);
        }

        /**
         * Function ipValidateV4
         *
         * @param $ip
         *
         * @return bool
         * @author   : 713uk13m <dev@nguyenanhung.com>
         * @copyright: 713uk13m <dev@nguyenanhung.com>
         * @time     : 08/08/2020 47:31
         */
        public function ipValidateV4($ip): bool
        {
            return !(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false);
        }

        /**
         * Function ipValidateV6
         *
         * @param $ip
         *
         * @return bool
         * @author   : 713uk13m <dev@nguyenanhung.com>
         * @copyright: 713uk13m <dev@nguyenanhung.com>
         * @time     : 08/08/2020 47:40
         */
        public function ipValidateV6($ip): bool
        {
            return !(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false);
        }

        /**
         * Function ip2longV6
         *
         * @param $ip
         *
         * @return string
         * @author   : 713uk13m <dev@nguyenanhung.com>
         * @copyright: 713uk13m <dev@nguyenanhung.com>
         * @time     : 08/08/2020 47:45
         */
        public function ip2longV6($ip): string
        {
            if (substr_count($ip, '::')) {
                $ip = str_replace('::', str_repeat(':0000', 8 - substr_count($ip, ':')) . ':', $ip);
            }
            $ip   = explode(':', $ip);
            $r_ip = '';
            foreach ($ip as $v) {
                $r_ip .= str_pad(base_convert($v, 16, 2), 16, 0, STR_PAD_LEFT);
            }

            return base_convert($r_ip, 2, 10);
        }

        /**
         * Function getRegionOfIP use IPInfo API
         *
         * @param string $ip
         * @param string $apiToken
         *
         * @return false|mixed
         * @author   : 713uk13m <dev@nguyenanhung.com>
         * @copyright: 713uk13m <dev@nguyenanhung.com>
         * @time     : 09/20/2021 39:34
         */
        public function getRegionOfIp(string $ip = '', string $apiToken = '')
        {
            if (empty($ip)) {
                return false;
            }
            try {
                $url      = 'https://ipinfo.io/' . $ip;
                $params   = array('token' => $apiToken);
                $endpoint = $url . '?' . http_build_query($params);
                $curl     = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL            => $endpoint,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING       => "",
                    CURLOPT_MAXREDIRS      => 10,
                    CURLOPT_TIMEOUT        => 30,
                    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST  => "GET",
                    CURLOPT_HTTPHEADER     => array(
                        'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.82 Safari/537.36'
                    ),
                ));

                $response = curl_exec($curl);
                $err      = curl_error($curl);
                curl_close($curl);
                if ($err) {
                    $message = "cURL Error #:" . $err;
                    if (function_exists('log_message')) {
                        log_message('error', $message);
                    }

                    return false;
                }

                $result = json_decode($response, true);

                return $result['region'] ?? false;
            } catch (Exception $e) {
                $message = 'Code: ' . $e->getCode() . ' - File: ' . $e->getFile() . ' - Line: ' . $e->getLine() . ' - Message: ' . $e->getMessage();
                if (function_exists('log_message')) {
                    log_message('error', $message);
                }

                return false;
            }
        }

        /**
         * Function ipInfo
         *
         * @param string $ip
         *
         * @return string|null
         * @author   : 713uk13m <dev@nguyenanhung.com>
         * @copyright: 713uk13m <dev@nguyenanhung.com>
         * @time     : 09/20/2021 14:49
         */
        public function ipInfo(string $ip = '')
        {
            try {
                $ipUrl = 'http://ip-api.com/json/' . $ip;
                $curl  = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL            => $ipUrl,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING       => '',
                    CURLOPT_MAXREDIRS      => 10,
                    CURLOPT_TIMEOUT        => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST  => 'GET',
                ));
                $response = curl_exec($curl);
                $error    = curl_error($curl);
                if ($error) {
                    return "Error";
                }

                return $response;
            } catch (Exception $e) {
                if (function_exists('log_message')) {
                    log_message('error', 'Error Message: ' . $e->getMessage());
                    log_message('error', 'Error Trace As String: ' . $e->getTraceAsString());
                }

                return 'Error File: ' . $e->getFile() . ' - Line: ' . $e->getLine() . ' - Code: ' . $e->getCode() . ' - Message: ' . $e->getMessage();

            }
        }

        /**
         * Function getIpInformation
         *
         * @param string $ip
         *
         * @return string|null
         * @author   : 713uk13m <dev@nguyenanhung.com>
         * @copyright: 713uk13m <dev@nguyenanhung.com>
         * @time     : 09/20/2021 33:03
         */
        public static function getIpInformation(string $ip = '')
        {
            return (new IP)->ipInfo($ip);
        }
    }
}
