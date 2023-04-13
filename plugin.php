<?php
/**
 * Plugin Name: Perique Form Components
 * Plugin URI: https://github.com/a8cteam51/can-we-all-go
 * Description: Testing plugins
 * Author: Glynn Quelch
 * Author URI: https://github.com/gin0115
 * Version: 1.0.0
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: form-comp
 *
 */

use PinkCrab\Perique\Application\App;
use PinkCrab\Perique\Application\Hooks;
use PinkCrab\Form_Components\Element\Field\Input\Text;
use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Component\Field\Label_Component;
use PinkCrab\Form_Components\Component\Field\Datalist_Component;
use PinkCrab\Form_Components\Component\Partial\Field_Wrapper_End;
use PinkCrab\Form_Components\Component\Partial\Field_Wrapper_Start;
// use PinkCrab\Form_Components\Component\Field\Input_Component;
// use PinkCrab\Form_Components\Component\Field\Input_Component;
// use PinkCrab\Form_Components\Component\Field\Input_Component;


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

return;
if ( isset( $_POST['submit'] ) ) {
	var_dump( $_POST );
};

?>

<form action="" method="post">
	<input type="time" name="time" id="time" value="12:00:00" >
<input type="color" id="appt" name="appt">
	<input type="submit" name="submit" value="Send">
	
</form>

<?php
die;
return;

// Require the autoloader so we can dynamically include the rest of the classes.
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

add_filter(
	Hooks::COMPONENT_ALIASES,
	function( array $aliases ): array {
		$custom_aliases = array(
			Input_Component::class     => __DIR__ . '/template/component/field/input-text.php',
			Field_Wrapper_Start::class => __DIR__ . '/template/component/partial/field-wrapper-start.php',
			Field_Wrapper_End::class   => __DIR__ . '/template/component/partial/field-wrapper-end.php',
			// Nonce_Component::class        => __DIR__ . '/template/component/partial/nonce.php',
			Label_Component::class     => __DIR__ . '/template/component/field/label.php',
			Datalist_Component::class  => __DIR__ . '/template/component/field/datalist.php',
			// Group_Component::class        => __DIR__ . '/template/component/partial/group.php',
			// Button_Component::class       => __DIR__ . '/template/component/field/button.php',
			// Notification_Component::class => __DIR__ . '/template/component/field/notification.php',
		);

		// @Todoc check if blade one active, then remap.
		return array_merge( $aliases, $custom_aliases );
	}
);


/** @var App $app */
$app = ( new \PinkCrab\Perique\Application\App_Factory( __DIR__ ) )
	->with_wp_dice( true )
	->app_config( array( 'path' => array( 'view' => __DIR__ . '/tests/Fixtures' ) ) )
	->boot();

add_action(
	'init',
	function() use ( $app ) {
		// return;
		if ( is_admin() ) {
			return;
		}
		// dump( $app );
		$app::view()->render(
			'tests/Fixtures/PHP/input-fields.php',
			array(
				'text' => ( new Text( 'input__text' ) )
					->label( 'Input Text Field' )
					->set_existing( 'Existing Value' )
					->id( 'input__text__id' )
					->add_class( 'input__text__class' )
					->data( 'input__text', 'data__value' )
					->attributes( array( 'input__text' => 'attr__value' ) )
					->add_wrapper_class( 'input__text__wrapper__class' )
					->datalist_items( array( 'one', 'two', 'three' ) ),
			)
		);

		die();
	},
	999
);

// function pages_with_category_and_tag_register(){
//   /*add categories and tags to pages*/
//   register_taxonomy_for_object_type('category', 'page');
//   register_taxonomy_for_object_type('post_tag', 'page');
// }
// add_action( 'init', 'pages_with_category_and_tag_register');

// function pages_with_category_and_tag_register_pre_get( $query ) {

//   if ( is_admin() || ! $query->is_main_query() ) {
//     return;
//   }
//   /*view categories and tags archive pages */
//   if($query->is_category && $query->is_main_query()){
//     $query->set('post_type', array( 'post', 'page'));
//   }
//   if($query->is_tag && $query->is_main_query()){
//     $query->set('post_type', array( 'post', 'page'));
//   }
// }
// add_action( 'pre_get_posts', 'pages_with_category_and_tag_register_pre_get' );
