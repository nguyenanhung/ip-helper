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

if (!class_exists('nguyenanhung\Libraries\IP\Session')) {
    /**
     * Class Session
     *
     * @package   nguyenanhung\Libraries\IP
     * @author    713uk13m <dev@nguyenanhung.com>
     * @copyright 713uk13m <dev@nguyenanhung.com>\
     */
    class Session
    {
        /**
         * Function start
         *
         * @author: 713uk13m <dev@nguyenanhung.com>
         * @time  : 2018-12-27 22:39
         *
         */
        public static function start()
        {
            if (self::sessionStarted()) {
                session_start();
            }
        }

        /**
         * Function sessionStarted
         *
         * @return bool
         * @author: 713uk13m <dev@nguyenanhung.com>
         * @time  : 2018-12-27 22:39
         *
         */
        public static function sessionStarted(): bool
        {
            return PHP_SESSION_NONE === session_status() || '' === session_id();
        }

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
         * @time  : 2018-12-27 22:38
         *
         */
        public static function exists($name)
        {
            if (is_array($name)) {
                $output = [];
                foreach ($name as $item) {
                    $output[(string)$item] = isset($_SESSION[(string)$item]);
                }

                return $output;
            }

            return isset($_SESSION[(string)$name]);
        }

        /**
         * Function get
         *
         * @param $name
         *
         * @return array|null
         * @author: 713uk13m <dev@nguyenanhung.com>
         * @time  : 2018-12-27 22:38
         *
         */
        public static function get($name)
        {
            if (is_array($name)) {
                $output = [];
                foreach ($name as $item) {
                    $output[(string)$item] = self::exists($item) ? $_SESSION[(string)$item] : null;
                }

                return $output;
            }

            return self::exists($name) ? $_SESSION[(string)$name] : null;
        }

        /**
         * Function save
         *
         * @param      $name
         * @param null $value
         *
         * @return null
         * @author: 713uk13m <dev@nguyenanhung.com>
         * @time  : 2018-12-27 22:42
         *
         */
        public static function save($name, $value = null)
        {
            return static::put($name, $value);
        }

        /**
         * Function set
         *
         * @param      $name
         * @param null $value
         *
         * @return null
         * @author: 713uk13m <dev@nguyenanhung.com>
         * @time  : 2018-12-27 22:42
         *
         */
        public static function set($name, $value = null)
        {
            return static::put($name, $value);
        }

        /**
         * Function put
         *
         * @param      $name
         * @param null $value
         *
         * @return null
         * @author: 713uk13m <dev@nguyenanhung.com>
         * @time  : 2018-12-27 22:38
         *
         */
        public static function put($name, $value = null)
        {
            if (is_array($name)) {
                foreach ($name as $key => $v) {
                    $_SESSION[(string)$key] = $v;
                }

                return $name;
            }

            return $_SESSION[(string)$name] = $value;
        }

        /**
         * Function delete
         *
         * @param $name
         *
         * @return array|null
         * @author: 713uk13m <dev@nguyenanhung.com>
         * @time  : 2018-12-27 22:38
         *
         */
        public static function delete($name)
        {
            $output = self::get($name);
            if (is_array($output)) {
                foreach ($output as $item) {
                    if (self::exists($item)) {
                        unset($_SESSION[(string)$item]);
                    }
                }
            }
            if (null !== $output && !is_array($output)) {
                unset($_SESSION[(string)$name]);
            }

            return $output;
        }

        /**
         * Function destroy
         *
         * @author: 713uk13m <dev@nguyenanhung.com>
         * @time  : 2018-12-27 22:38
         *
         */
        public static function destroy()
        {
            if (self::sessionStarted()) {
                session_destroy();
                $_SESSION = [];
            }
        }
    }
}
