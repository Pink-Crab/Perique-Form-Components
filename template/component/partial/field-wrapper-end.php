<?php
/**
 * The closing of the wrapper of a field component
 *
 * @package Perique\form-fields
 *
 * // Expected Variables
 * @string $after_field
 */
?>
	<?php // End of rendered field component. ?>
	<?php if ( '' !== $after_field ) : ?>
		<?php echo wp_kses_post( $after_field ); ?> 
	<?php endif; ?>
</div> <?php // Closing wrapper. ?>
