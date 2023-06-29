<?php

declare( strict_types=1 );

/**
 * Interface all form elements implement.
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

namespace PinkCrab\Form_Components\Element;

interface Element {

	/**
	 * Gets the value of name.
	 *
	 * @return string
	 */
	public function get_name(): string;

	/**
	 * Get the element type.
	 *
	 * @return string
	 */
	public function get_type(): string;

	// Ensure has all wrapper methods (uses the Wrapper_Attributes trait)

	/**
	 * All elements must have wrapper attributes.
	 *
	 * @return bool
	 */
	public function has_wrapper_attributes(): bool;

	/**
	 * Checks if the field has an attribute.
	 *
	 * @param string $attribute
	 * @return bool
	 */
	public function has_wrapper_attribute( string $attribute ): bool;

	/**
	 * Gets the value of an attribute.
	 *
	 * @param string $attribute
	 * @return string|int|float|bool|null
	 */
	public function get_wrapper_attribute( string $attribute );

	/**
	 * Get all attributes.
	 *
	 * @return array<string, string|int|float|bool|null>
	 */
	public function get_wrapper_attributes(): array;

	/**
	 * Sets the value of an attribute.
	 *
	 * @param string $attribute
	 * @param string|int|float|bool|null $value
	 * @return static
	 */
	public function wrapper_attribute( string $attribute, $value = null ): self;

	/**
	 * Sets an array of attributes.
	 *
	 * @param array<string, string|int|float|bool|null> $attributes
	 * @return static
	 */
	public function wrapper_attributes( array $attributes ): self;

	/**
	 * Removes an attribute if it exists.
	 *
	 * @param string $attribute
	 * @return static
	 */
	public function remove_wrapper_attribute( string $attribute ): self;

	// Helpers

	/**
	 * Adds a class to the class attribute.
	 *
	 * @param string $class
	 * @return static
	 */
	public function add_wrapper_class( string $class ): self;

	/**
	 * Remove a class from the class attribute.
	 *
	 * @param string $class
	 * @return static
	 */
	public function remove_wrapper_class( string $class ): self;


	/**
	 * Set the ID
	 *
	 * @param string $id
	 * @return static
	 */
	public function wrapper_id( string $id ): self;

	// /**
	//  * Get the wrapper ID
	//  *
	//  * @return string|null
	//  */
	// public function get_wrapper_id(): ?string;

	/**
	 * Add a data attribute.
	 *
	 * @param string $key
	 * @param string|int|float|bool|null $value
	 * @return static
	 */
	public function wrapper_data( string $key, $value ): self;

	// /**
	//  * Gets the wrapper class
	//  *
	//  * @return ?string
	//  */
	// public function get_wrapper_class(): ?string;

	// /**
	//  * Checks if a wrapper class is set.
	//  *
	//  * @return bool
	//  */
	// public function has_wrapper_class(): bool;
}
