<?php
/**
 * CommentPress Core BuddyPress Theme compatibility.
 *
 * Handles Theme compatibility with BuddyPress.
 *
 * @package CommentPress_Core
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;



if ( ! function_exists( 'commentpress_fix_bp_core_avatar_url' ) ) :

	/**
	 * Filter to fix broken Group Avatar images in BuddyPress 1.7.
	 *
	 * @since 3.3
	 *
	 * @param string $url The existing URL of the avatar.
	 * @return string $url The modified URL of the avatar.
	 */
	function commentpress_fix_bp_core_avatar_url( $url ) {

		// If in multisite and on non-root Site.
		if ( is_multisite() && ! bp_is_root_blog() ) {

			// Switch to root Site.
			switch_to_blog( bp_get_root_blog_id() );

			// Get upload dir data.
			$upload_dir = wp_upload_dir();

			// Get storage location of avatars.
			$url = $upload_dir['baseurl'];

			// Switch back.
			restore_current_blog();

		}

		// --<
		return $url;

	}

endif;



if ( ! function_exists( 'commentpress_amend_search_query' ) ) :

	/**
	 * Restrict search query to Pages only.
	 *
	 * @since 3.3
	 *
	 * @param WP_Query $query The query object, passed by reference.
	 */
	function commentpress_amend_search_query( &$query ) {

		/*
		 * Restrict to search outside admin.
		 *
		 * Note: BuddyPress does a redirect to the Blog Page and so $query->is_search is not set.
		 */
		if ( ! is_admin() && ! empty( $query->query['s'] ) ) {

			// Is this a BuddyPress search on the main BuddyPress instance?
			if ( function_exists( 'bp_search_form_type_select' ) && bp_is_root_blog() ) {

				/**
				 * Filters the Post Types to search.
				 *
				 * Searches Posts and Pages by default.
				 *
				 * @since 3.8.2
				 *
				 * @param array The default array of Post Types to search.
				 */
				$query->set( 'post_type', apply_filters( 'commentpress_amend_search_query_post_types', [ 'post', 'page' ] ) );

				// Get core plugin reference.
				$core = commentpress_core();
				if ( ! empty( $core ) ) {

					// Get special Pages array, if it's there.
					$special_pages = $core->db->setting_get( 'cp_special_pages' );

					/**
					 * Filters the Special Pages search query exclusions.
					 *
					 * @since 3.8.2
					 *
					 * @param array The array of Special Pages.
					 */
					$special_pages = apply_filters( 'commentpress_amend_search_query_exclusions', $special_pages );

					// Exclude Special Pages if we have them.
					if ( is_array( $special_pages ) ) {
						$query->set( 'post__not_in', $special_pages );
					}

				}

			}

		}

	}

endif;

// Add callback for search query modification.
add_filter( 'pre_get_posts', 'commentpress_amend_search_query' );



if ( ! function_exists( 'commentpress_bp_activity_css_class' ) ) :

	/**
	 * Adds a Group Blog "Text Format" class to a BuddyPress Activity.
	 *
	 * @since 3.3
	 *
	 * @param str $existing_class The existing class.
	 * @return str $existing_class The overridden class.
	 */
	function commentpress_bp_activity_css_class( $existing_class ) {

		// Get current item.
		global $activities_template;
		$current_activity = $activities_template->activity;

		// Bail if not Group Activity.
		if ( 'groups' !== $current_activity->component ) {
			return $existing_class;
		}

		// Bail if we have no Group Blog Text Format.
		$groupblog_text_format = groups_get_groupmeta( $current_activity->item_id, 'groupblogtype' );
		if ( empty( $groupblog_text_format ) ) {
			return $existing_class;
		}

		// Append Group Blog Text Format class.
		$existing_class .= ' ' . $groupblog_text_format;

		// --<
		return $existing_class;

	}

endif;



if ( ! function_exists( 'commentpress_bp_blog_css_class' ) ) :

	/**
	 * Adds a Group Blog "Text Format" class to a BuddyPress Sites Directory item.
	 *
	 * @since 3.3
	 *
	 * @param array $classes The existing classes.
	 * @return array $classes The modified classes.
	 */
	function commentpress_bp_blog_css_class( $classes ) {

		// Bail if not a Group Blog.
		if ( ! function_exists( 'get_groupblog_group_id' ) ) {
			return $classes;
		}

		// Access BuddyPress object.
		global $blogs_template;

		// Bail if we have no Group ID.
		$group_id = get_groupblog_group_id( $blogs_template->blog->blog_id );
		if ( empty( $group_id ) || ! is_numeric( $group_id ) ) {
			return $classes;
		}

		// Bail if we have no Group Blog Text Format.
		$groupblog_text_format = groups_get_groupmeta( $group_id, 'groupblogtype' );
		if ( empty( $groupblog_text_format ) ) {
			return $classes;
		}

		// Add classes.
		$classes[] = 'bp-groupblog-blog';
		$classes[] = $groupblog_text_format;

		// --<
		return $classes;

	}

endif;



if ( ! function_exists( 'commentpress_bp_group_css_class' ) ) :

	/**
	 * Adds a Group Blog "Text Format" class to a BuddyPress Groups Directory item.
	 *
	 * @since 3.3
	 *
	 * @param array $classes The existing classes.
	 * @return array $classes The modified classes.
	 */
	function commentpress_bp_group_css_class( $classes ) {

		// Only add classes when BuddyPress Groupblog is active.
		if ( ! function_exists( 'get_groupblog_group_id' ) ) {
			return $classes;
		}

		// Bail if we have no Group Blog Text Format.
		$groupblog_text_format = groups_get_groupmeta( bp_get_group_id(), 'groupblogtype' );
		if ( empty( $groupblog_text_format ) ) {
			return $classes;
		}

		// Add class.
		$classes[] = $groupblog_text_format;

		// --<
		return $classes;

	}

endif;



if ( ! function_exists( 'commentpress_prefix_bp_templates' ) ) :

	/**
	 * Prefixes BuddyPress Pages with the div wrappers that CommentPress Core needs.
	 *
	 * @since 3.3
	 */
	function commentpress_prefix_bp_templates() {

		// Prefixed wrappers.
		echo '
		<div id="wrapper">
			<div id="main_wrapper" class="clearfix">
				<div id="page_wrapper">';

	}

endif;

// Add callback for the above.
add_action( 'bp_before_directory_groupsites_page', 'commentpress_prefix_bp_templates' );



if ( ! function_exists( 'commentpress_suffix_bp_templates' ) ) :

	/**
	 * Suffixes BuddyPress Pages with the div wrappers that CommentPress Core needs.
	 *
	 * @since 3.3
	 */
	function commentpress_suffix_bp_templates() {

		// Prefixed wrappers.
		echo '
				</div><!-- /page_wrapper -->
			</div><!-- /main_wrapper -->
		</div><!-- /wrapper -->';

	}

endif;

// Add callback for the above.
add_action( 'bp_after_directory_groupsites_page', 'commentpress_suffix_bp_templates' );



if ( ! function_exists( 'commentpress_unwrap_buddypress_button' ) ) :

	/**
	 * Removes the default wrapping of buttons in the directory nav.
	 *
	 * The BP_Button class was refactored in BuddyPress 2.7 and all buttons are
	 * wrapped in <div class="generic-button"></div> by default. This causes the
	 * link to pick up the style of a button, which breaks the nav menu layout.
	 * This filter unwraps the link so it appears the same as in BuddyPress 2.6.n.
	 *
	 * @since 3.9.1
	 *
	 * @param array $button_args The existing params used to define the button.
	 * @return array $button_args The modified params used to define the button.
	 */
	function commentpress_unwrap_buddypress_button( $button_args ) {

		// Bail if not BP 2.7.x.
		if ( ! function_exists( 'bp_core_filter_wp_query' ) ) {
			return $button_args;
		}

		// Remove parent element.
		$button_args['parent_element'] = '';

		// --<
		return $button_args;

	}

endif;

// Add callbacks for the above.
add_filter( 'bp_get_group_create_button', 'commentpress_unwrap_buddypress_button' );
add_filter( 'bp_get_blog_create_button', 'commentpress_unwrap_buddypress_button' );
