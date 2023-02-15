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
         * Function getENV
         *
         * @param $key
         *
         * @return array|false|mixed|string
         * @author   : 713uk13m <dev@nguyenanhung.com>
         * @copyright: 713uk13m <dev@nguyenanhung.com>
         * @time     : 08/07/2022 32:44
         */
        private function getENV($key)
        {
            if (isset($_SERVER[$key])) {
                if (strpos($_SERVER[$key], ',')) {
                    $_arr = explode(',', $_SERVER[$key]);

                    return trim($_arr[0]);
                }

                return $_SERVER[$key];
            }
            if (isset($_ENV[$key])) {
                return $_ENV[$key];
            }
            if (@getenv($key)) {
                return @getenv($key);
            }
            if (function_exists('apache_getenv') and apache_getenv($key, true)) {
                return apache_getenv($key, true);
            }

            return '';
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
        public function setHaProxy($haProxyStatus = false)
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
        public function getIpAddress($convertToInteger = false)
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
        public function getIpByHaProxy($convertToInteger = false)
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
            if ($_SERVER['SERVER_NAME'] == 'localhost') {
                return '127.0.0.1';
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
        public function getRawIpAddress($convertToInteger = false)
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
            if ($_SERVER['SERVER_NAME'] == 'localhost') {
                return '127.0.0.1';
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
        public function ipInRange($ip_address = '', $network_range = '')
        {
            $ip_address = trim($ip_address);
            $network_range = trim($network_range);
            if (empty($ip_address) || empty($network_range)) {
                return null;
            }
            try {
                $address = Factory::parseAddressString($ip_address);
                $range = Factory::parseRangeString($network_range);
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
         * Function ipInRangeWithPhpRaw
         *
         * @param string $ip
         * @param string $range
         *
         * @return bool
         * @author: 713uk13m <dev@nguyenanhung.com>
         * @time  : 9/20/18 14:38
         *
         */
        function ipInRangeWithPhpRaw($ip = '', $range = '')
        {
            if (strpos($range, '/') === false) {
                $range .= '/32';
            }
            // $range is in IP/CIDR format eg 127.0.0.1/24
            list($range, $netmask) = explode('/', $range, 2);
            $range_decimal = ip2long($range);
            $ip_decimal = ip2long($ip);
            $wildcard_decimal = pow(2, (32 - $netmask)) - 1;
            $netmask_decimal = ~$wildcard_decimal;

            return (($ip_decimal & $netmask_decimal) == ($range_decimal & $netmask_decimal));
        }

        /**
         * Function ipv6InRangeWithPhpRaw
         *
         * @param string $ip
         * @param string $range
         *
         * @return bool
         * @author: 713uk13m <dev@nguyenanhung.com>
         * @time  : 9/20/18 14:38
         *
         */
        function ipv6InRangeWithPhpRaw($ip = '', $range = '')
        {
            $pieces = explode("/", $range, 2);
            $left_piece = $pieces[0];
            // $right_piece = $pieces[1];
            // Extract out the main IP pieces
            $ip_pieces = explode("::", $left_piece, 2);
            $main_ip_piece = $ip_pieces[0];
            $last_ip_piece = $ip_pieces[1];
            // Pad out the shorthand entries.
            $main_ip_pieces = explode(":", $main_ip_piece);
            foreach ($main_ip_pieces as $key => $val) {
                $main_ip_pieces[$key] = str_pad($main_ip_pieces[$key], 4, "0", STR_PAD_LEFT);
            }
            // Create the first and last pieces that will denote the IPV6 range.
            $first = $main_ip_pieces;
            $last = $main_ip_pieces;
            // Check to see if the last IP block (part after ::) is set
            $last_piece = "";
            $size = count($main_ip_pieces);
            if (trim($last_ip_piece) != "") {
                $last_piece .= str_pad($last_ip_piece, 4, "0", STR_PAD_LEFT);
                // Build the full form of the IPV6 address considering the last IP block set
                for ($i = $size; $i < 7; $i++) {
                    $first[$i] = "0000";
                    $last[$i] = "ffff";
                }
                $main_ip_pieces[7] = $last_piece;
            } else {
                // Build the full form of the IPV6 address
                for ($i = $size; $i < 8; $i++) {
                    $first[$i] = "0000";
                    $last[$i] = "ffff";
                }
            }
            // Rebuild the final long form IPV6 address
            $first = $this->ip2longV6(implode(":", $first));
            $last = $this->ip2longV6(implode(":", $last));

            return ($ip >= $first && $ip <= $last);
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
        public function ipValidate($ip)
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
        public function ipValidateV4($ip)
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
        public function ipValidateV6($ip)
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
        public function ip2longV6($ip)
        {
            if (substr_count($ip, '::')) {
                $ip = str_replace('::', str_repeat(':0000', 8 - substr_count($ip, ':')) . ':', $ip);
            }
            $ip = explode(':', $ip);
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
        public function getRegionOfIp($ip = '', $apiToken = '')
        {
            if (empty($ip)) {
                return false;
            }
            try {
                $url = 'https://ipinfo.io/' . $ip;
                $params = array('token' => $apiToken);
                $endpoint = $url . '?' . http_build_query($params);
                $curl = curl_init();
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
                $err = curl_error($curl);
                curl_close($curl);
                if ($err) {
                    $message = "cURL Error #:" . $err;
                    if (function_exists('log_message')) {
                        log_message('error', $message);
                    }

                    return false;
                }

                $result = json_decode($response, true);
                if (isset($result['region'])) {
                    return $result['region'];
                }

                return false;
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
        public function ipInfo($ip = '')
        {
            try {
                $ipUrl = 'http://ip-api.com/json/' . $ip;
                $curl = curl_init();

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
                $error = curl_error($curl);
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
         * Function isLocalhost - Current IP Connect is Localhost
         *
         * @param $ip
         *
         * @return bool
         * @author   : 713uk13m <dev@nguyenanhung.com>
         * @copyright: 713uk13m <dev@nguyenanhung.com>
         * @time     : 08/07/2022 31:05
         */
        public function isLocalhost($ip = '')
        {
            if (empty($ip)) {
                $ip = $this->getIpAddress();
            }

            return substr($ip, 0, 4) == '127.' or $ip == '::1';
        }

        /**
         * Function getServerIP
         *
         * @return array|false|mixed|string
         * @author   : 713uk13m <dev@nguyenanhung.com>
         * @copyright: 713uk13m <dev@nguyenanhung.com>
         * @time     : 08/07/2022 33:34
         */
        public function getServerIP()
        {
            $serverip = $this->getENV('SERVER_ADDR');
            if ($this->ipValidate($serverip)) {
                return $serverip;
            }
            if ($_SERVER['SERVER_NAME'] == 'localhost') {
                return '127.0.0.1';
            }
            if (function_exists('gethostbyname')) {
                return gethostbyname($_SERVER['SERVER_NAME']);
            }

            return 'none';
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
        public static function getIpInformation($ip = '')
        {
            $self = new self;

            return $self->ipInfo($ip);
        }
    }
}
