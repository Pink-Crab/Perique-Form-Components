<?php

declare( strict_types=1 );

/**
 * Group Component
 *
 * Renders a <div> wrapper containing child field components.
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

use PinkCrab\Perique\Services\View\Component\Component;

class Group_Component extends Component {

	/** @var array<Component> */
	protected $components;

	/** @var string */
	protected $attributes;

	/** @var string */
	protected $before;

	/** @var string */
	protected $after;

	/**
	 * @param array<Component> $components Child components already converted.
	 * @param string           $attributes Parsed HTML attributes string.
	 * @param string           $before     Content before fields.
	 * @param string           $after      Content after fields.
	 */
	public function __construct( array $components, string $attributes, string $before = '', string $after = '' ) {
		$this->components = $components;
		$this->attributes = $attributes;
		$this->before     = $before;
		$this->after      = $after;
	}
}
