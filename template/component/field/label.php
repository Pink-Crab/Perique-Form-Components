<?php
/**
 * Component template for a label
 *
 * @package Perique\form-fields
 *
 * // Expected Variables
 * @var string $label
 * @var string $for
 */
?>

<label for="<?php echo esc_attr( $for ); ?>"><?php echo esc_html( $label ); ?></label>
