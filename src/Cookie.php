<?php
/**
 * Project ip-helper
 * Created by PhpStorm
 * User: 713uk13m <dev@nguyenanhung.com>
 * Copyright: 713uk13m <dev@nguyenanhung.com>
 * Date: 09/22/2021
 * Time: 21:27
 */

namespace nguyenanhung\Libraries\IP;

if (!class_exists('nguyenanhung\Libraries\IP\Cookie')) {
    /**
     * Class Cookie
     *
     * @package   nguyenanhung\Libraries\IP
     * @author    713uk13m <dev@nguyenanhung.com>
     * @copyright 713uk13m <dev@nguyenanhung.com>
     */
    class Cookie
    {
        /**
         * Function has
         *
         * @param $name
         *
         * @return array|bool
         * @author: 713uk13m <dev@nguyenanhung.com>
         * @time  : 2018-12-27 22:40
         *
         */
        public static function has($name)
        {
            return static::exists($name);
        }

        /**
         * Function exists
         *
         * @param $name
         *
         * @return array|bool
         * @author: 713uk13m <dev@nguyenanhung.com>
         * @time  : 2018-12-27 22:40
         *
         */
        public static function exists($name)
        {
            if (is_array($name)) {
                $output = [];
                foreach ($name as $item) {
                    $output[(string)$item] = isset($_COOKIE[(string)$item]);
                }

                return $output;
            }

            return isset($_COOKIE[(string)$name]);
        }

        /**
         * Function get
         *
         * @param $name
         *
         * @return array|null
         * @author: 713uk13m <dev@nguyenanhung.com>
         * @time  : 2018-12-27 22:40
         *
         */
        public static function get($name)
        {
            if (is_array($name)) {
                $output = [];
                foreach ($name as $item) {
                    $output[(string)$item] = self::exists($item) ? $_COOKIE[(string)$item] : null;
                }

                return $output;
            }

            return self::exists($name) ? $_COOKIE[(string)$name] : null;
        }
    }
}
