<?php
/**
 * Template for rendering a group of components.
 *
 * @package Perique\form-fields
 *
 * // Expected Variables
 * @var Component[] $components Iterable of child components to render inside the group.
 * @var string      $before     HTML rendered before the group's children.
 * @var string      $after      HTML rendered after the group's children.
 * @var string      $attributes Pre-escaped HTML attributes for the group's wrapper element.
 */

?>
<?php $this->component( new PinkCrab\Form_Components\Component\Partial\Field_Wrapper_Start( $attributes ) ); ?>
		<?php if ( null !== $before && '' !== $before ) : ?>
			<?php echo wp_kses_post( $before ); ?>
		<?php endif; ?>
		<?php
		// Render each component.
		foreach ( $components as $component ) {
			$this->component( $component );
		}
		?>
		<?php if ( null !== $after && '' !== $after ) : ?>
			<?php echo wp_kses_post( $after ); ?>
		<?php endif; ?>
<?php
$this->component( new PinkCrab\Form_Components\Component\Partial\Field_Wrapper_End() );
