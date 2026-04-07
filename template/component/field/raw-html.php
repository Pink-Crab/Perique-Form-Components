<?php
/**
 * The Raw HTML component template
 *
 * @package Perique\form-fields
 *
 * // Expected Variables
 * @var Raw_HTML $field
 * @var string $html
 */
?>
<?php echo wp_kses_post( $html ); ?>
