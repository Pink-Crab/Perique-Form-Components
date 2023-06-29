<?php

declare( strict_types=1 );

/**
 * Component for rendering a Notification.
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

namespace PinkCrab\Form_Components\Component\Field;

use PinkCrab\Form_Components\Element\Element;
use PinkCrab\Perique\Services\View\Component\Component;
use PinkCrab\Form_Components\Element\Field_Traits\Form_Style;
use function PinkCrab\FunctionConstructors\Objects\usesTrait;
use PinkCrab\Form_Components\Element\Field\Attribute\Notification;

class Notification_Component extends Component {

	/** @var string */
	protected $notification;

	/** @var string */
	protected $wrapper_class;

	/**
	 * Creates an instance of the component.
	 *
	 * Element must use the Notification trait.
	 * Element must use the Form_Style trait.
	 *
	 * @param Element $element
	 */
	public function __construct( Element $element ) {
		// Ensure the element is a notification.
		if ( ! usesTrait( Notification::class )( $element ) ) {
			throw new \InvalidArgumentException( 'Element must implement Notification' );
		}

		// Ensure element has styles.
		if ( ! usesTrait( Form_Style::class )( $element ) ) {
			throw new \InvalidArgumentException( 'Element must have a style' );
		}

		$this->notification = esc_attr( $element->get_notification() ); // @phpstan-ignore-line, checks that method uses notifactions trait.

		// Set the wrapper class.
		$this->wrapper_class = sprintf(
			$element->get_style()->notification_wrapper_class(), // @phpstan-ignore-line, checks that method uses style trait.
			esc_attr( $element->get_notification_type() ) // @phpstan-ignore-line, checks that method uses notifactions trait.
		);
	}

}
