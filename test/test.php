<?php
/**
 * Project ip-helper
 * Created by PhpStorm
 * User: 713uk13m <dev@nguyenanhung.com>
 * Copyright: 713uk13m <dev@nguyenanhung.com>
 * Date: 09/20/2021
 * Time: 22:12
 */
require_once __DIR__ . '/../vendor/autoload.php';


echo "Helper getIpAddress: " . getIpAddress() . PHP_EOL;
echo "Helper getIpByHaProxy: " . getIpByHaProxy() . PHP_EOL;
echo "Helper getRawIpAddress: " . getRawIpAddress() . PHP_EOL;
echo "Helper checkIpInRange: " . checkIpInRange('127.0.0.1', '127.0.0.1/32') . PHP_EOL;
echo "Helper ipCalculator: " . json_encode(ipCalculator('127.0.0.1', '32')) . PHP_EOL;
echo "Helper ipValidate: " . ipValidate('118.71.97.254') . PHP_EOL;
echo "Helper ipValidateV4: " . ipValidateV4('118.71.97.254') . PHP_EOL;
echo "Helper ipValidateV6: " . ipValidateV6('2400:6180:0:d0::107a:1001') . PHP_EOL;
echo "Helper convertIpV6ToLong: " . convertIpV6ToLong('2400:6180:0:d0::107a:1001') . PHP_EOL;
echo "Helper getIpInformation: " . getIpInformation('118.71.97.254') . PHP_EOL;
