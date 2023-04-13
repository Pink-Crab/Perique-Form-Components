<?php

declare(strict_types=1);

/**
 * Datalist property.
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

use PinkCrab\Form_Components\Util\Esc;
use PinkCrab\Form_Components\Util\Arrays;

trait Datalist {

	/**
	 * Ensure only used when the field has attributes.
	 *
	 * @return bool
	 */
	abstract public function has_attributes(): bool;

	/**
	 * The format for the datalist key.
	 *
	 * @var string|null
	 */
	protected $datalist_key;

	/**
	 * Datalist Items
	 *
	 * @var array<string, string|null> $datalist_items
	 */
	protected $datalist_items = array();

	/**
	 * Gets the defined datalist key.
	 *
	 * @return string
	 */
	public function get_datalist_key(): string {
		$template = $this->datalist_key ?? '_{name}__list';

		// Replace placeholders.
		return \str_replace(
			array( '{name}', '{id}', '{hash}' ),
			array( $this->get_name(), $this->get_attribute('id'), md5( \json_encode( $this ) ?: get_class() ) ), //phpcs:ignore
			$template
		);
	}

	/**
	 * Sets the datalist key
	 *
	 * @param string $key
	 * @return self
	 */
	public function datalist_key( string $key ): self {
		$this->datalist_key = $key;
		$this->attribute( 'list', $this->get_datalist_key() );

		return $this;
	}

	/**
	 * Add an array of options to the datalist.
	 *
	 * @param array<string, string|null> $options
	 * @return self
	 */
	public function datalist_items( array $options ): self {

		// If the options are a list, convert to key value.
		if ( Arrays::is_list( $options ) ) {
			$options = \array_flip( $options );
		}

		foreach ( $options as $value => $label ) {
			$this->datalist_item( $value, is_string( $label ) ? $label : null );
		}

		return $this;
	}

	/**
	 * Adds a single options
	 *
	 * @param string $value
	 * @param string|null $label An option label to be used
	 * @return static
	 */
	public function datalist_item( string $value, ?string $label = null ) {
		// If no datalist key has been set, set it.
		if ( ! $this->datalist_key ) {
			$this->datalist_key( $this->get_datalist_key() );
		}

		$this->datalist_items[ Esc::attribute( $value ) ] = Esc::attribute( $label );
		return $this;
	}

	/**
	 * Checks if the datalist has any items
	 *
	 * @return bool
	 */
	public function has_datalist_items(): bool {
		return ! empty( $this->datalist_items );
	}

	/**
	 * Gets the list items.
	 *
	 * @return array<string, string|null>
	 */
	public function get_datalist_items(): array {
		return $this->datalist_items;
	}



}
