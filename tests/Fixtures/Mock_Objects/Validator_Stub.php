<?php

declare(strict_types=1);

/**
 * Stub for Respect\Validation\Validator used in tests.
 *
 * The real package is not installed as a dependency, so we provide
 * a minimal stub that satisfies the type hints in the Validation trait.
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace Respect\Validation;

if ( ! interface_exists( 'Respect\Validation\Validatable' ) ) {
	interface Validatable {
		/**
		 * @param mixed $input
		 * @return bool
		 */
		public function validate( $input ): bool;
	}
}

if ( ! class_exists( 'Respect\Validation\Validator' ) ) {
	class Validator implements Validatable {
		/**
		 * @param mixed $input
		 * @return bool
		 */
		public function validate( $input ): bool {
			return true;
		}
	}
}
