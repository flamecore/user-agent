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
 * @version  1.0-dev
 * @link     http://www.flamecore.org
 * @license  http://opensource.org/licenses/ISC ISC License
 */

namespace FlameCore\UserAgent;

/**
 * The User Agent definition
 *
 * @author   Christian Neff <christian.neff@gmail.com>
 */
class UserAgentDefinition
{
    /**
     * Gets known browsers. Since some UAs have more than one phrase we use an ordered array to define the precedence.
     *
     * @return array Returns an array in the format `[name => [regex, ...], ...]`.
     */
    public function getKnownBrowsers()
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
            'lynx' => ['lynx']
        );
    }

    /**
     * Gets known bots. Since some UAs have more than one phrase we use an ordered array to define the precedence.
     *
     * @return array Returns an array in the format `[name => [regex, ...], ...]`.
     */
    public function getKnownBots()
    {
        return array(
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
     * Gets known browser engines.
     *
     * @return array Returns an array in the format `[name => [regex, ...], ...]`.
     */
    public function getKnownEngines()
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
     * Gets known operating systems.
     *
     * @return array Returns an array in the format `[name => [regex, ...], ...]`.
     */
    public function getKnownOperatingSystems()
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
     * Gets known devices.
     *
     * @return array Returns an array in the format `[name => [regex, ...], ...]`.
     */
    public function getKnownDevices()
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
     * Filters the results to increase accuracy.
     *
     * @param array $information The user agent information
     *
     * @return array Returns the updated user agent information.
     */
    public function filter(array $information)
    {
        $information = $this->filterBots($information);
        $information = $this->filterBrowserNames($information);
        $information = $this->filterBrowserVersions($information);
        $information = $this->filterBrowserEngines($information);
        $information = $this->filterOperatingSystems($information);
        $information = $this->filterDevices($information);

        return $information;
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
        // Safari and Opera 10.00+ version number is not encoded "normally"
        if (in_array($userAgent['browser_name'], ['safari', 'opera']) && stripos($userAgent['string'], ' version/')) {
            $userAgent['browser_version'] = preg_replace('|.+ version/([0-9]+(?:\.[0-9]+)?).*|i', '$1', $userAgent['string']);

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
}
