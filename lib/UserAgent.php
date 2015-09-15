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

namespace FlameCore\UserAgent;

/**
 * The UserAgent class
 *
 * @author   Thibault Duplessis <thibault.duplessis at gmail dot com>
 * @author   Christian Neff <christian.neff@gmail.com>
 */
class UserAgent
{
    /**
     * @var string
     */
    protected $string;

    /**
     * @var string
     */
    protected $browserName;

    /**
     * @var string
     */
    protected $browserVersion;

    /**
     * @var string
     */
    protected $browserEngine;

    /**
     * @var string
     */
    protected $operatingSystem;

    /**
     * @var string
     */
    protected $device;

    /**
     * Creates a UserAgent object.
     *
     * @param string $string The user agent string. Uses the current User Agent string by default.
     * @param \FlameCore\UserAgent\UserAgentStringParser $parser The parser used to parse the string
     */
    private function __construct($string = null, UserAgentStringParser $parser = null)
    {
        $this->configureFromUserAgentString($string, $parser);
    }

    /**
     * Creates a UserAgent object from the global $_SERVER string.
     *
     * @return UserAgent The user agent object.
     */
    public static function createFromGlobal()
    {
        return new self();
    }

    /**
     * Creates a UserAgent object.
     *
     * @param string $string The user agent string. Uses the current User Agent string by default.
     * @param \FlameCore\UserAgent\UserAgentStringParser $parser The parser used to parse the string
     * @return UserAgent The user agent object.
     */
    public static function create($string = null, UserAgentStringParser $parser = null)
    {
        return new self($string, $parser);
    }

    /**
     * Returns the string representation of the object.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getFullName();
    }

    /**
     * Gets the user agent string.
     *
     * @return string
     */
    public function getUserAgentString()
    {
        return $this->string;
    }

    /**
     * Sets the user agent string.
     *
     * @param string $string The user agent string
     */
    private function setUserAgentString($string)
    {
        $this->string = $string;
    }

    /**
     * Gets the browser name.
     *
     * @return string
     */
    public function getBrowserName()
    {
        return $this->browserName;
    }

    /**
     * Gets the browser version.
     *
     * @return string
     */
    public function getBrowserVersion()
    {
        return $this->browserVersion;
    }

    /**
     * Gets the full name of the browser. This combines browser name and version.
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->getBrowserName().' '.$this->getBrowserVersion();
    }

    /**
     * Gets the browser engine name.
     *
     * @return string
     */
    public function getBrowserEngine()
    {
        return $this->browserEngine;
    }

    /**
     * Gets the operating system name.
     *
     * @return string
     */
    public function getOperatingSystem()
    {
        return $this->operatingSystem;
    }

    /**
     * Gets the device name.
     *
     * @return string
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * Tells whether this user agent is unknown.
     *
     * @return bool Returns TRUE if this user agent is unknown, FALSE otherwise.
     */
    public function isUnknown()
    {
        return empty($this->browserName);
    }

    /**
     * Tells whether this user agent is a known real browser.
     *
     * @return bool Returns TRUE if this user agent is a real browser, FALSE otherwise.
     */
    public function isRealBrowser()
    {
        return in_array($this->getBrowserName(), $this->getKnownRealBrowsers());
    }

    /**
     * Tells whether this user agent is a known bot/crawler.
     *
     * @return bool Returns TRUE if this user agent is a bot, FALSE otherwise.
     */
    public function isBot()
    {
        return in_array($this->getBrowserName(), $this->getKnownBots());
    }

    /**
     * Configures the user agent information from a user agent string.
     *
     * @param string $string The user agent string
     * @param \FlameCore\UserAgent\UserAgentStringParser $parser The parser used to parse the string
     */
    public function configureFromUserAgentString($string, UserAgentStringParser $parser = null)
    {
        if (!$parser) {
            $parser = new UserAgentStringParser();
        }

        $this->setUserAgentString($string);
        $this->fromArray($parser->parse($string));
    }

    /**
     * Converts the user agent information to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'browser_name' => $this->getBrowserName(),
            'browser_version' => $this->getBrowserVersion(),
            'browser_engine' => $this->getBrowserEngine(),
            'operating_system' => $this->getOperatingSystem(),
            'device' => $this->getDevice()
        );
    }

    /**
     * Configures the user agent information from an array.
     *
     * @param array $data The data array
     */
    private function fromArray(array $data)
    {
        $this->browserName = $data['browser_name'];
        $this->browserVersion = $data['browser_version'];
        $this->browserEngine = $data['browser_engine'];
        $this->operatingSystem = $data['operating_system'];
        $this->device = $data['device'];
    }

    /**
     * Returns an array of strings identifying known real browsers.
     *
     * @return array
     */
    protected function getKnownRealBrowsers()
    {
        return array(
            'firefox',
            'chrome',
            'msie',
            'opera',
            'safari',
            'edge',
            'yabrowser',
            'maxthon',
            'konqueror',
            'netscape',
            'lynx'
        );
    }

    /**
     * Returns an array of strings identifying known bots.
     *
     * @return array
     */
    protected function getKnownBots()
    {
        return array(
            'googlebot',
            'bingbot',
            'msnbot',
            'yahoobot',
            'yandexbot',
            'baidubot',
            'facebookbot'
        );
    }
}
