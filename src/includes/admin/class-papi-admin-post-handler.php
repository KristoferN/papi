<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Admin Post Handler.
 *
 * @package Papi
 * @since 1.0.0
 */

class Papi_Admin_Post_Handler extends Papi_Admin_Data_Handler {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */

	public function __construct() {
		$this->setup_actions();
	}

	/**
	 * Setup actions.
	 *
	 * @todo Try to get this working
	 * @since 1.0.0
	 */

	private function setup_actions() {
		add_action( 'save_post', [$this, 'save_meta_boxes'], 1, 2 );
	}

	/**
	 * Pre save page template and page type.
	 *
	 * @param int $post_id
	 *
	 * @since 1.0.0
	 */

	private function pre_save( $post_id ) {
		// Can't proceed without a post id.
		if ( is_null( $post_id ) ) {
			return;
		}

		$data = $this->get_pre_data();

		foreach ( $data as $key => $value ) {
			if ( empty( $value ) ) {
				continue;
			}

			update_post_meta( $post_id, $key, $value );
		}
	}

	/**
	 * Save meta boxes.
	 *
	 * @since 1.0.0
	 */

	public function save_meta_boxes() {
		// Fetch the post id.
		if ( isset( $_POST['post_ID'] ) ) {
			$post_id = papi_get_sanitized_post( 'post_ID' );
			$post_id = intval( $post_id );
		}

		// Can't proceed without a post id.
		if ( empty( $post_id ) ) {
			return;
		}

		// Check the post being saved has the same id as the post id. This will prevent other save post events.
		if ( papi_get_sanitized_post( 'post_ID' ) != strval( $post_id ) ) {
			return;
		}

		$post = get_post( $post_id );

		// Can't proceed without a post id or a post.
		if ( empty( $post ) ) {
			return;
		}

		// Don't save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Check if our nonce is vailed.
		if ( ! isset( $_POST['papi_meta_nonce'] ) || ! wp_verify_nonce( $_POST['papi_meta_nonce'], 'papi_save_data' ) ) {
			return;
		}

		// Check for any of the capabilities before we save the code.
		if ( ! current_user_can( 'edit_posts' ) || ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		// Convert post id to int if is a string.
		if ( is_string( $post_id ) ) {
			$post_id = intval( $post_id );
		}

		$this->save_property( $post_id );
	}

	/**
	 * Save property and property type.
	 *
	 * @param int $post_id
	 *
	 * @since 1.0.0
	 */

	public function save_property( $post_id ) {
		// Pre save page template, page type and some others dynamic values.
		$this->pre_save( $post_id );

		// Get properties data.
		$data = $this->get_post_data();

		// Prepare property data.
		$data = $this->prepare_properties_data( $data, $post_id );

		foreach ( $data as $key => $property ) {
			papi_property_update_meta( [
				'post_id'       => $post_id,
				'slug'          => $key,
				'type'          => $property['type'],
				'value'         => $property['value']
			] );
		}
	}
}

new Papi_Admin_Post_Handler;
