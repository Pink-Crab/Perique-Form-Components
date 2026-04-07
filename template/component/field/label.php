<?php
/**
 * Component template for a label
 *
 * @package Perique\form-fields
 *
 * // Expected Variables
 * @var string $label
 * @var string $for_name
 * @var string $label_class
 */
?>

<label for="<?php echo esc_attr( $for_name ); ?>"<?php echo $label_class ? ' class="' . esc_attr( $label_class ) . '"' : ''; ?>><?php echo esc_html( $label ); ?></label>
