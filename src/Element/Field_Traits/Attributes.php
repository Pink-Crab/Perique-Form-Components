<?php

declare( strict_types=1 );

/**
 * Trait to add Attributes to a field.
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

trait Attributes {

	/**
	 * Holds all the attributes
	 *
	 * @var array<string, string|int|float|bool|null>
	 */
	protected $attributes = array();

	/**
	 * Checks if the field has an attribute.
	 *
	 * @param string $attribute
	 * @return bool
	 */
	public function has_attribute( string $attribute ): bool {
		return array_key_exists( $attribute, $this->attributes );
	}

	/**
	 * Checks if there are any attributes.
	 *
	 * @return bool
	 */
	public function has_attributes(): bool {
		return ! empty( $this->attributes );
	}

	/**
	 * Gets the value of an attribute.
	 *
	 * @param string $attribute
	 * @return string|int|float|bool|null
	 */
	public function get_attribute( string $attribute ) {
		return \array_key_exists( $attribute, $this->attributes )
			? $this->attributes[ $attribute ]
			: null;
	}

	/**
	 * Get all attributes.
	 *
	 * @return array<string, string|int|float|bool|null>
	 */
	public function get_attributes(): array {
		return $this->attributes;
	}

	/**
	 * Sets the value of an attribute.
	 *
	 * @param string $attribute
	 * @param string|int|float|bool|null $value
	 * @return static
	 */
	public function attribute( string $attribute, $value = null ): self {
		$this->attributes[ $attribute ] = $value;
		return $this;
	}

	/**
	 * Sets an array of attributes.
	 *
	 * @param array<string, string|int|float|bool|null> $attributes
	 * @return static
	 */
	public function attributes( array $attributes ): self {
		$this->attributes = array_merge( $this->attributes, $attributes );
		return $this;
	}

	/**
	 * Removes an attribute if it exists.
	 *
	 * @param string $attribute
	 * @return static
	 */
	public function remove_attribute( string $attribute ): self {
		if ( $this->has_attribute( $attribute ) ) {
			unset( $this->attributes[ $attribute ] );
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
	public function add_class( string $class ): self {
		$classes = $this->get_attribute( 'class' );
		if ( ! $classes ) {
			return $this->attribute( 'class', $class );
		}

		$classes = explode( ' ', strval( $classes ) );
		if ( ! in_array( $class, $classes, true ) ) {
			$classes[] = $class;
		}

		return $this->attribute( 'class', implode( ' ', $classes ) );
	}

	/**
	 * Remove a class from the class attribute.
	 *
	 * @param string $class
	 * @return static
	 */
	public function remove_class( string $class ): self {
		$classes = $this->get_attribute( 'class' );
		if ( null === $classes ) {
			return $this;
		}

		$classes = explode( ' ', strval( $classes ) );
		if ( in_array( $class, $classes, true ) ) {
			$classes = array_diff( $classes, array( $class ) );
		}

		return $this->attribute( 'class', implode( ' ', $classes ) );
	}


	/**
	 * Set the ID
	 *
	 * @param string $id
	 * @return static
	 */
	public function id( string $id ): self {
		return $this->attribute( 'id', $id );
	}

	/**
	 * Add a data attribute.
	 *
	 * @param string $key
	 * @param string|int|float|bool|null $value
	 * @return static
	 */
	public function data( string $key, $value ): self {
		return $this->attribute( 'data-' . $key, $value );
	}


}
