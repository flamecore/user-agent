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
 * Simple User Agent string parser
 *
 * @author   Thibault Duplessis <thibault.duplessis at gmail dot com>
 * @author   Christian Neff <christian.neff@gmail.com>
 */
class UserAgentStringParser
{
    /**
     * Parses a user agent string.
     *
     * @param string $string The user agent string. Uses the current User Agent string by default.
     * @param bool $fast Make the parser run faster while sacrificing accuracy
     *
     * @return array Returns the user agent information:
     *
     *   - `string`:           The original user agent string
     *   - `browser_name`:     The browser name, e.g. `"chrome"`
     *   - `browser_version`:  The browser version, e.g. `"43.0"`
     *   - `browser_engine`:   The browser engine, e.g. `"webkit"`
     *   - `operating_system`: The operating system, e.g. `"linux"`
     */
    public function parse($string = null, $fast = false)
    {
        // use current user agent string as default
        if ($string === null) {
            $string = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
        }

        // parse quickly (with medium accuracy)
        $information = $this->doParse($string);

        // run some filters to increase accuracy
        if (!$fast) {
            $information = $this->filterBots($information);
            $information = $this->filterBrowserNames($information);
            $information = $this->filterBrowserVersions($information);
            $information = $this->filterBrowserEngines($information);
            $information = $this->filterOperatingSystems($information);
            $information = $this->filterDevices($information);
        }

        return $information;
    }

    /**
     * Cleans the user agent string.
     *
     * @param string $string The dirty user agent string
     *
     * @return string Returns the clean user agent string.
     */
    public function cleanString($string)
    {
        return trim($string);
    }

    /**
     * Extracts information from the user agent string.
     *
     * @param string $string The user agent string
     *
     * @return array Returns the user agent information.
     */
    protected function doParse($string)
    {
        $userAgent = array(
            'string' => $this->cleanString($string),
            'browser_name' => null,
            'browser_version' => null,
            'browser_engine' => null,
            'operating_system' => null,
            'device' => 'Other'
        );

        if (empty($userAgent['string'])) {
            return $userAgent;
        }

        // Find the right name/version phrase (or return empty array if none found)
        foreach ($this->getKnownBrowsers() as $browser => $regexes) {
            // Build regex that matches phrases for known browsers (e.g. "Firefox/2.0" or "MSIE 6.0").
            // This only matches the major and minor version numbers (e.g. "2.0.0.6" is parsed as simply "2.0").
            $pattern = '#('.join('|', $regexes).')[/ ]+([0-9]+(?:\.[0-9]+)?)#i';

            if (preg_match($pattern, $userAgent['string'], $matches)) {
                if (isset($matches[3])) {
                    $browser = str_replace('*', strtolower($matches[2]), $browser);
                }

                $userAgent['browser_name'] = $browser;
                $userAgent['browser_version'] = end($matches);

                break;
            }
        }

        // Find operating system
        if ($result = $this->match($this->getKnownOperatingSystems(), $userAgent['string'])) {
            $userAgent['operating_system'] = $result;
        }

        // Find browser engine
        if ($result = $this->match($this->getKnownEngines(), $userAgent['string'])) {
            $userAgent['browser_engine'] = $result;
        }

        // Find device name
        if ($result = $this->match($this->getKnownDevices(), $userAgent['string'], true)) {
            $userAgent['device'] = $result;
        }

        return $userAgent;
    }

    /**
     * Gets known browsers. Since some UAs have more than one phrase we use an ordered array to define the precedence.
     *
     * @return array Returns an array in the format `[name => [regex, ...], ...]`.
     */
    protected function getKnownBrowsers()
    {
        return array(
            'firefox' => ['firefox', 'minefield', 'iceweasel', 'shiretoko', 'namoroka', 'shredder', 'granparadiso'],
            'opera' => ['opr', 'opera'],
            'edge' => ['edge'],
            'yabrowser' => ['yabrowser'],
            'maxthon' => ['maxthon'],
            'msie' => ['msie'],
            'chrome' => ['chrome'],
            'safari' => ['safari'],
            'konqueror' => ['konqueror'],
            'netscape' => ['netscape'],
            'lynx' => ['lynx'],
            'googlebot' => ['googlebot'],
            'bingbot' => ['bingbot'],
            'msnbot' => ['msnbot'],
            'yahoobot' => ['yahoobot'],
            'yandexbot' => ['yandex\w+'],
            'baidubot' => ['baiduspider\w*'],
            'facebookbot' => ['facebookexternalhit'],
            'flamecore *' => ['flamecore (\w+)']
        );
    }

    /**
     * Gets known operating systems.
     *
     * @return array Returns an array in the format `[name => [regex, ...], ...]`.
     */
    protected function getKnownOperatingSystems()
    {
        return array(
            'Windows 10' => ['windows nt 10.0'],
            'Windows 8.1' => ['windows nt 6.3'],
            'Windows 8' => ['windows nt 6.2'],
            'Windows 7' => ['windows nt 6.1'],
            'Windows Vista' => ['windows nt 6.0'],
            'Windows Server 2003/XP x64' => ['windows nt 5.2'],
            'Windows XP' => ['windows nt 5.1', 'windows xp'],
            'Windows 2000' => ['windows nt 5.0'],
            'Mac OS X' => ['mac os x'],
            'Mac OS 9' => ['mac_powerpc'],
            'Macintosh' => ['macintosh'],
            'Ubuntu' => ['ubuntu'],
            'iOS' => ['iphone', 'ipad', 'ipod'],
            'Android' => ['android'],
            'BlackBerry' => ['blackberry'],
            'Mobile' => ['mobile', 'webos'],
            'Linux' => ['linux']
        );
    }

    /**
     * Gets known browser engines.
     *
     * @return array Returns an array in the format `[name => [regex, ...], ...]`.
     */
    protected function getKnownEngines()
    {
        return array(
            'webkit' => ['webkit'],
            'gecko' => ['gecko'],
            'trident' => ['trident'],
            'presto' => ['presto'],
            'khtml' => ['khtml']
        );
    }

    /**
     * Gets known devices.
     *
     * @return array Returns an array in the format `[name => [regex, ...], ...]`.
     */
    protected function getKnownDevices()
    {
        return array(
            'Apple iPhone' => ['iphone'],
            'Apple iPad' => ['ipad'],
            'Apple iPod' => ['ipod'],
            'Google Nexus *' => ['nexus (\w+)'],
            'BlackBerry' => ['blackberry'],
            'Amazon Kindle Fire' => ['kindle fire'],
            'Amazon Kindle' => ['kindle'],
            'Mobile' => ['mobile', 'android']
        );
    }

    /**
     * Filters bots to increase accuracy.
     *
     * @param array $userAgent The user agent information
     *
     * @return array Returns the updated user agent information.
     */
    protected function filterBots(array $userAgent)
    {
        // Yahoo bot has a special user agent string
        if ($userAgent['browser_name'] === null && stripos($userAgent['string'], 'yahoo! slurp')) {
            $userAgent['browser_name'] = 'yahoobot';
            return $userAgent;
        }

        return $userAgent;
    }

    /**
     * Filters browser names to increase accuracy.
     *
     * @param array $userAgent The user agent information
     *
     * @return array Returns the updated user agent information.
     */
    protected function filterBrowserNames(array $userAgent)
    {
        // IE11 hasn't 'MSIE' in its user agent string
        if (empty($userAgent['browser_name']) && $userAgent['browser_engine'] === 'trident' && strpos($userAgent['string'], 'rv:')) {
            $userAgent['browser_name'] = 'msie';
            $userAgent['browser_version'] = preg_replace('|.+rv:([0-9]+(?:\.[0-9]+)+).+|', '$1', $userAgent['string']);
            return $userAgent;
        }

        return $userAgent;
    }

    /**
     * Filters browser versions to increase accuracy.
     *
     * @param array $userAgent The user agent information
     *
     * @return array Returns the updated user agent information.
     */
    protected function filterBrowserVersions(array $userAgent)
    {
        // Safari version is not encoded "normally"
        if ($userAgent['browser_name'] === 'safari' && stripos($userAgent['string'], ' version/')) {
            $userAgent['browser_version'] = preg_replace('|.+\sversion/([0-9]+(?:\.[0-9]+)?).+|i', '$1', $userAgent['string']);
            return $userAgent;
        }

        // Opera 10.00 (and higher) version number is located at the end
        if ($userAgent['browser_name'] === 'opera' && stripos($userAgent['string'], ' version/')) {
            $userAgent['browser_version'] = preg_replace('|.+\sversion/([0-9]+\.[0-9]+)\s*.*|i', '$1', $userAgent['string']);
            return $userAgent;
        }

        return $userAgent;
    }

    /**
     * Filters browser engines to increase accuracy.
     *
     * @param array $userAgent The user agent information
     *
     * @return array Returns the updated user agent information.
     */
    protected function filterBrowserEngines(array $userAgent)
    {
        // MSIE does not always declare its engine
        if ($userAgent['browser_name'] === 'msie' && empty($userAgent['browser_engine'])) {
            $userAgent['browser_engine'] = 'trident';
            return $userAgent;
        }

        return $userAgent;
    }

    /**
     * Filters operating systems to increase accuracy.
     *
     * @param array $userAgent The user agent information
     *
     * @return array Returns the updated user agent information.
     */
    protected function filterOperatingSystems(array $userAgent)
    {
        // Android instead of Linux
        if (strpos($userAgent['string'], 'Android ')) {
            $userAgent['operating_system'] = preg_replace('|.+(Android [0-9]+(?:\.[0-9]+)+).+|', '$1', $userAgent['string']);
            return $userAgent;
        }

        return $userAgent;
    }

    /**
     * Filters devices to increase accuracy.
     *
     * @param array $userAgent The user agent information
     *
     * @return array Returns the updated user agent information.
     */
    protected function filterDevices(array $userAgent)
    {
        return $userAgent;
    }

    /**
     * Matches the User Agent string against the given list of regexes.
     *
     * @param array $list The list of regexes
     * @param string $string The User Agent string
     * @param bool $wildcard Enable wildcard
     *
     * @return string Returns the matched entry from the list or FALSE if no entry matched.
     */
    protected function match(array $list, $string, $wildcard = false)
    {
        foreach ($list as $name => $regexes) {
            $pattern = '#(?<!like )('.join('|', $regexes).')#i';

            if (preg_match($pattern, $string, $matches)) {
                if ($wildcard && isset($matches[2])) {
                    $name = str_replace('*', strtoupper($matches[2]), $name);
                }

                return $name;
            }
        }

        return false;
    }
}
