<?php

declare( strict_types=1 );

/**
 * Trait to add Wrapper Attributes to an element.
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

namespace PinkCrab\Form_Components\Element\Field_Traits;

use PinkCrab\Form_Components\Utils;
use PinkCrab\Form_Components\Util\Esc;
use PinkCrab\Form_Components\Element\Element;

use function PinkCrab\FunctionConstructors\GeneralFunctions\compose;

trait Wrapper_Attributes {

	/**
	 * Holds all the attributes
	 *
	 * @var array<string, string|int|float|bool|null>
	 */
	protected $wrapper_attributes = array();

	/**
	 * Checks if the field has an attribute.
	 *
	 * @param string $attribute
	 * @return bool
	 */
	public function has_wrapper_attribute( string $attribute ): bool {
		return array_key_exists( $attribute, $this->wrapper_attributes );
	}

	/**
	 * Checks if there are any attributes.
	 *
	 * @return bool
	 */
	public function has_wrapper_attributes(): bool {
		return ! empty( $this->wrapper_attributes );
	}

	/**
	 * Gets the value of an attribute.
	 *
	 * @param string $attribute
	 * @return string|int|float|bool|null
	 */
	public function get_wrapper_attribute( string $attribute ) {
		return \array_key_exists( $attribute, $this->wrapper_attributes )
			? Esc::attribute( $this->wrapper_attributes[ $attribute ] )
			: null;
	}

	/**
	 * Get all attributes.
	 *
	 * @return array<string, string|int|float|bool|null>
	 */
	public function get_wrapper_attributes(): array {
		return $this->wrapper_attributes;
	}

	/**
	 * Sets the value of an attribute.
	 *
	 * @param string $attribute
	 * @param string|int|float|bool|null $value
	 * @return static
	 */
	public function wrapper_attribute( string $attribute, $value = null ): self {
		$this->wrapper_attributes[ $attribute ] = Esc::attribute( $value );
		return $this;
	}

	/**
	 * Sets an array of attributes.
	 *
	 * @param array<string, string|int|float|bool|null> $attributes
	 * @return static
	 */
	public function wrapper_attributes( array $attributes ): self {
		$this->wrapper_attributes = array_merge( $this->wrapper_attributes, $attributes );
		return $this;
	}

	/**
	 * Removes an attribute if it exists.
	 *
	 * @param string $attribute
	 * @return static
	 */
	public function remove_wrapper_attribute( string $attribute ): self {
		if ( $this->has_wrapper_attribute( $attribute ) ) {
			unset( $this->wrapper_attributes[ $attribute ] );
		}
		return $this;
	}

	// Helpers

	/**
	 * Adds a class to the class attribute.
	 *
	 * @param string $class
	 * @return static
	 */
	public function add_wrapper_class( string $class ): self {
		$classes = $this->get_wrapper_attribute( 'class' );
		if ( ! $classes ) {
			return $this->wrapper_attribute( 'class', Esc::attribute( $class ) );
		}

		$classes = explode( ' ', Esc::attribute( $classes ) );
		if ( ! in_array( $class, $classes, true ) ) {
			$classes[] = Esc::attribute( $class );
		}

		return $this->wrapper_attribute( 'class', implode( ' ', array_map( array( Esc::class, 'attribute' ), $classes ) ) );
	}

	/**
	 * Remove a class from the class attribute.
	 *
	 * @param string $class
	 * @return static
	 */
	public function remove_wrapper_class( string $class ): self {
		$classes = $this->get_wrapper_attribute( 'class' );
		if ( ! $classes ) {
			return $this;
		}

		$classes = explode( ' ', Esc::attribute( $classes ) );
		if ( in_array( $class, $classes, true ) ) {
			$classes = array_diff( $classes, array( $class ) );
		}

		return $this->wrapper_attribute( 'class', implode( ' ', array_map( array( Esc::class, 'attribute' ), $classes ) ) );
	}


	/**
	 * Set the ID
	 *
	 * @param string $id
	 * @return static
	 */
	public function wrapper_id( string $id ): self {
		return $this->wrapper_attribute( 'id', Esc::attribute( $id ) );
	}


	/**
	 * Add a data attribute.
	 *
	 * @param string $key
	 * @param string|int|float|bool|null $value
	 * @return static
	 */
	public function wrapper_data( string $key, $value ): self {
		return $this->wrapper_attribute( 'data-' . Esc::attribute( $key ), Esc::attribute( $value ) );
	}

}
