<?php

declare( strict_types=1 );

/**
 * Form Component
 *
 * Renders a <form> element wrapping child field components.
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

namespace PinkCrab\Form_Components\Component\Form;

use PinkCrab\Form_Components\Element\Form;
use PinkCrab\Form_Components\Util\Attributes;
use PinkCrab\Form_Components\Component\Component_Factory;
use PinkCrab\Perique\Services\View\Component\Component;

class Form_Component extends Component {

	/** @var Form */
	protected $form;

	/** @var array<Component> */
	protected $components;

	/** @var string */
	protected $form_attributes;

	/** @var string */
	protected $before_form;

	/** @var string */
	protected $after_form;

	/**
	 * @param Form $form
	 */
	public function __construct( Form $form ) {
		$this->form = $form;

		// Convert child elements to components.
		$factory          = Component_Factory::instance();
		$this->components = $factory->from_elements( $form->get_fields() );

		// Build form attributes.
		$attributes           = $form->get_wrapper_attributes();
		$attributes['method'] = esc_attr( $form->get_method() );

		if ( $form->get_action() !== '' ) {
			$attributes['action'] = esc_url( $form->get_action() );
		}

		if ( $form->get_enctype() !== null ) {
			$attributes['enctype'] = esc_attr( $form->get_enctype() );
		}

		$this->form_attributes = Attributes::parse( $attributes );

		// Before/after content.
		$this->before_form = $form->get_before() ?? '';
		$this->after_form  = $form->get_after() ?? '';
	}
}
