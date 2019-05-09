<?php
/**
 * Plugin Name: Limit Modified Date
 * Plugin URI: https://github.com/billerickson/Limit-Modified-Date
 * Description: Make minor changes to your content without changing the last modified date
 * Version: 1.0.0
 * Author: Bill Erickson
 * Author URI: https://www.billerickson.net
 *
 */

class Limit_Modified_Date {

	private $meta_key = 'limit_modified_date';

	private $version = '1.0';

	function __construct() {
		add_action( 'init', array( $this, 'register_post_meta' ) );
		add_filter( 'is_protected_meta', array( $this, 'meta_unprotect' ), 10, 2 );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
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

		$supported_post_types = (array) apply_filters( 'limit_modified_date_post_types', array_keys( get_post_types( array(
			'show_ui' => true,
		) ) ) );
		return in_array( $type, $supported_post_types );
	}

}
