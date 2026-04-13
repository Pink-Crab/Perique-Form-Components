<?php

declare( strict_types=1 );

/**
 * Fieldset Component
 *
 * Renders a <fieldset> with optional <legend> wrapping child components.
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

use PinkCrab\Form_Components\Element\Fieldset;
use PinkCrab\Form_Components\Util\Attributes;
use PinkCrab\Form_Components\Component\Component_Factory;
use PinkCrab\Perique\Services\View\Component\Component;

class Fieldset_Component extends Component {

	/** @var Fieldset */
	protected $fieldset;

	/** @var array<Component> */
	protected $components;

	/** @var string */
	protected $fieldset_attributes;

	/** @var string|null */
	protected $legend;

	/** @var string */
	protected $legend_class;

	/** @var string */
	protected $before;

	/** @var string */
	protected $after;

	/** @var string|null */
	protected $pre_description;

	/** @var string|null */
	protected $post_description;

	/** @var string */
	protected $description_class;

	/**
	 * @param Fieldset $fieldset
	 */
	public function __construct( Fieldset $fieldset ) {
		$this->fieldset = $fieldset;

		// Convert child elements to components.
		$factory          = Component_Factory::instance();
		$this->components = $factory->from_elements( $fieldset->get_fields() );

		// Build fieldset attributes.
		$attributes = $fieldset->get_wrapper_attributes();

		if ( $fieldset->is_disabled() ) {
			$attributes['disabled'] = null;
		}

		$this->fieldset_attributes = Attributes::parse( $attributes );

		$this->legend            = $fieldset->get_legend();
		$this->legend_class      = $fieldset->get_style()->legend_class();
		$this->before            = $fieldset->get_before() ?? '';
		$this->after             = $fieldset->get_after() ?? '';
		$this->pre_description   = $fieldset->get_pre_description();
		$this->post_description  = $fieldset->get_post_description();
		$this->description_class = $fieldset->get_style()->description_class();
	}
}
