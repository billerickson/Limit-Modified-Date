<?php
/**
 * Plugin Name: Limit Modified Date
 * Plugin URI: https://github.com/billerickson/Limit-Modified-Date
 * Description: Prevent the "modified date" from changing when making minor changes to your content.
 * Version: 1.0.0
 * Author: Bill Erickson
 * Author URI: https://www.billerickson.net
 *
 */

class Limit_Modified_Date {

	private $meta_key = 'limit_modified_date';

	private $version = '1.0';

	function __construct() {

		// Use original modified date
		add_action('wp_insert_post_data', array( $this, 'use_original_modified_date' ), 20, 2 );

		// Checkbox in block editor
		add_action( 'init', array( $this, 'register_post_meta' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );

		// Checkbox in classic editor
		add_action( 'post_submitbox_misc_actions', array( $this, 'classic_editor_checkbox' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );

	}

	/*
	 * Use original modified date
	 *
	 * @param array Slashed post data
	 * @param array Raw post data
	 *
	 * @return array Slashed post data with modified post_modified and post_modified_gmt
	 * */
	function use_original_modified_date( $data, $postarr ) {

		// Block editor uses post meta
		$use_original = get_post_meta( $postarr['ID'], $this->meta_key, true );
		$last_modified = get_post_meta( $postarr['ID'], 'last_modified_date', true );

		if( $use_original && $last_modified ) {

			$data['post_modified'] = date( 'Y-m-d H:i:s', strtotime( $last_modified ) );
			$data['post_modified_gmt'] = get_gmt_from_date( $data['post_modified'] );

		// Classic editor
		} else {

			$use_original = isset( $_POST[ $this->meta_key ] ) ? filter_var( $_POST[ $this->meta_key ], FILTER_VALIDATE_BOOLEAN ) : false;
			if( $use_original ) {

				if( isset( $postarr['post_modified'] ) )
					$data['post_modified'] = $postarr['post_modified'];
				if( isset( $postarr['post_modified_gmt'] ) )
					$data['post_modified_gmt'] = $postarr['post_modified_gmt'];
			}
		}

		return $data;
	}

	/**
	 * Registers the custom post meta fields needed by the post type.
	 */
	function register_post_meta() {
		$args = array(
			'show_in_rest' => true,
			'single'       => true
		);

		register_meta( 'post', $this->meta_key, $args );
		register_meta( 'post', 'last_modified_date', $args );
	}

	/**
	 * Enqueues JavaScript and CSS for the block editor.
	 */
	function enqueue_block_editor_assets() {

		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		global $post;
		if( ! $this->is_supported_post_type( get_post_type( $post ) ) )
			return;

		wp_enqueue_script(
			'limit-modified-date-js',
			plugins_url( 'assets/js/editor.js', __FILE__ ),
			[
				'wp-components',
				'wp-data',
				'wp-edit-post',
				'wp-editor',
				'wp-element',
				'wp-i18n',
				'wp-plugins',
			],
			$this->version,
			true
		);

		wp_localize_script( 'limit-modified-date-js', 'limit_modified_date', array( 'current' => get_the_modified_time() ) );
	}

	/**
	 * Determine whether a post type supports custom links.
	 *
	 * @param string $type The post type to check.
	 * @return bool Whether this post type supports custom links.
	 */
	public static function is_supported_post_type( $type ) {
		if ( is_object( $type ) ) {
			if ( isset( $type->id ) ) {
				$type = $type->id;
			}
		}

		$supported_post_types = (array) apply_filters( 'limit_modified_date_post_types', array( 'post' ) );
		return in_array( $type, $supported_post_types );
	}

	/**
	 * Checkbox in classic editor
	 *
	 */
	function classic_editor_checkbox() {
		global $post;

		if( ! $this->is_supported_post_type( get_post_type( $post ) ) )
			return;

		wp_nonce_field( $this->meta_key, $this->meta_key . '_nonce' );
		$val = get_post_meta( $post->ID, $this->meta_key, true );

		echo '<div class="misc-pub-section">';
			echo '<input type="checkbox" name="' . $this->meta_key . '" id="' . $this->meta_key . '" value="1"' . checked( $val, '1', false ) . ' />';
			echo '<label for="' . $this->meta_key . '">' . __( 'Don\'t update the modified date', 'limit-modified-date' ) . '</label>';
		echo '</div>';
	}

	function save_post( $post_id ) {

		if ( ! isset( $_POST['post_type'] ) ) {
			return;
		}

		if ( ! isset( $_POST[ $this->meta_key . '_nonce'] ) || ! wp_verify_nonce( $_POST[ $this->meta_key . '_nonce' ], $this->meta_key ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( ! isset( $_POST['limit_modified_date'] ) ) {
			// editing the post causes it to be purged anyway, so just remove the meta
			delete_post_meta( $post_id, $this->meta_key );
		} else {
			if ( 1 === absint( $_POST['limit_modified_date'] ) ) {
				update_post_meta( $post_id, $this->meta_key, 1 );
			}
		}
	}
}

new Limit_Modified_Date();
