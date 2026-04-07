<?php

declare( strict_types=1 );

/**
 * Raw HTML element.
 *
 * Used to render arbitrary HTML content within a form.
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

use PinkCrab\Form_Components\Element\Element;
use PinkCrab\Form_Components\Element\Field_Traits\Element_Wrap;

class Raw_HTML implements Element {

	use Element_Wrap;

	/**
	 * The element name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The raw HTML content.
	 *
	 * @var string
	 */
	protected $html = '';

	/**
	 * Constructs an instance of Raw_HTML.
	 *
	 * @param string $name
	 * @param string $html
	 */
	public function __construct( string $name, string $html = '' ) {
		$this->name = esc_attr( \sanitize_title( $name ) );
		$this->html = $html;
	}

	/**
	 * Static constructor.
	 *
	 * @param string $name
	 * @param string $html
	 * @return static
	 */
	public static function make( string $name, string $html = '' ): static {
		return new static( $name, $html ); // @phpstan-ignore new.static
	}

	/** @inheritDoc */
	public function get_name(): string {
		return $this->name;
	}

	/** @inheritDoc */
	public function get_type(): string {
		return 'raw_html';
	}

	/**
	 * Set the HTML content.
	 *
	 * @param string $html
	 * @return static
	 */
	public function html( string $html ): self {
		$this->html = $html;
		return $this;
	}

	/**
	 * Get the HTML content.
	 *
	 * @return string
	 */
	public function get_html(): string {
		return $this->html;
	}

	/**
	 * Check if HTML content has been set.
	 *
	 * @return bool
	 */
	public function has_html(): bool {
		return '' !== $this->html;
	}
}
