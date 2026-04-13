<?php

declare( strict_types=1 );

/**
 * Select field element.
 *
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Form
 */

namespace PinkCrab\Form_Components\Element\Field;

use PinkCrab\Form_Components\Element\Field;
use PinkCrab\Form_Components\Util\Sanitize;
use PinkCrab\Form_Components\Element\Field_Traits\Sanitizer;
use function PinkCrab\FunctionConstructors\Objects\usesTrait;
use PinkCrab\Form_Components\Element\Field\Attribute\{Label, Single_Value, Notification, Options, Disabled, Required, Multiple, Autocomplete, Size, Description};

class Select extends Field {

	use Label, Single_Value, Notification, Options, Disabled, Required, Multiple, Autocomplete, Size, Description;

	/**
	 * Holds the value of the field (may be array when multiple).
	 *
	 * @var string|int|float|null|bool|array<string>
	 */
	protected $value;

	/**
	 * The default sanitizer for the select.
	 *
	 * @var callable(mixed):mixed|null
	 */
	protected $sanitizer = Sanitize::TEXT;

	/**
	 * Returns the value of the field.
	 *
	 * @return string|int|float|null|bool|array<string>
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * Holds the optgroups.
	 *
	 * @var array<string, array<string, string>>
	 */
	protected $optgroups = array();

	/** @inheritDoc */
	public function get_type(): string {
		return 'select';
	}

	/** @inheritDoc */
	public function set_existing( $value ): self {
		// When multiple is enabled, accept and store an array of values.
		if ( $this->is_multiple() && is_array( $value ) ) {
			if ( usesTrait( Sanitizer::class )( $this ) ) {
				$value = array_map( fn( $v ) => $this->sanitize( $v ), $value );
			}
			$this->value = $value;
			return $this;
		}

		if ( usesTrait( Sanitizer::class )( $this ) ) {
			$value = $this->sanitize( $value );
		}

		$this->value( $value );
		return $this;
	}

	/**
	 * Check if a given value is currently selected.
	 *
	 * @param string $option_value
	 * @return bool
	 */
	public function is_selected( string $option_value ): bool {
		if ( ! $this->has_value() ) {
			return false;
		}

		$value = $this->get_value();
		if ( is_array( $value ) ) {
			return in_array( $option_value, array_map( 'strval', $value ), true );
		}

		return (string) $value === $option_value;
	}

	/**
	 * Add an optgroup with its options.
	 *
	 * @param string $label The optgroup label
	 * @param array<string, string> $options The options within the group
	 * @return static
	 */
	public function optgroup( string $label, array $options ): self {
		$this->optgroups[ $label ] = $options;
		return $this;
	}

	/**
	 * Get all optgroups.
	 *
	 * @return array<string, array<string, string>>
	 */
	public function get_optgroups(): array {
		return $this->optgroups;
	}

	/**
	 * Check if any optgroups have been defined.
	 *
	 * @return bool
	 */
	public function has_optgroups(): bool {
		return ! empty( $this->optgroups );
	}
}
