<?php
/**
 * CommentPress AJAX Infinite Scroll class.
 *
 * Handles AJAX infinite scroll functionality.
 *
 * @package CommentPress_Core
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * CommentPress AJAX Infinite Scroll Class.
 *
 * This class provides AJAX infinite scroll functionality.
 *
 * @since 4.0
 */
class CommentPress_AJAX_Infinite_Scroll {

	/**
	 * Core loader object.
	 *
	 * @since 4.0
	 * @access public
	 * @var CommentPress_Core_Loader
	 */
	public $core;

	/**
	 * AJAX loader object.
	 *
	 * @since 4.0
	 * @access public
	 * @var CommentPress_AJAX_Loader
	 */
	public $ajax;

	/**
	 * Relative path to the assets directory.
	 *
	 * @since 4.0
	 * @access private
	 * @var string
	 */
	private $assets_path = 'includes/core/assets/';

	/**
	 * Constructor.
	 *
	 * @since 4.0
	 *
	 * @param CommentPress_AJAX_Loader $ajax Reference to the AJAX loader object.
	 */
	public function __construct( $ajax ) {

		// Store references to loader objects.
		$this->ajax = $ajax;
		$this->core = $this->ajax->core;

		// Init when this plugin is fully loaded.
		add_action( 'commentpress/ajax/loaded', [ $this, 'initialise' ] );

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

		// Add our Javascripts.
		add_action( 'wp_enqueue_scripts', [ $this, 'scripts_enqueue' ], 120 );

		// Add AJAX infinite scroll functionality.
		add_action( 'wp_ajax_cpajax_load_next_page', [ $this, 'next_page_load' ] );
		add_action( 'wp_ajax_nopriv_cpajax_load_next_page', [ $this, 'next_page_load' ] );

	}

	// -------------------------------------------------------------------------

	/**
	 * Adds the Infinite Scroll scripts.
	 *
	 * @since 3.4
	 */
	public function scripts_enqueue() {

		// Can only now see $post.
		if ( ! $this->ajax->can_activate() ) {
			return;
		}

		// Allow this to be disabled.
		if ( apply_filters( 'cpajax_disable_infinite_scroll', false ) ) {
			return;
		}

		// Always load the Comment form, even if Comments are disabled.
		add_filter( 'commentpress_force_comment_form', '__return_true' );

		// Access globals.
		global $post;

		// Bail if we are we asking for a Special Page.
		if ( $this->core->pages_legacy->is_special_page() ) {
			return;
		}

		// Default to minified scripts.
		$min = commentpress_minified();

		// Add waypoints script.
		wp_enqueue_script(
			'cpajax-waypoints',
			plugins_url( $this->assets_path . 'js/jquery.waypoints' . $min . '.js', COMMENTPRESS_PLUGIN_FILE ),
			[ 'jquery' ], // Dependencies.
			COMMENTPRESS_VERSION, // Version.
			true
		);

		// Add infinite scroll script.
		wp_enqueue_script(
			'cpajax-infinite',
			plugins_url( $this->assets_path . 'js/cp-ajax-infinite' . $min . '.js', COMMENTPRESS_PLUGIN_FILE ),
			[ 'cpajax', 'cpajax-waypoints' ], // Dependencies.
			COMMENTPRESS_VERSION, // Version.
			true
		);

		// Init vars.
		$infinite = [];

		// Is "live" Comment refreshing enabled?
		$infinite['nonce'] = wp_create_nonce( 'cpajax_infinite_nonce' );

		// Use wp function to localise.
		wp_localize_script( 'cpajax', 'CommentpressAjaxInfiniteSettings', $infinite );

	}

	/**
	 * Loads the Next Page.
	 *
	 * @since 3.4
	 */
	public function next_page_load() {

		// Init data.
		$data = [
			'status' => 'failure',
		];

		// Error check.
		$nonce = isset( $_POST['cpajax_infinite_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['cpajax_infinite_nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'cpajax_infinite_nonce' ) ) {
			$data['status'] = __( 'Authentication failure.', 'commentpress-core' );
			wp_send_json( $data );
		}

		// Get incoming data.
		$current_post_id = isset( $_POST['current_post_id'] ) ? absint( $_POST['current_post_id'] ) : '';

		// Sanity check.
		if ( '' === $current_post_id ) {
			$data['status'] = __( 'Could not find current Post ID.', 'commentpress-core' );
			wp_send_json( $data );
		}

		// Get all Pages.
		$all_pages = $this->core->nav->document_pages_get_all( 'readable' );

		// If we have any Pages.
		if ( count( $all_pages ) === 0 ) {
			$data['status'] = __( 'Could not find any Pages.', 'commentpress-core' );
			wp_send_json( $data );
		}

		// Init the key we want.
		$page_key = false;

		// Loop.
		foreach ( $all_pages as $key => $page_obj ) {

			// Is it the currently viewed Page?
			if ( (int) $page_obj->ID === (int) $current_post_id ) {

				// Set Page key.
				$page_key = $key;

				// Bail to preserve key.
				break;

			}

		}

		// Die if we don't get a key.
		if ( false === $page_key ) {
			$data['status'] = __( 'Could not find a Page key.', 'commentpress-core' );
			wp_send_json( $data );
		}

		// Die if there is no next item.
		if ( ! isset( $all_pages[ $page_key + 1 ] ) ) {
			$data['status'] = __( 'Could not find the Page key in the array.', 'commentpress-core' );
			wp_send_json( $data );
		}

		// Get object.
		$new_post = $all_pages[ $page_key + 1 ];

		// Get Page data.
		$post = get_post( $new_post->ID );

		// Enable API.
		setup_postdata( $post );

		// Get title using buffer.
		ob_start();
		// phpcs:ignore Squiz.Commenting.InlineComment.InvalidEndChar
		// wp_title( '|', true, 'right' );
		bloginfo( 'name' );
		commentpress_site_title( '|' );
		$page_title = ob_get_contents();
		ob_end_clean();

		// Format title.
		$page_title = get_the_title( $post->ID ) . ' | ' . $page_title;

		// Get Next Page.

		// Get Feature Image.
		ob_start();
		commentpress_get_feature_image();
		$feature_image = ob_get_contents();
		ob_end_clean();

		// Get title.
		$title = '<h2 class="post_title"><a href="' . get_permalink( $post->ID ) . '">' . get_the_title( $post->ID ) . '</a></h2>';

		// Because AJAX may be routed via admin or front end.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && is_admin() ) {

			// Add CommentPress Core filter to the content when it's on the admin side.
			add_filter( 'the_content', [ $this->core->parser, 'the_content_parse' ], 20 );

		}

		// Get content.
		$content = apply_filters( 'the_content', $post->post_content );

		// Generate Page numbers.
		$this->core->nav->page_numbers_generate( $all_pages );

		// Get menu ID, if we have one.
		if ( isset( $new_post->menu_id ) ) {
			$menu_id = 'wpcustom_menuid-' . $new_post->menu_id;
		} else {
			$menu_id = 'wppage_menuid-' . $new_post->ID;
		}

		// Get Page number.
		$number = $this->core->nav->page_number_get( $post->ID );

		// Add Page number.
		if ( $number ) {
			// Is it arabic?
			if ( is_numeric( $number ) ) {
				$page_num = '<div class="running_header_bottom">page ' . $number . '</div>';
			} else {
				$page_num = '<div class="running_header_bottom">page ' . strtolower( $number ) . '</div>';
			}
		}

		// Init nav.
		$this->core->nav->setup_items();

		// Get Page navigation.
		$navigation = commentpress_page_navigation();
		if ( '' !== $navigation ) {
			$navigation = '<div class="page_navigation"><ul>' . $navigation . '</ul></div><!-- /page_navigation -->';
		}

		// Init upper nav.
		$upper_navigation = '';

		// Do we have a featured image?
		if ( ! commentpress_has_feature_image() ) {

			// Assign upper Page navigation.
			$upper_navigation = $navigation;

		} else {

			// We have a Feature Image - clear title in main body of content.
			$title = '';

		}

		// Always show lower nav.
		$lower_navigation = '<div class="page_nav_lower">' .
			$navigation .
		'</div><!-- /page_nav_lower -->';

		// Wrap in div.
		$data = '<div class="page_wrapper cp_page_wrapper">' .
			$feature_image .
			$upper_navigation .
			'<div class="content">' .
				'<div id="post-' . $post->ID . '" ' . esc_attr( implode( ' ', get_post_class( $menu_id, $post->ID ) ) ) . '>' .
					$title .
					$content .
					$page_num .
				'</div>' .
			'</div>' .
			$lower_navigation .
		'</div>';

		// Get Comments using buffer.
		ob_start();
		$vars = $this->core->display->get_javascript_vars();

		/**
		 * Try to locate template using WordPress method.
		 *
		 * @since 3.4
		 *
		 * @param str The existing path returned by WordPress.
		 * @return str The modified path.
		 */
		$cp_comments_by_para = apply_filters( 'cp_template_comments_by_para', locate_template( 'assets/templates/comments_by_para.php' ) );

		// Load it if we find it.
		if ( '' !== $cp_comments_by_para ) {
			load_template( $cp_comments_by_para );
		}

		$comments = ob_get_contents();
		ob_end_clean();

		// Wrap in div.
		$comments = '<div class="comments-for-' . $post->ID . '">' . $comments . '</div>';

		// Construct response.
		$response = [
			'post_id'        => $post->ID,
			'url'            => get_permalink( $post->ID ),
			'title'          => $page_title,
			'content'        => $data,
			'comments'       => $comments,
			'comment_status' => $post->comment_status,
		];

		// Send data to browser.
		wp_send_json( $data );

	}

}
