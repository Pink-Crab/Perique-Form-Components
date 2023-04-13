<?php

declare( strict_types=1 );

/**
 * Trait used to hold fields.
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
use Respect\Validation\Validator;
use Respect\Validation\Validatable;
use PinkCrab\Form_Components\Element\Field;
use PinkCrab\Form_Components\Element\Element;
use function PinkCrab\FunctionConstructors\Arrays\mapKey;


trait Fields {

	/**
	 * The form fields
	 *
	 * @var Element[]
	 */
	protected $fields = array();

	/**
	 * Array of all field names.
	 *
	 * @var array<string>
	 */
	protected $field_names = array();


	/**
	 * Rules for the fields
	 *
	 * @var array<string, Validator>
	 */
	protected $validation_rules = array();

	/**
	 * Adds a field to the form.
	 *
	 * @param string $key
	 * @param class-string<Element> $field
	 * @param ?callable(Element):Element $config
	 * @return static
	 */
	public function add_field( string $key, string $field, ?callable $config = null ): self {

		$instance = new $field( $key, $this->form_style );

		// If a config is set, apply it.
		if ( $config ) {
			$instance = $config( $instance );
		}

		// Add to the fields array.
		$this->fields[ $key ] = $instance;

		$this->add_field_from_instance( $instance );

		return $this;
	}

	/**
	 * Sets all keys and validation rules from the passed field.
	 *
	 * @param Element $element
	 * @return void
	 */
	protected function add_field_from_instance( Element $element ): void {
		$fields = $this->get_sub_fields( $element );

		foreach ( $fields as $field ) {
			$this->field_names[ esc_attr( $field->get_name() ) ] = esc_attr( $field->get_name() );

			// Get the validation rules, if any.
			if ( Utils::class_uses_trait( $field, Validation::class )
			&& $field->has_validator()
			) {
				/** @var Validator $validator */
				$validator = $field->get_validator();
				$this->add_validation_rule( $field->get_name(), $validator );
			}
		}
	}

	/**
	 * Adds a validation rule to the field.
	 *
	 * @param string $key
	 * @param Validatable $validator
	 * @return static
	 */
	public function add_validation_rule( string $key, Validatable $validator ): self {
		$this->validation_rules[ $key ] = $validator;
		return $this;
	}

	/**
	 * Get all validation rules.
	 *
	 * @return array<string, Validator>
	 */
	public function get_validation_rules(): array {
		return $this->validation_rules;
	}

	/**
	 * Get all the sub fields, by recursively checking for sub fields.
	 *
	 * @param Element $field
	 * @return array<Field>
	 */
	protected function get_sub_fields( Element $field ): array {

		// If the field doesn't use this trait, return an array of just this field.
		if ( ! Utils::class_uses_trait( $field, Fields::class ) ) {
			return array( $field );
		}

		// Check if any sub fields have sub fields.
		$sub_fields = array();
		foreach ( $field->get_fields() as $sub_field ) {
			$sub_fields = array_merge( $sub_fields, $this->get_sub_fields( $sub_field ) );
		}

		return $sub_fields;
	}



	/**
	 * Checks if the form has a field.
	 *
	 * @param string $key
	 * @return bool
	 */
	public function has_field( string $key ): bool {
		$fields = $this->get_nested_fields();
		return array_key_exists( $key, $fields );
	}

	/**
	 * Returns the field.
	 *
	 * @param string $key
	 * @return ?Element
	 */
	public function get_field( string $key ): ?Element {
		$fields = $this->get_nested_fields();
		return $fields[ $key ] ?? null;
	}

	/**
	 * Get all fields.
	 *
	 * @return Element[]
	 */
	public function get_fields(): array {
		return $this->fields;
	}

	/**
	 * Get the field names
	 *
	 * @return array<string>
	 */
	public function get_field_names(): array {
		return $this->field_names;
	}

	/**
	 * Get all fields as an array.
	 *
	 * @return array<string, mixed>
	 */
	public function get_nested_fields(): array {
		$fields = array_map( array( $this, 'get_sub_fields' ), $this->fields );
		$fields = array_merge( ...array_values( $fields ) );
		$fields = mapKey(
			function( $field ) use ( $fields ) {
				return $fields[ $field ]->get_name();
			}
		)( $fields );
		return $fields;
	}
}
