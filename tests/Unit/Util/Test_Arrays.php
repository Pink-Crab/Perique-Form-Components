<?php

declare(strict_types=1);

/**
 * Unit tests for array helpers
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Util;

use WP_UnitTestCase;
use PinkCrab\Form_Components\Util\Arrays;

class Test_Arrays extends WP_UnitTestCase {

    /** @testdox It should be possible to check if an array is a list or not. */
    public function test_is_array_list(): void
    {
        $this->assertTrue(Arrays::is_list(array(1,2,3)));
        $this->assertFalse(Arrays::is_list(array('a' => 1, 'b' => 2, 'c' => 3)));
        $this->assertFalse(Arrays::is_list(array(0 => 1, 1 => 2, 4 => 3)));
    }
}