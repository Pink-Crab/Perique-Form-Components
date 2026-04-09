<?php

declare( strict_types=1 );

/**
 * Factory for creating form components from elements.
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

namespace PinkCrab\Form_Components\Component;

use PinkCrab\Form_Components\Util\Attributes;
use PinkCrab\Form_Components\Element\Form;
use PinkCrab\Form_Components\Element\Nonce;
use PinkCrab\Form_Components\Element\Raw_HTML;
use PinkCrab\Form_Components\Element\Field\Select;
use PinkCrab\Form_Components\Element\Field\Group\Radio_Group;
use PinkCrab\Form_Components\Element\Field\Group\Checkbox_Group;
use PinkCrab\Form_Components\Element\Field\Textarea;
use PinkCrab\Form_Components\Component\Form\Group_Component;
use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Component\Field\Button_Component;
use PinkCrab\Form_Components\Component\Form\Form_Component;
use PinkCrab\Form_Components\Component\Form\Fieldset_Component;
use PinkCrab\Form_Components\Component\Partial\Nonce_Component;
use PinkCrab\Form_Components\Component\Field\Select_Component;
use PinkCrab\Form_Components\Component\Field\Raw_HTML_Component;
use PinkCrab\Form_Components\Component\Field\Radio_Group_Component;
use PinkCrab\Form_Components\Component\Field\Checkbox_Group_Component;
use PinkCrab\Form_Components\Component\Field\Textarea_Component;
use PinkCrab\Perique\Services\View\Component\Component;
use PinkCrab\Form_Components\Element\Field\Input\Abstract_Input;
use PinkCrab\Form_Components\Element\Custom_Field;
use PinkCrab\Form_Components\Component\Field\Custom_Field_Component;
use PinkCrab\Form_Components\Element\{Field, Group, Fieldset, Element, Button};
use function PinkCrab\FunctionConstructors\Objects\isInstanceOf;

class Component_Factory {

	/**
	 * Return an instance from a static constructor
	 *
	 * @return Component_Factory
	 */
	public static function instance() {
		$class = self::class;
		return new $class();
	}

	/**
	 * Creates an array of components from an array of elements.
	 *
	 * @param array<Element> $elements
	 * @return array<Component>
	 */
	public function from_elements( array $elements ): array {
		return array_map(
			function( Element $element ): Component {
				return $this->from_element( $element );
			},
			$elements
		);
	}

	/**
	 * Create a component from a given element.
	 *
	 * @param Element $element
	 * @return Component
	 */
	public function from_element( Element $element ): Component {
		switch ( true ) {
			case $element instanceof Checkbox_Group:
				return $this->from_checkbox_group( $element );

			case $element instanceof Radio_Group:
				return $this->from_radio_group( $element );

			case $element instanceof Select:
				return $this->from_select( $element );

			case $element instanceof Textarea:
				return $this->from_textarea( $element );

			case $element instanceof Custom_Field:
				return $this->from_custom_field( $element );

			case $element instanceof Abstract_Input:
				return $this->from_field( $element );

			case $element instanceof Group:
				return $this->from_group( $element );

			case $element instanceof Fieldset:
				return new Fieldset_Component( $element );

			case $element instanceof Button:
				return $this->from_button( $element );

			case $element instanceof Raw_HTML:
				return $this->from_html( $element );

			case $element instanceof Nonce:
				return new Nonce_Component( $element );

			case $element instanceof Form:
				return new Form_Component( $element );

			default:
				throw new \InvalidArgumentException( 'Element is not a valid form element' );
		}
	}

	/**
	 * Create a component from a given input field.
	 *
	 * @param Abstract_Input $field
	 * @return Component
	 */
	public function from_field( Abstract_Input $field ): Component {
		return new Input_Component( $field );
	}

	/**
	 * Create a component from a Checkbox_Group field.
	 *
	 * @param Checkbox_Group $group
	 * @return Component
	 */
	public function from_checkbox_group( Checkbox_Group $group ): Component {
		return new Checkbox_Group_Component( $group );
	}

	/**
	 * Create a component from a Radio_Group field.
	 *
	 * @param Radio_Group $group
	 * @return Component
	 */
	public function from_radio_group( Radio_Group $group ): Component {
		return new Radio_Group_Component( $group );
	}

	/**
	 * Create a component from a Select field.
	 *
	 * @param Select $select
	 * @return Component
	 */
	public function from_select( Select $select ): Component {
		return new Select_Component( $select );
	}

	/**
	 * Create a component from a Textarea field.
	 *
	 * @param Textarea $textarea
	 * @return Component
	 */
	public function from_textarea( Textarea $textarea ): Component {
		return new Textarea_Component( $textarea );
	}

	/**
	 * Create component from Group element
	 *
	 * @param Group $group
	 * @return Component
	 */
	public function from_group( Group $group ): Component {

		// Add id to groups attributes if not set.
		$attributes = $group->get_wrapper_attributes();

		if ( ! $group->has_wrapper_attribute( 'id' ) ) {
			$attributes['id'] = esc_attr( "field_{$group->get_name()}_wrapper" );
		}

		return new Group_Component(
			array_map(
				function ( Element $element ): Component {
					return $this->from_element( $element );
				},
				$group->get_fields()
			),
			Attributes::parse( $attributes ),
			$group->get_before() ?? '',
			$group->get_after() ?? ''
		);
	}

	/**
	 * Create a component from a Button element.
	 *
	 * @param Button $button
	 * @return Component
	 */
	public function from_button( Button $button ): Component {
		return new Button_Component( $button );
	}

	/**
	 * Create a component from a Raw_HTML element.
	 *
	 * @param Raw_HTML $html
	 * @return Component
	 */
	public function from_html( Raw_HTML $html ): Component {
		return new Raw_HTML_Component( $html );
	}

	/**
	 * Create a component from a Custom_Field element.
	 *
	 * @param Custom_Field $field
	 * @return Component
	 */
	public function from_custom_field( Custom_Field $field ): Component {
		return new Custom_Field_Component( $field );
	}
}
