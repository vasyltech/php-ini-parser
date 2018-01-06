<?php

/** 
  * Copyright (C) <2018>  VasylTech <vasyl@vasyltech.com>
  * -------
  * LICENSE: This file is subject to the terms and conditions defined in
  * file 'LICENSE', which is part of source package.
 */

spl_autoload_register(function($cname) {
    if (strpos($cname, 'PhpIni\\') === 0) {
        $fname = realpath(__DIR__ . '/' . str_replace('\\', '/', $cname) . '.php');
        
        if (file_exists($fname)) {
            require $fname;
        }
    }
});