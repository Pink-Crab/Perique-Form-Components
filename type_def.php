<?php

/**
 * Type stubs for external dependencies not yet installed.
 */

namespace Respect\Validation;

interface Validatable {
	/**
	 * @param mixed $input
	 * @return bool
	 */
	public function validate( $input ): bool;
}

class Validator implements Validatable {
	/**
	 * @param mixed $input
	 * @return bool
	 */
	public function validate( $input ): bool {
		return true;
	}
}
