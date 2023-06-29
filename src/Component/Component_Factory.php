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

use PinkCrab\Form_Components\Utils;
use PinkCrab\Form_Components\Component\Form\Group_Component;
use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Component\Field\Button_Component;
use PinkCrab\Perique\Services\View\Component\Component;
use function PinkCrab\FunctionConstructors\Objects\isInstanceOf;
use PinkCrab\Form_Components\Element\{Field, Group, Fieldset, Element, Button};

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
			case $element instanceof Field:
				/*@var Field $element */
				$element = $element;
				return $this->from_field( $element );

			case $element instanceof Group:
				return $this->from_group( $element );

			// case Utils::class_uses_trait( $element, Fieldset::class ):
			// 	return $this->from_fieldset( $element );

			case $element instanceof Button:
				return $this->from_button( $element );

			// case Utils::class_uses_trait( $element, Raw_HTML::class ):
			// 	return $this->from_html( $element );

			default:
				throw new \InvalidArgumentException( 'Element is not a valid form element' );
		}
	}

	/**
	 * Create a component from a given field.
	 *
	 * @param Field $field
	 * @return Component
	 */
	public function from_field( Field $field ): Component {
		return new Input_Component( $field );
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
				function( Element $element ): Component {
					return $this->from_element( $element );
				},
				$group->get_fields()
			),
			Utils::parse_attributes( $attributes ),
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

	// /**
	//  * Create a component from a Raw_HTML element.
	//  *
	//  * @param Raw_HTML $html
	//  * @return Component
	//  */
	// public function from_html( Raw_HTML $html ): Component {
	// 	return new Component( $html );
	// }


}
