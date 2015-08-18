<?php
/**
 * FlameCore UserAgent
 * Copyright (C) 2015 IceFlame.net
 *
 * Permission to use, copy, modify, and/or distribute this software for
 * any purpose with or without fee is hereby granted, provided that the
 * above copyright notice and this permission notice appear in all copies.
 *
 * @package  FlameCore\UserAgent
 * @version  1.0
 * @link     http://www.flamecore.org
 * @license  http://opensource.org/licenses/ISC ISC License
 */

namespace FlameCore\UserAgent\Tests;

use FlameCore\UserAgent\UserAgentStringParser;

/**
 * Test class for UserAgentStringParser
 */
class UserAgentStringParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParserWithDesktopBrowsers()
    {
        $testData = array(
            // Chrome Mac
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36'
            => array('chrome', '37.0', 'Mac OS X', 'webkit', 'Other'),

            // Safari Mac
            'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_2; fr-fr) AppleWebKit/531.21.8 (KHTML, like Gecko) Version/4.0.4 Safari/531.21.10'
            => array('safari', '4.0', 'Mac OS X', 'webkit', 'Other'),

            // Opera 9 Windows
            'Opera/9.61 (Windows NT 6.0; U; en) Presto/2.1.1'
            => array('opera', '9.61', 'Windows Vista', 'presto', 'Other'),

            // Opera 10 Windows
            'Opera/9.80 (Windows NT 5.1; U; en) Presto/2.2.15 Version/10.10'
            => array('opera', '10.10', 'Windows XP', 'presto', 'Other'),

            // Opera 15 Windows
            'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.52 Safari/537.36 OPR/15.0.1147.100'
            => array('opera', '15.0', 'Windows 7', 'webkit', 'Other'),

            // Konqueror
            'Mozilla/5.0 (compatible; Konqueror/4.4; Linux) KHTML/4.4.1 (like Gecko) Fedora/4.4.1-1.fc12'
            => array('konqueror', '4.4', 'Linux', 'khtml', 'Other'),

            // Firefox Linux
            'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.17) Gecko/2010010604 Linux Mint/7 (Gloria) Firefox/3.0.17'
            => array('firefox', '3.0', 'Linux', 'gecko', 'Other'),

            // Firefox Windows
            'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-GB; rv:1.9.1.7) Gecko/20091221 Firefox/3.5.7 GTB6 (.NET CLR 3.5.30729)'
            => array('firefox', '3.5', 'Windows 7', 'gecko', 'Other'),

            // Firefox OSX
            'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.1.8) Gecko/20100202 Firefox/3.5.8'
            => array('firefox', '3.5', 'Mac OS X', 'gecko', 'Other'),

            // Chrome Linux
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36'
            => array('chrome', '41.0', 'Linux', 'webkit', 'Other'),

            // Minefield Mac
            'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.3a1pre) Gecko/20100113 Minefield/3.7a1pre'
            => array('firefox', '3.7', 'Mac OS X', 'gecko', 'Other'),

            // IE 6 Windows
            'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; DigExt)'
            => array('msie', '6.0', 'Windows 2000', 'trident', 'Other'),

            // IE 7 Windows
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; Trident/4.0; GTB6; SLCC1; .NET CLR 2.0.50727; OfficeLiveConnector.1.3; OfficeLivePatch.0.0; .NET CLR 3.5.30729; InfoPath.2; .NET CLR 3.0.30729; MSOffice 12)'
            => array('msie', '7.0', 'Windows Vista', 'trident', 'Other'),

            // IE 11 Windows
            'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko'
            => array('msie', '11.0', 'Windows 7', 'trident', 'Other'),

            // Edge (Windows 10)
            'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.71 Safari/537.36 Edge/12.0'
            => array('edge', '12.0', 'Windows 10', 'webkit', 'Other'),

            // Yandex.Browser
            'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.12785 YaBrowser/13.12.1599.12785 Safari/537.36'
            => array('yabrowser', '13.12', 'Windows 7', 'webkit', 'Other'),

            // Maxthon 3.0
            'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US) AppleWebKit/533.1 (KHTML, like Gecko) Maxthon/3.0.8.2 Safari/533.1'
            => array('maxthon', '3.0', 'Windows Vista', 'webkit', 'Other'),

            // Maxthon 2.0
            'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/4.0; WOW64; Trident/5.0; .NET CLR 3.5.30729; Media Center PC 6.0; Maxthon 2.0)'
            => array('maxthon', '2.0', 'Windows 7', 'trident', 'Other'),

            // Namoroka Ubuntu
            'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2pre) Gecko/20100116 Ubuntu/9.10 (karmic) Namoroka/3.6pre'
            => array('firefox', '3.6', 'Ubuntu', 'gecko', 'Other'),

            // Namoroka Mac
            'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.2) Gecko/20100105 Firefox/3.6'
            => array('firefox', '3.6', 'Mac OS X', 'gecko', 'Other'),

            // Lynx
            'Lynx/2.8.6rel.5 libwww-FM/2.14 SSL-MM/1.4.1 OpenSSL/1.0.0a'
            => array('lynx', '2.8', null, null, 'Other')
        );

        $this->doTest($testData);
    }

    public function testParserWithMobileBrowsers()
    {
        $testData = array(
            // iPhone 4
            'Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_3_2 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8H7 Safari/6533.18.5'
            => array('safari', '5.0', 'iOS', 'webkit', 'Apple iPhone'),

            // Motorola Xoom
            'Mozilla/5.0 (Linux; U; Android 3.0; en-us; Xoom Build/HRI39) AppleWebKit/534.13 (KHTML, like Gecko) Version/4.0 Safari/534.13'
            => array('safari', '4.0', 'Android 3.0', 'webkit', 'Mobile'),

            // Samsung Galaxy Tab
            'Mozilla/5.0 (Linux U Android 2.2 es-es GT-P1000 Build/FROYO) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1'
            => array('safari', '4.0', 'Android 2.2', 'webkit', 'Mobile'),

            // Google Nexus
            'Mozilla/5.0 (Linux; U; Android 2.2; en-us; Nexus One Build/FRF91) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1'
            => array('safari', '4.0', 'Android 2.2', 'webkit', 'Google Nexus ONE'),

            // HTC Desire
            'Mozilla/5.0 (Linux; U; Android 2.1-update1; de-de; HTC Desire 1.19.161.5 Build/ERE27) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17'
            => array('safari', '4.0', 'Android 2.1', 'webkit', 'Mobile'),

            // Android Gingerbread
            'Mozilla/5.0 (Linux; U; Android 2.3.6; ru-ru; GT-B5512 Build/GINGERBREAD) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1'
            => array('safari', '4.0', 'Android 2.3.6', 'webkit', 'Mobile'),

            // Nexus 7
            'Mozilla/5.0 (Linux; Android 4.1.1; Nexus 7 Build/JRO03D) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.166  Safari/535.19'
            => array('chrome', '18.0', 'Android 4.1.1', 'webkit', 'Google Nexus 7'),

            // iPad
            'Mozilla/5.0 (iPad; CPU OS 6_1_3 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10B329 Safari/8536.25'
            => array('safari', '6.0', 'iOS', 'webkit', 'Apple iPad')
        );

        $this->doTest($testData);
    }

    public function testParserWithBots()
    {
        $testData = array(
            // Google Bot
            'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'
            => array('googlebot', '2.1', null, null, 'Other'),

            // Bing Bot
            'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)'
            => array('bingbot', '2.0', null, null, 'Other'),

            // MSN Bot
            'msnbot/2.0b (+http://search.msn.com/msnbot.htm)'
            => array('msnbot', '2.0', null, null, 'Other'),

            // Yahoo Bot
            'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)'
            => array('yahoobot', null, null, null, 'Other'),

            // Yandex Bot
            'Mozilla/5.0 (compatible; YandexBot/3.0; +http://yandex.com/bots)'
            => array('yandexbot', '3.0', null, null, 'Other'),

            // Baidu Bot
            'Mozilla/5.0 (compatible; Baiduspider/2.0; +http://www.baidu.com/search/spider.html)'
            => array('baidubot', '2.0', null, null, 'Other'),

            // Facebook
            'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)'
            => array('facebookbot', '1.1', null, null, 'Other'),

            // Feedfetcher Google
            'Feedfetcher-Google; (+http://www.google.com/feedfetcher.html; 2 subscribers; feed-id=6924676383167400434)'
            => array(null, null, null, null, 'Other'),

            // FlameCore
            'Mozilla/5.0 (compatible; FlameCore Webtools/1.3)'
            => array('flamecore webtools', '1.3', null, null, 'Other'),

            // Speedy Spider
            'Speedy Spider (http://www.entireweb.com/about/search_tech/speedy_spider/)'
            => array(null, null, null, null, 'Other')
        );

        $this->doTest($testData);
    }

    private function doTest(array $testData)
    {
        $parser = new UserAgentStringParser();

        $i = 0;

        foreach ($testData as $string => $data) {
            $i++;

            $expected = array(
                'string'           => $string,
                'browser_name'     => $data[0],
                'browser_version'  => $data[1],
                'operating_system' => $data[2],
                'browser_engine'   => $data[3],
                'device'           => $data[4]
            );

            $result = $parser->parse($string);

            $this->assertEquals($expected, $result, "User Agent #$i:" . PHP_EOL . "  $string");
        }
    }
}
