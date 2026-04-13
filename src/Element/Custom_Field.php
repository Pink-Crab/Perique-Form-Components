<?php

declare( strict_types=1 );

/**
 * Custom Field element.
 *
 * A field that renders arbitrary HTML content but with the full field treatment:
 * wrapper div, label, notification, style classes, before/after, sanitizer, validator.
 *
 * Supports custom wp_kses rules for content filtering. If no rules are provided,
 * defaults to wp_kses_post rules. Pass an empty array to skip kses filtering entirely.
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

use PinkCrab\Form_Components\Element\Field;
use PinkCrab\Form_Components\Element\Field\Attribute\Label;
use PinkCrab\Form_Components\Element\Field\Attribute\Single_Value;
use PinkCrab\Form_Components\Element\Field\Attribute\Notification;
use PinkCrab\Form_Components\Element\Field\Attribute\Description;

class Custom_Field extends Field {

	use Label, Single_Value, Notification, Description;

	/**
	 * The HTML content to render in the field slot.
	 *
	 * @var string
	 */
	protected $content = '';

	/**
	 * Optional callable that receives this field and returns HTML.
	 *
	 * @var callable|null
	 */
	protected $content_callback = null;

	/**
	 * Custom wp_kses allowed HTML rules.
	 * null = use wp_kses_post (default)
	 * array = use custom rules with wp_kses
	 *
	 * @var array<string, array<string, bool>>|null
	 */
	protected $kses_rules = null;

	/**
	 * Whether kses filtering is enabled.
	 *
	 * @var bool
	 */
	protected $kses_enabled = true;

	/** @inheritDoc */
	public function get_type(): string {
		return 'custom_field';
	}

	/** @inheritDoc */
	public function set_existing( $value ): self {
		$this->value( $value );
		return $this;
	}

	/**
	 * Sets the HTML content string.
	 *
	 * @param string $content
	 * @return static
	 */
	public function content( string $content ): self {
		$this->content = $content;
		return $this;
	}

	/**
	 * Sets a callable that receives this field and returns HTML.
	 *
	 * @param callable $callback fn(Custom_Field): string
	 * @return static
	 */
	public function content_callback( callable $callback ): self {
		$this->content_callback = $callback;
		return $this;
	}

	/**
	 * Gets the rendered content.
	 *
	 * If a callback is set, it takes priority over the string content.
	 *
	 * @return string
	 */
	public function get_content(): string {
		if ( null !== $this->content_callback ) {
			return call_user_func( $this->content_callback, $this );
		}
		return $this->content;
	}

	/**
	 * Checks if content has been set (string or callback).
	 *
	 * @return bool
	 */
	public function has_content(): bool {
		return '' !== $this->content || null !== $this->content_callback;
	}

	/**
	 * Sets custom wp_kses rules for content filtering.
	 *
	 * @param array<string, array<string, bool>> $rules
	 * @return static
	 */
	public function kses_rules( array $rules ): self {
		$this->kses_rules = $rules;
		return $this;
	}

	/**
	 * Gets the kses rules. Returns null if using default wp_kses_post.
	 *
	 * @return array<string, array<string, bool>>|null
	 */
	public function get_kses_rules(): ?array {
		return $this->kses_rules;
	}

	/**
	 * Disables kses filtering entirely.
	 *
	 * @return static
	 */
	public function disable_kses(): self {
		$this->kses_enabled = false;
		return $this;
	}

	/**
	 * Enables kses filtering.
	 *
	 * @return static
	 */
	public function enable_kses(): self {
		$this->kses_enabled = true;
		return $this;
	}

	/**
	 * Checks if kses filtering is enabled.
	 *
	 * @return bool
	 */
	public function is_kses_enabled(): bool {
		return $this->kses_enabled;
	}

	/**
	 * Filters the content through kses rules.
	 *
	 * @param string $content
	 * @return string
	 */
	public function filter_content( string $content ): string {
		if ( ! $this->kses_enabled ) {
			return $content;
		}

		if ( null !== $this->kses_rules ) {
			return wp_kses( $content, $this->kses_rules );
		}

		return wp_kses_post( $content );
	}
}
