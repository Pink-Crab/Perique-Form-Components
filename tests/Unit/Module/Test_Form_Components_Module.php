<?php

declare(strict_types=1);

/**
 * Unit tests for the Form_Components module.
 *
 * @since 0.1.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Form_Components\Tests\Unit\Module;

use WP_UnitTestCase;
use PinkCrab\Perique\Interfaces\Module;
use PinkCrab\Form_Components\Module\Form_Components;
use PinkCrab\Form_Components\Component\Field\Input_Component;
use PinkCrab\Form_Components\Component\Field\Label_Component;
use PinkCrab\Form_Components\Component\Field\Select_Component;
use PinkCrab\Form_Components\Component\Field\Raw_HTML_Component;
use PinkCrab\Form_Components\Component\Field\Textarea_Component;
use PinkCrab\Form_Components\Component\Field\Radio_Group_Component;
use PinkCrab\Form_Components\Component\Field\Checkbox_Group_Component;
use PinkCrab\Form_Components\Component\Field\Button_Component;
use PinkCrab\Form_Components\Component\Field\Datalist_Component;
use PinkCrab\Form_Components\Component\Field\Notification_Component;
use PinkCrab\Form_Components\Component\Field\Custom_Field_Component;
use PinkCrab\Form_Components\Component\Form\Form_Component;
use PinkCrab\Form_Components\Component\Form\Group_Component;
use PinkCrab\Form_Components\Component\Form\Fieldset_Component;
use PinkCrab\Form_Components\Component\Partial\Nonce_Component;
use PinkCrab\Form_Components\Component\Partial\Field_Wrapper_End;
use PinkCrab\Form_Components\Component\Partial\Field_Wrapper_Start;
use PinkCrab\Perique\Application\Hooks;

/**
 * @group unit
 * @group module
 */
class Test_Form_Components_Module extends WP_UnitTestCase {

	/** @testdox Form_Components should implement the Module interface */
	public function test_implements_module(): void {
		$module = new Form_Components();
		$this->assertInstanceOf( Module::class, $module );
	}

	/** @testdox get_middleware should return null */
	public function test_get_middleware_returns_null(): void {
		$module = new Form_Components();
		$this->assertNull( $module->get_middleware() );
	}

	/** @testdox The module constructor should register the component alias filter */
	public function test_constructor_registers_filter(): void {
		$module = new Form_Components();
		$this->assertNotFalse( has_filter( Hooks::COMPONENT_ALIASES ) );
	}

	/** @testdox The component alias filter should add all form component aliases */
	public function test_component_aliases_registered(): void {
		// Remove any existing filters first
		remove_all_filters( Hooks::COMPONENT_ALIASES );

		$module = new Form_Components();

		// Apply the filter with an empty array
		$aliases = apply_filters( Hooks::COMPONENT_ALIASES, array() );

		// Verify all expected component classes are registered
		$this->assertArrayHasKey( Input_Component::class, $aliases );
		$this->assertArrayHasKey( Textarea_Component::class, $aliases );
		$this->assertArrayHasKey( Select_Component::class, $aliases );
		$this->assertArrayHasKey( Raw_HTML_Component::class, $aliases );
		$this->assertArrayHasKey( Checkbox_Group_Component::class, $aliases );
		$this->assertArrayHasKey( Radio_Group_Component::class, $aliases );
		$this->assertArrayHasKey( Field_Wrapper_Start::class, $aliases );
		$this->assertArrayHasKey( Field_Wrapper_End::class, $aliases );
		$this->assertArrayHasKey( Nonce_Component::class, $aliases );
		$this->assertArrayHasKey( Label_Component::class, $aliases );
		$this->assertArrayHasKey( Datalist_Component::class, $aliases );
		$this->assertArrayHasKey( Button_Component::class, $aliases );
		$this->assertArrayHasKey( Notification_Component::class, $aliases );
		$this->assertArrayHasKey( Form_Component::class, $aliases );
		$this->assertArrayHasKey( Group_Component::class, $aliases );
		$this->assertArrayHasKey( Fieldset_Component::class, $aliases );
		$this->assertArrayHasKey( Custom_Field_Component::class, $aliases );
	}

	/** @testdox The template paths should point to existing files */
	public function test_template_paths_exist(): void {
		// Remove any existing filters first
		remove_all_filters( Hooks::COMPONENT_ALIASES );

		$module  = new Form_Components();
		$aliases = apply_filters( Hooks::COMPONENT_ALIASES, array() );

		foreach ( $aliases as $class => $path ) {
			$this->assertFileExists( $path, "Template not found for {$class}: {$path}" );
		}
	}

	/** @testdox The component aliases should merge with existing aliases */
	public function test_component_aliases_merge_with_existing(): void {
		// Remove any existing filters first
		remove_all_filters( Hooks::COMPONENT_ALIASES );

		$module = new Form_Components();

		$existing = array( 'ExistingComponent' => '/path/to/template.php' );
		$aliases  = apply_filters( Hooks::COMPONENT_ALIASES, $existing );

		// Should keep existing and add new
		$this->assertArrayHasKey( 'ExistingComponent', $aliases );
		$this->assertArrayHasKey( Input_Component::class, $aliases );
	}

	/** @testdox pre_boot should be callable without errors */
	public function test_pre_boot(): void {
		$module = new Form_Components();
		$module->pre_boot(
			new \PinkCrab\Perique\Application\App_Config(),
			new \PinkCrab\Loader\Hook_Loader(),
			$this->createMock( \PinkCrab\Perique\Interfaces\DI_Container::class )
		);
		$this->assertTrue( true ); // No exception thrown
	}

	/** @testdox pre_register should be callable without errors */
	public function test_pre_register(): void {
		$module = new Form_Components();
		$module->pre_register(
			new \PinkCrab\Perique\Application\App_Config(),
			new \PinkCrab\Loader\Hook_Loader(),
			$this->createMock( \PinkCrab\Perique\Interfaces\DI_Container::class )
		);
		$this->assertTrue( true ); // No exception thrown
	}

	/** @testdox post_register should be callable without errors */
	public function test_post_register(): void {
		$module = new Form_Components();
		$module->post_register(
			new \PinkCrab\Perique\Application\App_Config(),
			new \PinkCrab\Loader\Hook_Loader(),
			$this->createMock( \PinkCrab\Perique\Interfaces\DI_Container::class )
		);
		$this->assertTrue( true ); // No exception thrown
	}
}
