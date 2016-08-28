<?php

class Papi_Lib_Fields_Option_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		global $current_screen;

		$current_screen = WP_Screen::get( 'admin_init' );

		$_SERVER['REQUEST_URI'] = 'http://site.com/?page=papi/options/header-option-type';

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );
	}

	public function tearDown() {
		parent::tearDown();
		$_SERVER['REQUEST_URI'] = '';

		global $current_screen;
		$current_screen = null;
	}

	public function test_papi_delete_option() {
		$this->assertFalse( papi_delete_option( 1 ) );
		$this->assertFalse( papi_delete_option( null ) );
		$this->assertFalse( papi_delete_option( true ) );
		$this->assertFalse( papi_delete_option( false ) );
		$this->assertFalse( papi_delete_option( [] ) );
		$this->assertFalse( papi_delete_option( (object) [] ) );
		$this->assertFalse( papi_delete_option( '' ) );
		$this->assertFalse( papi_delete_option( 'fake_slug' ) );
		update_option( 'name', 'Kalle' );
		$this->assertSame( 'Kalle', papi_get_option( 'name' ) );
		$this->assertTrue( papi_delete_option( 'name' ) );
		$this->assertNull( papi_get_option( 'name' ) );
	}

	public function test_papi_get_option() {
		$this->assertNull( papi_get_option( 'site' ) );
		$this->assertNull( papi_get_option( 1 ) );
		$this->assertNull( papi_get_option( null ) );
		$this->assertNull( papi_get_option( true ) );
		$this->assertNull( papi_get_option( false ) );
		$this->assertNull( papi_get_option( [] ) );
		$this->assertNull( papi_get_option( (object) [] ) );
		$this->assertNull( papi_get_option( '' ) );
		update_option( 'name', 'fredrik' );
		$this->assertSame( 'fredrik', papi_get_option( 'name' ) );
	}

	public function test_papi_option_shortcode() {
		update_option( 'name', 'fredrik' );

		$this->assertEmpty( papi_option_shortcode( [] ) );
		$this->assertSame( 'fredrik', papi_option_shortcode( [
			'slug' => 'name'
		] ) );

		$this->assertSame( '1, 2, 3', papi_option_shortcode( [
			'slug'    => 'numbers',
			'default' => [1, 2, 3]
		] ) );
	}

	public function test_papi_update_option() {
		$this->assertFalse( papi_update_option( 1 ) );
		$this->assertFalse( papi_update_option( null ) );
		$this->assertFalse( papi_update_option( true ) );
		$this->assertFalse( papi_update_option( false ) );
		$this->assertFalse( papi_update_option( [] ) );
		$this->assertFalse( papi_update_option( (object) [] ) );
		$this->assertFalse( papi_update_option( '' ) );
		$this->assertFalse( papi_update_option( 0, 'fake_slug' ) );
		$this->assertFalse( papi_update_option( 'fake_slug' ) );
		$this->assertFalse( papi_update_option( 93099, 'fake_slug' ) );
		$this->assertFalse( papi_update_option( 'fake_slug' ) );
		$this->assertTrue( papi_update_option( 'name', 'Kalle' ) );
		$this->assertSame( 'Kalle', papi_get_option( 'name' ) );
	}

	public function test_the_papi_option() {
		update_option( 'name', 'fredrik' );

		the_papi_option( 'name' );
		$this->expectOutputRegex( '/fredrik/' );

		the_papi_option( 'numbers', [1, 2, 3] );
		$this->expectOutputRegex( '/1\, 2\, 3/' );
	}
}
