<?php

declare(strict_types=1);

/**
 * Plugin Bootstrap
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

namespace PinkCrab\Form_Components\Registration;

use PinkCrab\Perique\Application\Hooks;
use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Component\Field\Label_Component;
use PinkCrab\Form_Components\Component\Partial\Nonce_Component;
use PinkCrab\Form_Components\Component\Field\Datalist_Component;
use PinkCrab\Form_Components\Component\Partial\Field_Wrapper_End;
use PinkCrab\Form_Components\Component\Partial\Field_Wrapper_Start;

final class Form_Component_Bootstrap {

	private $lib_root = null;

	public function __construct( ?string $lib_root = null ) {
		// If no lib root is set, use the default.
		$this->lib_root = $lib_root ?? dirname( __DIR__, 2 );
	}

	/**
	 * Lazy constructor.
	 *
	 * @param string|null $lib_root
	 * @return self
	 */
	public static function init( ?string $lib_root = null ): self {
		$instance = new self( $lib_root );
		$instance->load();
		return $instance;
	}

	/**
	 * Called when first loaded.
	 *
	 * @return void
	 */
	public function load(): void {
		$this->include_template_paths();
	}

	/**
	 * Include the component template paths.
	 *
	 * @return void
	 */
	private function include_template_paths(): void {
		add_filter(
			Hooks::COMPONENT_ALIASES,
			function( array $aliases ): array {
				$custom_aliases = array(
					Input_Component::class     => $this->resolve_template_path( 'field/input-text.php' ),
					Field_Wrapper_Start::class => $this->resolve_template_path( 'partial/field-wrapper-start.php' ),
					Field_Wrapper_End::class   => $this->resolve_template_path( 'partial/field-wrapper-end.php' ),
					// Nonce_Component::class        => $this->resolve_template_path('partial/nonce.php'),
					Label_Component::class     => $this->resolve_template_path( 'field/label.php' ),
					Datalist_Component::class  => $this->resolve_template_path( 'field/datalist.php' ),
				// Group_Component::class        => $this->resolve_template_path('partial/group.php'),
				// Button_Component::class       => $this->resolve_template_path('field/button.php'),
				// Notification_Component::class => $this->resolve_template_path('field/notification.php'),
				);

				return array_merge( $aliases, $custom_aliases );
			},
			1
		);
	}

	/**
	 * Resolve the path to the template.
	 *
	 * @param string $template
	 * @return string
	 */
	private function resolve_template_path( string $template ): string {
		return $this->lib_root . '/template/component/' . rtrim( $template, \DIRECTORY_SEPARATOR );

	}
}
