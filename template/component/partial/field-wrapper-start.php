<?php
/**
 * The opening wrapper of a field component.
 *
 * @package Perique\form-fields
 *
 * // Expected Variables
 * @var string $wrapper_attributes Pre-escaped HTML attributes for the wrapper element.
 */
?>
<div <?php echo ( $wrapper_attributes ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, parts escaped before composition. ?>>
	<?php // Start of rendered field component. ?>
