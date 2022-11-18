<?php
/**
 * CommentPress Core Revisions class.
 *
 * Handles "Revisions" workflow in CommentPress Core.
 *
 * @package CommentPress_Core
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * CommentPress Core Revisions Class.
 *
 * This class provides "Revisions" workflow to CommentPress Core.
 *
 * @since 4.0
 */
class CommentPress_Core_Revisions {

	/**
	 * Plugin object.
	 *
	 * @since 4.0
	 * @access public
	 * @var object $core The plugin object.
	 */
	public $core;

	/**
	 * Prevent save_post hook firing more than once.
	 *
	 * @since 3.3
	 * @access public
	 * @var str $saved_post True if post already saved.
	 */
	public $saved_post = false;

	/**
	 * Constructor.
	 *
	 * @since 4.0
	 *
	 * @param object $core Reference to the core plugin object.
	 */
	public function __construct( $core ) {

		// Store reference to core plugin object.
		$this->core = $core;

		// Init when this plugin is fully loaded.
		add_action( 'commentpress/core/loaded', [ $this, 'initialise' ] );

	}

	/**
	 * Initialises this object.
	 *
	 * @since 4.0
	 */
	public function initialise() {

		// Register hooks.
		$this->register_hooks();

	}

	/**
	 * Register WordPress hooks.
	 *
	 * @since 4.0
	 */
	public function register_hooks() {

		// Maybe create revision.
		add_action( 'commentpress/core/db/post_meta/saved', [ $this, 'create_revision' ] );

	}

	// -------------------------------------------------------------------------

	/**
	 * Maybe create a post revision.
	 *
	 * @since 4.0
	 *
	 * @param object $post The WordPress post object.
	 */
	public function create_revision( $post ) {

		// ---------------------------------------------------------------------
		// Create new post with content of current.
		// ---------------------------------------------------------------------

		// Do we want to create a new revision?
		$data = isset( $_POST['commentpress_new_post'] ) ? sanitize_text_field( wp_unslash( $_POST['commentpress_new_post'] ) ) : '0';
		if ( $data == '0' ) {
			return;
		}

		// We need to make sure this only runs once.
		if ( $this->saved_post === false ) {
			$this->saved_post = true;
		} else {
			return;
		}

		// ---------------------------------------------------------------------

		// We're through: create it.
		$new_post_id = $this->create_new_post( $post );

		// ---------------------------------------------------------------------
		// Store ID of new version in current version.
		// ---------------------------------------------------------------------

		// Set key.
		$key = '_cp_newer_version';

		// If the custom field already has a value.
		if ( get_post_meta( $post->ID, $key, true ) !== '' ) {

			// Delete the meta_key if empty string.
			if ( $data === '' ) {
				delete_post_meta( $post->ID, $key );
			} else {
				update_post_meta( $post->ID, $key, $new_post_id );
			}

		} else {

			// Add the data.
			add_post_meta( $post->ID, $key, $new_post_id );

		}

		// ---------------------------------------------------------------------
		// Store incremental version number in new version
		// ---------------------------------------------------------------------

		// Set key.
		$key = '_cp_version_count';

		// If the custom field of our current post has a value.
		if ( get_post_meta( $post->ID, $key, true ) !== '' ) {

			// Get current value.
			$value = get_post_meta( $post->ID, $key, true );

			// Increment.
			$value++;

		} else {

			// This must be the first new version (Draft 2).
			$value = 2;

		}

		// Add the data.
		add_post_meta( $new_post_id, $key, $value );

		// ---------------------------------------------------------------------
		// Store formatter in new version
		// ---------------------------------------------------------------------

		// Set key.
		$key = '_cp_post_type_override';

		// If we have one set.
		if ( get_post_meta( $post->ID, $key, true ) !== '' ) {

			// Get current value.
			$formatter = get_post_meta( $post->ID, $key, true );

			// Add the data.
			add_post_meta( $new_post_id, $key, esc_sql( $formatter ) );

		}

		/**
		 * Allow plugins to hook in.
		 *
		 * @since 3.3
		 *
		 * @param int $new_post_id The numeric ID of the new post.
		 */
		do_action( 'cp_workflow_save_copy', $new_post_id );

		/*
		// Get the edit post link.
		$edit_link = get_edit_post_link( $new_post_id );

		// Redirect there?
		*/

	}

	/**
	 * Create new post with content of existing.
	 *
	 * @since 3.4
	 * @since 4.0 Moved to this class.
	 *
	 * @param int $post The WordPress post object to make a copy of.
	 * @return int $new_post_id The numeric ID of the new post.
	 */
	public function create_new_post( $post ) {

		// Define basics.
		$new_post = [
			'post_status' => 'draft',
			'post_type' => 'post',
			'comment_status' => 'open',
			'ping_status' => 'open',
			'to_ping' => '', // Quick fix for Windows.
			'pinged' => '', // Quick fix for Windows.
			'post_content_filtered' => '', // Quick fix for Windows.
			'post_excerpt' => '', // Quick fix for Windows.
		];

		// Add post-specific stuff.

		// Default page title.
		$prefix = __( 'Copy of ', 'commentpress-core' );

		/**
		 * Allow overrides of prefix.
		 *
		 * @since 3.3
		 *
		 * @param str $prefix The existing prefix.
		 */
		$prefix = apply_filters( 'commentpress_new_post_title_prefix', $prefix );

		// Set title, but allow overrides.
		$new_post['post_title'] = apply_filters( 'commentpress_new_post_title', $prefix . $post->post_title, $post );

		// Set excerpt, but allow overrides.
		$new_post['post_excerpt'] = apply_filters( 'commentpress_new_post_excerpt', $post->post_excerpt );

		// Set content, but allow overrides.
		$new_post['post_content'] = apply_filters( 'commentpress_new_post_content', $post->post_content );

		// Set post author, but allow overrides.
		$new_post['post_author'] = apply_filters( 'commentpress_new_post_author', $post->post_author );

		// Insert the post into the database.
		$new_post_id = wp_insert_post( $new_post );

		// --<
		return $new_post_id;

	}

}