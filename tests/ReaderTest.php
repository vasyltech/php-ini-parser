<?php

declare(strict_types=1);

/** 
  * Copyright (C) <2018>  VasylTech <vasyl@vasyltech.com>
  * -------
  * LICENSE: This file is subject to the terms and conditions defined in
  * file 'LICENSE', which is part of source package.
 */

use PhpIni\Reader,
    PHPUnit\Framework\TestCase;

/**
 * Reader test class
 */
final class ReaderTest extends TestCase {

    /**
     * Simple config
     *
     * @return void
     */
    public function testSimpleSection() {
        $config = (new Reader)->parse(
            file_get_contents(__DIR__ . '/configs/simple.ini')
        );

        $this->assertSame(array(
            'testSection' => array(
                'optionA' => '1'
            )
        ), $config);
    }

    /**
     * Simple inheritance
     *
     * @return void
     */
    public function testSimpleInheritance() {
        $config = (new Reader)->parse(
            file_get_contents(__DIR__ . '/configs/inheritance.ini')
        );

        $this->assertSame(array(
            'sectionA' => array(
                'optionA' => '1'
            ),
            'sectionB' => array(
                'optionA' => '1',
                'optionB' => '2'
            )
        ), $config);
    }

}