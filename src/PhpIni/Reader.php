<?php

declare(strict_types=1);

/** 
  * Copyright (C) <2018>  VasylTech <vasyl@vasyltech.com>
  * -------
  * LICENSE: This file is subject to the terms and conditions defined in
  * file 'LICENSE', which is part of source package.
 */

namespace PhpIni;

use InvalidArgumentException;

/**
 * INI Reader
 *
 * Parse INI configuration string
 *
 * @author Vasyl Martyniuk <vasyl@vasyltech.com>
 * @copyright Copyright 2018 VasylTech
 */
class Reader {

    /**
     * Default INI key separator
     */
    const SEPARATOR = '.';

    /**
     * Default section inheritance token
     */
    const INHERIT_KEY = ':';

    /**
     * Parse INI string
     * 
     * Parse configuration string
     *
     * @param  string $string
     * 
     * @return array
     * 
     * @throws InvalidArgumentException
     * 
     * @access public
     */
    public function parse(string $string) : array {
        if (!empty($string)) {
            //parse the string
            set_error_handler(function($error, $message = '') {
                Throw new InvalidArgumentException(
                    sprintf('Error parsing INI: %s', $message), $error
                );
            });
            $ini = parse_ini_string($string, true);
            restore_error_handler();

            $response = $this->process(($ini ?? array()));
        } else {
            $response = array();
        }

        return $response;
    }

    /**
     * Process data from the parsed INI file.
     *
     * @param  array $data
     * 
     * @return array
     * 
     * @access protected
     */
    protected function process(array $data) : array {
        $config = array();
        
        foreach ($data as $section => $data) {
            //check if section has parent section or property
            if (preg_match('/[\s\w]{1}' . self::INHERIT_KEY . '[\s\w]{1}/', $section)) {
                $section = $this->inherit($section, $config);
            } else {
                $section = $this->evaluate($section, $config);

                if ($section === false) {
                    continue; //false - means that it was conditional
                }
            }

            if (is_array($data)) { //this is a INI section, build the nested tree
                $this->buildNestedSection($data, $config[$section]);
            } else { //single property, no need to do anything
                $config[$section] = $this->parseValue($data);
            }
        }

        return $config;
    }

    /**
     * Inherit settings from parent section
     * 
     * @param string $section
     * @param array  &$config
     * 
     * @return string
     * 
     * @access protected
     */
    protected function inherit(string $section, array &$config) : string {
        $sections = explode(self::INHERIT_KEY, $section);
        $target = trim($sections[0]);
        $parent = trim($sections[1]);

        if (isset($config[$parent])) {
            $config[$target] = $config[$parent];
        }

        return $target;
    }
    
    /**
     * Evaluate section definition
     * 
     * @param string $section
     * @param array  &$config
     * 
     * @return bool|string
     * 
     * @access protected
     */
    protected function evaluate(string $section, array &$config) {
        //evaluate the section and if not false move forward
        $evaluator = new Evaluator($section);
        
        $result = $evaluator->evaluate();
        
        if ($result) {
            $config[$evaluator->getAlias()] = array();
        }
        
        return ($result !== false ? $evaluator->getAlias() : $result);
    }

    /**
     * Build nested section
     * 
     * @param array      $data
     * @param array|null &$config
     * 
     * @access protected
     */
    protected function buildNestedSection(array $data, &$config) {
        foreach ($data as $key => $value) {
            $root = &$config;
            foreach (explode(self::SEPARATOR, $key) as $level) {
                if (!isset($root[$level])) {
                    $root[$level] = array();
                }
                $root = &$root[$level];
            }
            $root = $this->parseValue($value);
        }
    }

    /**
     * Parse scalar value
     * 
     * @param mixed $value
     * 
     * @return string
     * 
     * @access protected
     */
    protected function parseValue($value) : string {
        return trim($value);
    }

}