<?php

declare( strict_types=1 );

/**
 * The base of all <Input> fields.
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

namespace PinkCrab\Form_Components\Element\Field\Input;

use PinkCrab\Form_Components\Util\Esc;
use PinkCrab\Form_Components\Element\Field;
use PinkCrab\Form_Components\Util\Sanitize;
use PinkCrab\Form_Components\Element\Field\Attribute\Label;
use PinkCrab\Form_Components\Element\Field_Traits\Sanitizer;
use function PinkCrab\FunctionConstructors\Objects\usesTrait;
use PinkCrab\Form_Components\Element\Field_Traits\Form_Style;
use PinkCrab\Form_Components\Element\Field\Attribute\Notification;
use PinkCrab\Form_Components\Element\Field\Attribute\Single_Value;

class Abstract_Input extends Field {

	use Label, Single_Value, Form_Style, Notification;

	/**
	 * Set the value of the input
	 *
	 * @param mixed $value
	 * @return static
	 */
	final public function set_existing( $value ): self {

		// Sanitize value if sanitizer is set.
		if ( usesTrait( Sanitizer::class )( $this ) ) {
			$value = $this->sanitize( $value );
		}

		// If this input has a single value.
		if ( usesTrait( Single_Value::class )( $this ) ) {
			$this->value( $value );
		}

		return $this;
	}

	/**
	 * Sets the input type
	 *
	 * @var string
	 */
	protected $input_type = 'text';

	/**
	 * Get the input type
	 *
	 * @return string
	 */
	final public function get_input_type(): string {
		return $this->input_type;
	}

	/**
	 * @inheritDoc
	 */
	final public function get_type(): string {
		return $this->get_input_type() . '_input';
	}

		/**
	 * Set tabindex value.
	 *
	 * @param string $tabindex
	 * @return static
	 */
	public function tabindex( string $tabindex ): self {
		$this->attribute( 'tabindex', Sanitize::number( $tabindex ) );

		return $this;
	}

	/**
	 * Checks if the field has a tabindex.
	 *
	 * @return bool
	 */
	public function has_tabindex(): bool {
		return $this->has_attribute( 'tabindex' );
	}

	/**
	 * Get tabindex value.
	 *
	 * @return string|null
	 */
	public function get_tabindex(): ?string {
		return $this->has_attribute( 'tabindex' )
			? Esc::attribute( $this->get_attribute( 'tabindex' ) ?? '' )
			: null;
	}

	/**
	 * Clears the tabindex value.
	 *
	 * @return static
	 */
	public function clear_tabindex(): self {
		if ( $this->has_attribute( 'tabindex' ) ) {
			$this->remove_attribute( 'tabindex' );
		}
		return $this;
	}
}
