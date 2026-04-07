<?php

declare(strict_types=1);

/**
 * Unit tests for ESC helpers
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Util;

use WP_UnitTestCase;
use PHP_CodeSniffer\Files\DummyFile;
use PinkCrab\Form_Components\Util\Esc;

class Test_Esc extends WP_UnitTestCase {

	/**@testdox It should be possible to esc a string and have all HTML cast to entities. */
	public function test_esc_html(): void {
		// Calls wp `esc_html` function.
        \add_filter( 'esc_html', function( $safe_text, $text ) {
            return 'esc_html_' . $safe_text;
        }, 10, 2 );

        $value = Esc::html( 'string' );
        $this->assertEquals( 'esc_html_string', $value );
	}

    /** @testdox It should be possible to pass string, int, float or Stringable to esc::html() and be treated as strings. */
    public function test_esc_html_casts_to_string(): void {
        // Calls wp `esc_html` function.
        \add_filter( 'esc_html', function( $safe_text, $text ) {
            return 'esc_html_' . $safe_text;
        }, 10, 2 );

        $value = Esc::html( 1 );
        $this->assertEquals( 'esc_html_1', $value );

        $value = Esc::html( 1.1 );
        $this->assertEquals( 'esc_html_1.1', $value );

        $value = Esc::html( new class {
            public function __toString() {
                return 'stringable';
            }
        });
        $this->assertEquals( 'esc_html_stringable', $value );
    }

	/**@testdox It should be possible to esc a string and have all un safe attribute chars cast to entities. */
    public function test_esc_attr(): void {
        // Calls wp `esc_attr` function.
        \add_filter( 'attribute_escape', function( $safe_text, $text ) {
            return 'esc_attr_' . $safe_text;
        }, 10, 2 );

        $value = Esc::attribute( 'string' );
        $this->assertEquals( 'esc_attr_string', $value );
    }

    /** @testdox It should be possible to pass string, int, float or Stringable to esc::attribute() and be treated as strings. */
    public function test_esc_attr_casts_to_string(): void {
        // Calls wp `esc_attr` function.
        \add_filter( 'attribute_escape', function( $safe_text, $text ) {
            return 'esc_attr_' . $safe_text;
        }, 10, 2 );

        $value = Esc::attribute( 1 );
        $this->assertEquals( 'esc_attr_1', $value );

        $value = Esc::attribute( 1.1 );
        $this->assertEquals( 'esc_attr_1.1', $value );

        $value = Esc::attribute( new class {
            public function __toString() {
                return 'stringable';
            }
        });
        $this->assertEquals( 'esc_attr_stringable', $value );
    }

}
