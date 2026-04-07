<?php

declare( strict_types=1 );

/**
 * Nonce element.
 *
 * Renders a WordPress nonce hidden field.
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

use PinkCrab\Form_Components\Element\Field_Traits\Wrapper_Attributes;

class Nonce implements Element {

	use Wrapper_Attributes;

	/**
	 * The nonce action.
	 *
	 * @var string
	 */
	protected string $action;

	/**
	 * The nonce field name.
	 *
	 * @var string
	 */
	protected string $name;

	/**
	 * @param string $action The nonce action.
	 * @param string $name   The nonce field name.
	 */
	public function __construct( string $action, string $name = '_wpnonce' ) {
		$this->action = esc_attr( $action );
		$this->name   = esc_attr( $name );
	}

	/**
	 * Static constructor.
	 *
	 * @param string $action The nonce action.
	 * @param string $name   The nonce field name.
	 * @return static
	 */
	public static function make( string $action, string $name = '_wpnonce' ): static {
		return new static( $action, $name ); // @phpstan-ignore new.static
	}

	/**
	 * Get the nonce action.
	 *
	 * @return string
	 */
	public function get_action(): string {
		return $this->action;
	}

	/** @inheritDoc */
	public function get_name(): string {
		return $this->name;
	}

	/** @inheritDoc */
	public function get_type(): string {
		return 'nonce';
	}
}
