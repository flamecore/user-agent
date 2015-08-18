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

use FlameCore\UserAgent\UserAgent;

/**
 * Test class for UserAgent
 */
class UserAgentTest extends \PHPUnit_Framework_TestCase
{
    public function testBrowserUserAgentString()
    {
        $userAgentString = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36';
        $userAgent = new UserAgent($userAgentString);

        $this->assertEquals('chrome', $userAgent->getBrowserName(), 'Browser: $userAgent->getBrowserName() works');
        $this->assertEquals('41.0', $userAgent->getBrowserVersion(), 'Browser: $userAgent->getBrowserVersion() works');
        $this->assertEquals('Linux', $userAgent->getOperatingSystem(), 'Browser: $userAgent->getOperatingSystem() works');
        $this->assertEquals('webkit', $userAgent->getBrowserEngine(), 'Browser: $userAgent->getBrowserEngine() works');
        $this->assertEquals(false, $userAgent->isUnknown(), 'Browser: User agent is not unknown');
        $this->assertEquals(false, $userAgent->isBot(), 'Browser: User agent is not a bot');
    }

    public function testBotUserAgentString()
    {
        $userAgentString = 'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)';
        $userAgent = new UserAgent($userAgentString);

        $this->assertEquals('bingbot', $userAgent->getBrowserName(), 'Bot: $userAgent->getBrowserName() works');
        $this->assertEquals('2.0', $userAgent->getBrowserVersion(), 'Bot: $userAgent->getBrowserVersion() works');
        $this->assertEquals(null, $userAgent->getOperatingSystem(), 'Bot: $userAgent->getOperatingSystem() works');
        $this->assertEquals(null, $userAgent->getBrowserEngine(), 'Bot: $userAgent->getBrowserEngine() works');
        $this->assertEquals(false, $userAgent->isUnknown(), 'Bot: User agent is not unknown');
        $this->assertEquals(true, $userAgent->isBot(), 'Bot: User agent is a bot');
    }

    public function testMalformedUserAgentString()
    {
        $userAgent = new UserAgent('hmm...');

        $this->assertEquals(null, $userAgent->getBrowserName(), 'Malformed: $userAgent->getBrowserName() works');
        $this->assertEquals(null, $userAgent->getBrowserVersion(), 'Malformed: $userAgent->getBrowserVersion() works');
        $this->assertEquals(null, $userAgent->getOperatingSystem(), 'Malformed: $userAgent->getOperatingSystem() works');
        $this->assertEquals(null, $userAgent->getBrowserEngine(), 'Malformed: $userAgent->getBrowserEngine() works');
        $this->assertEquals(true, $userAgent->isUnknown(), 'Malformed: User agent is unknown');
        $this->assertEquals(false, $userAgent->isBot(), 'Malformed: User agent is not a bot');
    }

    public function testToArray()
    {
        $userAgentString = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36';
        $userAgent = new UserAgent($userAgentString);

        $expected = array(
            'browser_name'     => 'chrome',
            'browser_version'  => '41.0',
            'operating_system' => 'Linux',
            'browser_engine'   => 'webkit',
            'device'           => 'Other'
        );

        $result = $userAgent->toArray();

        $this->assertEquals($expected, $result, '$userAgent->toArray() works');
    }
}
