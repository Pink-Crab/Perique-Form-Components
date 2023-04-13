<?php

declare(strict_types=1);

/**
 * Adds an optional notification to the field.
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
 * @package PinkCrab\Form_Fields
 */

namespace PinkCrab\Form_Components\Element\Field\Attribute;

use PinkCrab\Form_Components\Style\Style;

use PinkCrab\Form_Components\Element\Field;
use PinkCrab\Form_Components\Element\Element;
use function PinkCrab\FunctionConstructors\GeneralFunctions\{pipe};

trait Notification {

	/**
	 * Ensures can only be used on actual fields.
	 */
	abstract public function add_wrapper_class( string $value): Element;
	abstract public function get_wrapper_attribute( string $attribute);
	abstract public function add_class( string $value): Element;
	abstract public function get_attribute( string $attribute);
	abstract public function get_style(): ?Style;


	/**
	 * Sets the select text
	 *
	 * @var ?string
	 */
	protected $notification = null;

	/**
	 * Notification type
	 *
	 * @var string
	 */
	protected $notification_type = 'info';

	/**
	 * Sets the notification text.
	 *
	 * @param ?string $notification
	 * @return static
	 */
	public function notification( string $notification, string $type = 'info' ): self {
		$this->remove_notifications();
		$this->notification      = $notification;
		$this->notification_type = $type;
		$this->set_notification_type();

		return $this;
	}

	/**
	 * Checks if the field has a notification
	 *
	 * @return bool
	 */
	public function has_notification(): bool {
		return ! is_null( $this->notification );
	}

	/**
	 * Gets the notification text.
	 *
	 * @return string
	 */
	public function get_notification(): string {
		return $this->notification;

	}

	/**
	 * Gets the notification type.
	 *
	 * @return string
	 */
	public function get_notification_type(): string {
		return $this->notification_type;
	}

	/**
	 * Create info notification
	 *
	 * @param string $notification
	 * @return static
	 */
	public function info_notification( string $notification ): self {
		return $this->notification( $notification, 'info' );
	}

	/**
	 * Create success notification
	 *
	 * @param string $notification
	 * @return static
	 */
	public function success_notification( string $notification ): self {
		return $this->notification( $notification, 'success' );
	}

	/**
	 * Create warning notification
	 *
	 * @param string $notification
	 * @return static
	 */
	public function warning_notification( string $notification ): self {
		return $this->notification( $notification, 'warning' );
	}

	/**
	 * Create error notification
	 *
	 * @param string $notification
	 * @return static
	 */
	public function error_notification( string $notification ): self {
		return $this->notification( $notification, 'error' );
	}

	/**
	 * Replace/set the notification type in wrapper and field classes.
	 *
	 * @return void
	 */
	protected function set_notification_type(): void {

		$notification = \sprintf( $this->get_style()->notification_template(), $this->get_notification_type() );

		$this->add_wrapper_class( $notification );
		$this->add_class( $notification );
	}

	/**
	 * Removes any notification classes from the wrapper and field.
	 *
	 * @return void
	 */
	protected function remove_notifications(): void {
		$this->notification      = null;
		$this->notification_type = 'info';

		$existing_notification_template = \sprintf( '#%s#', \str_replace( '%s', '(info|success|warning|error)', $this->get_style()->notification_template() ) );

		// Remove any possible wrapper classes.
		$wrapper = $this->get_wrapper_attribute( 'class' );
		if ( $wrapper ) {
			// Check if class contains any notification types.
			if ( preg_match( $existing_notification_template, $wrapper ) ) {
				$wrapper = preg_replace( $existing_notification_template, '', $wrapper );
				$this->wrapper_attribute( 'class', $wrapper );
			}
		}

		// Remove any possible field classes.
		$field = $this->get_attribute( 'class' );
		if ( $field ) {
			// Check if class contains any notification types.
			if ( preg_match( $existing_notification_template, $field ) ) {
				$field = preg_replace( $existing_notification_template, '', $field );
				$this->attribute( 'class', $field );
			}
		}
	}

}
