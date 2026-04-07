<?php
/**
 * The opening wrapper of a field component
 *
 * @package Perique\form-fields
 *
 * // Expected Variables
 * @string $before_field
 * @string $wrapper_attributes
 */
?>
<div <?php echo ( $wrapper_attributes ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, parts escaped before composition. ?>>
	<?php if ( '' !== $before_field ) : ?>
		<?php echo wp_kses_post( $before_field ); ?> 
	<?php endif; ?>
	<?php // Start of rendered field component. ?>
