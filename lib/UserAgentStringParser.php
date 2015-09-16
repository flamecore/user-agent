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
 * Simple User Agent string parser
 *
 * @author   Thibault Duplessis <thibault.duplessis at gmail dot com>
 * @author   Christian Neff <christian.neff@gmail.com>
 */
class UserAgentStringParser
{
    /**
     * @var \FlameCore\UserAgent\UserAgentDefinition
     */
    protected $definition;

    /**
     * @param \FlameCore\UserAgent\UserAgentDefinition $definition
     */
    public function __construct(UserAgentDefinition $definition = null)
    {
        $this->definition = $definition ?: new UserAgentDefinition();
    }

    /**
     * Parses a user agent string.
     *
     * @param string $string The user agent string
     * @param bool $strict Enable strict mode. This makes the parser run a bit slower but increases the accuracy significantly.
     *
     * @return array Returns the user agent information:
     *
     *   - `string`:           The original user agent string
     *   - `browser_name`:     The browser name, e.g. `"chrome"`
     *   - `browser_version`:  The browser version, e.g. `"43.0"`
     *   - `browser_engine`:   The browser engine, e.g. `"webkit"`
     *   - `operating_system`: The operating system, e.g. `"linux"`
     */
    public function parse($string, $strict = true)
    {
        // Parse quickly (with medium accuracy)
        $information = $this->doParse($string);

        // Run some filters to increase accuracy
        if ($strict) {
            $information = $this->definition->filter($information);
        }

        return $information;
    }

    /**
     * Parses the user agent string provided by the global.
     *
     * @param bool $strict Enable strict mode. This makes the parser run a bit slower but increases the accuracy significantly.
     *
     * @return array Returns the user agent information.
     *
     * @see UserAgentStringParser::parse()
     */
    public function parseFromGlobal($strict = true)
    {
        $string = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;

        return $this->parse($string, $strict);
    }

    /**
     * @return \FlameCore\UserAgent\UserAgentDefinition
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * @param \FlameCore\UserAgent\UserAgentDefinition $definition
     */
    public function setDefinition($definition)
    {
        $this->definition = $definition;
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

        $browsers = $this->definition->getKnownBrowsers();
        $bots = $this->definition->getKnownBots();
        $userAgents = $browsers + $bots;

        // Find the right name/version phrase (or return empty array if none found)
        foreach ($userAgents as $name => $regexes) {
            if ($matches = $this->matchBrowser($regexes, $userAgent['string'])) {
                if (isset($matches[3])) {
                    $name = str_replace('*', strtolower($matches[2]), $name);
                }

                $userAgent['browser_name'] = $name;
                $userAgent['browser_version'] = end($matches);

                break;
            }
        }

        // Find browser engine
        $engines = $this->definition->getKnownEngines();
        if ($result = $this->find($engines, $userAgent['string'])) {
            $userAgent['browser_engine'] = $result;
        }

        // Find operating system
        $operatingSystems = $this->definition->getKnownOperatingSystems();
        if ($result = $this->find($operatingSystems, $userAgent['string'])) {
            $userAgent['operating_system'] = $result;
        }

        // Find device name
        $devices = $this->definition->getKnownDevices();
        if ($result = $this->find($devices, $userAgent['string'], true)) {
            $userAgent['device'] = $result;
        }

        return $userAgent;
    }

    /**
     * Matches the list of browser regexes against the given User Agent string.
     *
     * @param array $regexes The list of regexes
     * @param string $string The User Agent string
     *
     * @return array|false Returns the parts of the matching regex or FALSE if no regex matched.
     */
    protected function matchBrowser(array $regexes, $string)
    {
        // Build regex that matches phrases for known browsers (e.g. "Firefox/2.0" or "MSIE 6.0").
        // This only matches the major and minor version numbers (e.g. "2.0.0.6" is parsed as simply "2.0").
        $pattern = '#('.join('|', $regexes).')[/ ]+([0-9]+(?:\.[0-9]+)?)#i';

        if (preg_match($pattern, $string, $matches)) {
            return $matches;
        }

        return false;
    }

    /**
     * Matches the list of regexes against the given User Agent string.
     *
     * @param array $regexes The list of regexes
     * @param string $string The User Agent string
     *
     * @return array|false Returns the parts of the matching regex or FALSE if no regex matched.
     */
    protected function match(array $regexes, $string)
    {
        $pattern = '#(?<!like )('.join('|', $regexes).')#i';

        if (preg_match($pattern, $string, $matches)) {
            return $matches;
        }

        return false;
    }

    /**
     * Matches the list of regexes against the given User Agent string.
     *
     * @param array $list The list of regexes
     * @param string $string The User Agent string
     * @param bool $wildcard Enable wildcard
     *
     * @return string Returns the matched entry from the list or FALSE if no entry matched.
     */
    protected function find(array $list, $string, $wildcard = false)
    {
        foreach ($list as $name => $regexes) {
            if ($matches = $this->match($regexes, $string)) {
                if ($wildcard && isset($matches[2])) {
                    $name = str_replace('*', strtoupper($matches[2]), $name);
                }

                return $name;
            }
        }

        return false;
    }

    /**
     * Cleans the user agent string.
     *
     * @param string $string The dirty user agent string
     *
     * @return string Returns the clean user agent string.
     */
    protected function cleanString($string)
    {
        return trim($string);
    }
}
