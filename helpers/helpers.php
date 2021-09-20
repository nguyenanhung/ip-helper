<?php
/**
 * Project ip-helper
 * Created by PhpStorm
 * User: 713uk13m <dev@nguyenanhung.com>
 * Copyright: 713uk13m <dev@nguyenanhung.com>
 * Date: 09/20/2021
 * Time: 22:10
 */
if (!function_exists('getIpAddress')) {
    /**
     * Function getIpAddress
     *
     * @param false $convertToInteger
     *
     * @return bool|int|string
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/20/2021 25:27
     */
    function getIpAddress($convertToInteger = false)
    {
        $ip = new nguyenanhung\Libraries\IP\IP();

        return $ip->getIpAddress($convertToInteger);
    }
}
if (!function_exists('getIpByHaProxy')) {
    /**
     * Function getIpByHaProxy
     *
     * @param false $convertToInteger
     *
     * @return bool|int|string
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/20/2021 25:27
     */
    function getIpByHaProxy($convertToInteger = false)
    {
        $ip = new nguyenanhung\Libraries\IP\IP();

        return $ip->getIpByHaProxy($convertToInteger);
    }
}
if (!function_exists('getRawIpAddress')) {
    /**
     * Function getRawIpAddress
     *
     * @param false $convertToInteger
     *
     * @return bool|int|string
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/20/2021 25:27
     */
    function getRawIpAddress($convertToInteger = false)
    {
        $ip = new nguyenanhung\Libraries\IP\IP();

        return $ip->getRawIpAddress($convertToInteger);
    }
}
if (!function_exists('checkIpInRange')) {
    /**
     * Function checkIpInRange
     *
     * @param string $ip_address
     * @param string $network_range
     *
     * @return bool|string|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/20/2021 26:38
     */
    function checkIpInRange($ip_address = '', $network_range = '')
    {
        $ip = new nguyenanhung\Libraries\IP\IP();

        return $ip->ipInRange($ip_address, $network_range);
    }
}
if (!function_exists('ipCalculator')) {
    /**
     * Function ipCalculator
     *
     * @param string $ip_address
     * @param string $network_size
     *
     * @return array|string
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/20/2021 27:04
     */
    function ipCalculator($ip_address = '', $network_size = '')
    {
        $ip = new nguyenanhung\Libraries\IP\IP();

        return $ip->ipCalculator($ip_address, $network_size);
    }
}
if (!function_exists('ipValidate')) {
    /**
     * Function ipValidate
     *
     * @param string $ip_address
     *
     * @return bool
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/20/2021 28:12
     */
    function ipValidate($ip_address = '')
    {
        $ip = new nguyenanhung\Libraries\IP\IP();

        return $ip->ipValidate($ip_address);
    }
}
if (!function_exists('ipValidateV4')) {
    /**
     * Function ipValidateV4
     *
     * @param string $ip_address
     *
     * @return bool
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/20/2021 28:12
     */
    function ipValidateV4($ip_address = '')
    {
        $ip = new nguyenanhung\Libraries\IP\IP();

        return $ip->ipValidateV4($ip_address);
    }
}
if (!function_exists('ipValidateV6')) {
    /**
     * Function ipValidateV6
     *
     * @param string $ip_address
     *
     * @return bool
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/20/2021 28:12
     */
    function ipValidateV6($ip_address = '')
    {
        $ip = new nguyenanhung\Libraries\IP\IP();

        return $ip->ipValidateV6($ip_address);
    }
}
if (!function_exists('convertIpV6ToLong')) {
    /**
     * Function convertIpV6ToLong
     *
     * @param string $ip_address
     *
     * @return string
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/20/2021 30:06
     */
    function convertIpV6ToLong($ip_address = '')
    {
        $ip = new nguyenanhung\Libraries\IP\IP();

        return $ip->ip2longV6($ip_address);
    }
}
if (!function_exists('getIpInformation')) {
    /**
     * Function getIpInformation
     *
     * @param string $ip_address
     *
     * @return string|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/20/2021 29:44
     */
    function getIpInformation($ip_address = '')
    {
        $ip = new nguyenanhung\Libraries\IP\IP();

        return $ip->ipInfo($ip_address);
    }
}
if (!function_exists('getRegionOfIPUseIPInfo')) {
    /**
     * Function getRegionOfIPUseIPInfo
     *
     * @param string $ip_address
     * @param string $token
     *
     * @return false|mixed
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/20/2021 41:03
     */
    function getRegionOfIPUseIPInfo($ip_address = '', $token = '')
    {
        $ip = new nguyenanhung\Libraries\IP\IP();

        return $ip->getRegionOfIp($ip_address, $token);
    }
}
