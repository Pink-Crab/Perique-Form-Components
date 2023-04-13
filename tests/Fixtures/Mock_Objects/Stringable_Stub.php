<?php

declare(strict_types=1);

/**
 * Stub class used to emulate a stringable object.
 * 
 * Implements the stringable interface if php8 is used or it exists.
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Fixtures\Mock_Objects;
// If php8 is used, use the native stringable interface.
if ( interface_exists( 'Stringable' ) ) {
	class Stringable_Stub implements \Stringable {

		/**
		 * The string value.
		 *
		 * @var string
		 */
		private $value;
		/**
		 * Constructor.
		 *
		 * @param string $value
		 */
		public function __construct( string $value ) {
			$this->value = $value;
		}
		/**
		 * Returns the string value.
		 *
		 * @return string
		 */
		public function __toString() : string {
			return $this->value;
		}
	}
} else {
	// Otherwise use a stub class.
	class Stringable_Stub {

		/**
		 * The string value.
		 *
		 * @var string
		 */
		private $value;
		/**
		 * Constructor.
		 *
		 * @param string $value
		 */
		public function __construct( string $value ) {
			$this->value = $value;
		}
		/**
		 * Returns the string value.
		 *
		 * @return string
		 */
		public function __toString() : string {
			return $this->value;
		}
	}
}
