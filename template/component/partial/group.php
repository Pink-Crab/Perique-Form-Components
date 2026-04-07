<?php
/**
 * Template for rendering a group of Components
 *
 * @package Perique\form-fields
 *
 * // Expected Variables
 * @var Component $components
 * @string $before
 * @string $after
 * @string $attributes
 */

?>
<?php $this->component( new PinkCrab\Form_Components\Component\Partial\Field_Wrapper_Start( $attributes, $before ) ); ?> 
		<?php
		// Render each component.
		foreach ( $components as $component ) {
			$this->component( $component );
		}
		?>
<?php
$this->component( new PinkCrab\Form_Components\Component\Partial\Field_Wrapper_End( $after ) );
