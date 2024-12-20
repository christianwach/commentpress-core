<?php
/**
 * CommentPress Core Theme Comment functions.
 *
 * Handles common Comment functionality in CommentPress themes.
 *
 * @package CommentPress_Core
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;



if ( ! function_exists( 'commentpress_get_user_link' ) ) :

	/**
	 * Get User link in vanilla WordPress scenarios.
	 *
	 * In default single install mode, just link to their URL, unless they are an
	 * author, in which case we link to their Author Page. In multisite, the same.
	 * When BuddyPress is enabled, always link to their profile.
	 *
	 * @since 3.0
	 *
	 * @param WP_User    $user The WordPress User object.
	 * @param WP_Comment $comment The WordPress Comment object.
	 * @return string $url The URL for the User.
	 */
	function commentpress_get_user_link( $user, $comment = null ) {

		// Bail if not a User.
		if ( ! ( $user instanceof WP_User ) ) {
			return '';
		}

		// Get core plugin reference.
		$core = commentpress_core();

		// If BuddyPress.
		if ( ! empty( $core ) && $core->bp->is_buddypress() ) {

			// BuddyPress link - $no_anchor = null, $just_link = true.
			$url = bp_core_get_userlink( $user->ID, null, true );

		} else {

			// Get standard WordPress author URL.
			$url = get_author_posts_url( $user->ID );

			// WordPress sometimes leaves 'http://' or 'https://' in the field.
			if ( 'http://' === $url || 'https://' === $url ) {
				$url = '';
			}

		}

		/**
		 * Filters User link.
		 *
		 * @since 3.9
		 *
		 * @param string $url The URL for the User.
		 * @param WP_User The WordPress User object.
		 * @param WP_Comment The WordPress Comment object.
		 */
		return apply_filters( 'commentpress_get_user_link', $url, $user, $comment );

	}

endif;



if ( ! function_exists( 'commentpress_format_comment' ) ) :

	/**
	 * Format Comment on custom CommentPress Core Comments Pages.
	 *
	 * @since 3.0
	 *
	 * @param WP_Comment $comment The Comment object.
	 * @param string     $context Either "all" for all-comments or "by" for comments-by-commenter.
	 * @return string The formatted Comment HTML.
	 */
	function commentpress_format_comment( $comment, $context = 'all' ) {

		// Construct link.
		$comment_link = get_comment_link( $comment->comment_ID );

		// Construct anchor.
		$comment_anchor = '<a href="' . $comment_link . '" title="' . esc_attr( __( 'See comment in context', 'commentpress-core' ) ) . '">' . __( 'Comment', 'commentpress-core' ) . '</a>';

		// Construct date.
		$comment_date = date_i18n( get_option( 'date_format' ), strtotime( $comment->comment_date ) );

		// If context is 'all comments'.
		if ( 'all' === $context ) {

			// Get author.
			if ( ! empty( $comment->comment_author ) ) {

				// Was it a registered User?
				if ( 0 !== (int) $comment->user_id ) {

					// Get User details.
					$user = get_userdata( $comment->user_id );

					// Get User link.
					$user_link = commentpress_get_user_link( $user, $comment );

					// Did we get one?
					if ( ! empty( $user_link ) && 'http://' !== $user_link ) {

						// Construct link to User URL.
						$comment_author = '<a href="' . $user_link . '">' . $comment->comment_author . '</a>';

					} else {

						// Just show author name.
						$comment_author = $comment->comment_author;

					}

				} else {

					// Do we have an author URL?
					if ( ! empty( $comment->comment_author_url ) && 'http://' !== $comment->comment_author_url ) {

						// Construct link to User URL.
						$comment_author = '<a href="' . $comment->comment_author_url . '">' . $comment->comment_author . '</a>';

					} else {

						// Define context.
						$comment_author = $comment->comment_author;

					}

				}

			} else {

				// We don't have a name.
				$comment_author = __( 'Anonymous', 'commentpress-core' );

			}

			// Construct Comment header content.
			$comment_meta_content = sprintf(
				/* translators: 1: Comment link, 2: Comment author, 3: Comment date. */
				__( '%1$s by %2$s on %3$s', 'commentpress-core' ),
				$comment_anchor,
				$comment_author,
				$comment_date
			);

			// Wrap Comment meta in a div.
			$comment_meta = '<div class="comment_meta">' . $comment_meta_content . '</div>' . "\n";

			/**
			 * Filters the Comment meta markup in an "All Comments" context.
			 *
			 * @since 3.4
			 *
			 * @param string $comment_meta The Comment meta markup.
			 * @param WP_Comment $comment The WordPress Comment object.
			 * @param string $comment_anchor The Comment anchor tag.
			 * @param string $comment_author The Comment author tag.
			 * @param string $comment_date The Comment date tag.
			 */
			$comment_meta = apply_filters( 'commentpress_format_comment_all_meta', $comment_meta, $comment, $comment_anchor, $comment_author, $comment_date );

		} elseif ( 'by' === $context ) {

			// Context is 'by commenter'.

			// Construct link.
			$page_link = trailingslashit( get_permalink( $comment->comment_post_ID ) );

			// Construct Page anchor.
			$page_anchor = '<a href="' . $page_link . '">' . get_the_title( $comment->comment_post_ID ) . '</a>';

			// Construct Comment header content.
			$comment_meta_content = sprintf(
				/* translators: 1: Comment link, 2: Comment author, 3: Comment date. */
				__( '%1$s on %2$s on %3$s', 'commentpress-core' ),
				$comment_anchor,
				$page_anchor,
				$comment_date
			);

			// Wrap Comment meta in a div.
			$comment_meta = '<div class="comment_meta">' . $comment_meta_content . '</div>' . "\n";

			/**
			 * Filters the Comment meta markup in a "By Commenter" context.
			 *
			 * @since 3.4
			 *
			 * @param string $comment_meta The Comment meta markup.
			 * @param WP_Comment $comment The WordPress Comment object.
			 * @param string $comment_anchor The Comment anchor tag.
			 * @param string $page_anchor The Comment author tag.
			 * @param string $comment_date The Comment date tag.
			 */
			$comment_meta = apply_filters( 'commentpress_format_comment_by_meta', $comment_meta, $comment, $comment_anchor, $page_anchor, $comment_date );

		}

		// Render Comment content via built-in WordPress filter.
		$comment_body = '<div class="comment-content">' . apply_filters( 'comment_text', $comment->comment_content ) . '</div>' . "\n";

		// Construct Comment.
		return '<div class="comment_wrapper">' . "\n" . $comment_meta . $comment_body . '</div>' . "\n\n";

	}

endif;



if ( ! function_exists( 'commentpress_get_comments_by_content' ) ) :

	/**
	 * "Comments-by" Page display function.
	 *
	 * @todo Do we want trackbacks?
	 *
	 * @since 3.0
	 *
	 * @return str $html The HTML for the Page.
	 */
	function commentpress_get_comments_by_content() {

		// Init return.
		$html = '';

		// Build query.
		$query = [
			'status'  => 'approve',
			'orderby' => 'comment_author, comment_post_ID, comment_date',
			'order'   => 'ASC',
		];

		// Get all approved Comments.
		$all_comments = get_comments( $query );

		// Kick out if none.
		if ( count( $all_comments ) === 0 ) {
			return $html;
		}

		// Build list of authors.
		$authors_with = [];
		$author_names = [];
		/* $post_comment_counts = []; */

		foreach ( $all_comments as $comment ) {

			// Add to authors with Comments array.
			if ( ! in_array( $comment->comment_author_email, $authors_with, true ) ) {
				$authors_with[] = $comment->comment_author_email;
				$name           = ! empty( $comment->comment_author ) ? $comment->comment_author : __( 'Anonymous', 'commentpress-core' );
				$author_names[ $comment->comment_author_email ] = $name;
			}

			/*
			// Increment counter.
			if ( ! isset( $post_comment_counts[$comment->comment_author_email] ) ) {
				$post_comment_counts[$comment->comment_author_email] = 1;
			} else {
				$post_comment_counts[$comment->comment_author_email]++;
			}
			*/

		}

		// Kick out if none.
		if ( count( $authors_with ) === 0 ) {
			return $html;
		}

		// Open ul.
		$html .= '<ul class="all_comments_listing">' . "\n\n";

		// Loop through authors.
		foreach ( $authors_with as $author ) {

			// Open li.
			$html .= '<li class="author_li"><!-- author li -->' . "\n\n";

			// Add gravatar.
			$html .= '<h3>' . get_avatar( $author, $size = '24' ) . esc_html( $author_names[ $author ] ) . '</h3>' . "\n\n";

			// Open Comments div.
			$html .= '<div class="item_body">' . "\n\n";

			// Open ul.
			$html .= '<ul class="item_ul">' . "\n\n";

			// Loop through Comments.
			foreach ( $all_comments as $comment ) {

				// Does it belong to this author?
				if ( $author === $comment->comment_author_email ) {

					// Open li.
					$html .= '<li class="item_li"><!-- item li -->' . "\n\n";

					// Show the Comment.
					$html .= commentpress_format_comment( $comment, 'by' );

					// Close li.
					$html .= '</li><!-- /item li -->' . "\n\n";

				}

			}

			// Close ul.
			$html .= '</ul>' . "\n\n";

			// Close item div.
			$html .= '</div><!-- /item_body -->' . "\n\n";

			// Close li.
			$html .= '</li><!-- /.author_li -->' . "\n\n\n\n";

		}

		// Close ul.
		$html .= '</ul><!-- /.all_comments_listing -->' . "\n\n";

		// --<
		return $html;

	}

endif;



if ( ! function_exists( 'commentpress_get_comments_by_page_content' ) ) :

	/**
	 * "Comments-by" Page display wrapper function.
	 *
	 * @since 3.0
	 *
	 * @return str $page_content The Page content.
	 */
	function commentpress_get_comments_by_page_content() {

		// Allow oEmbed in Comments.
		global $wp_embed;
		if ( $wp_embed instanceof WP_Embed ) {
			add_filter( 'comment_text', [ $wp_embed, 'autoembed' ], 1 );
		}

		// Get data.
		$page_content = commentpress_get_comments_by_content();

		// --<
		return $page_content;

	}

endif;



if ( ! function_exists( 'commentpress_get_comment_activity' ) ) :

	/**
	 * Activity sidebar display function.
	 *
	 * @todo Do we want trackbacks?
	 *
	 * @since 3.3
	 *
	 * @param str $scope The scope of the Activities.
	 * @return str $page_content The HTML output for Activities.
	 */
	function commentpress_get_comment_activity( $scope = 'all' ) {

		// Allow oEmbed in Comments.
		global $wp_embed;
		if ( $wp_embed instanceof WP_Embed ) {
			add_filter( 'comment_text', [ $wp_embed, 'autoembed' ], 1 );
		}

		// Declare access to globals.
		global $post;

		// Init Page content.
		$page_content = '';

		// Define defaults.
		$args = [
			'number' => 10,
			'status' => 'approve',
			// Exclude trackbacks and pingbacks until we decide what to do with them.
			'type'   => '',
		];

		// If we are on a 404, for example.
		if ( 'post' === $scope && is_object( $post ) ) {

			// Get all Comments.
			$args['post_id'] = $post->ID;

		}

		// Get 'em.
		$data = get_comments( $args );

		// Did we get any?
		if ( count( $data ) > 0 ) {

			// Init Comments array.
			$comments_array = [];

			// Loop.
			foreach ( $data as $comment ) {

				// Exclude Comments from password-protected Posts.
				if ( ! post_password_required( $comment->comment_post_ID ) ) {
					$comment_markup = commentpress_get_comment_activity_item( $comment );
					if ( ! empty( $comment_markup ) ) {
						$comments_array[] = $comment_markup;
					}
				}

			}

			// Wrap in list if we get some.
			if ( ! empty( $comments_array ) ) {

				// Open ul.
				$page_content .= '<ol class="comment_activity">' . "\n\n";

				// Add Comments.
				$page_content .= implode( '', $comments_array );

				// Close ul.
				$page_content .= '</ol><!-- /comment_activity -->' . "\n\n";

			}

		}

		// --<
		return $page_content;

	}

endif;



if ( ! function_exists( 'commentpress_get_comment_activity_item' ) ) :

	/**
	 * Get Comment formatted for the Activity Sidebar.
	 *
	 * @since 3.3
	 *
	 * @param WP_Comment $comment The WordPress Comment object.
	 * @return string $item_html The modified Comment HTML.
	 */
	function commentpress_get_comment_activity_item( $comment ) {

		// Declare access to globals.
		global $post;

		// Init markup.
		$item_html = '';

		// Only Comments until we decide what to do with pingbacks and trackbacks.
		if ( 'pingback' === $comment->comment_type ) {
			return $item_html;
		}
		if ( 'trackback' === $comment->comment_type ) {
			return $item_html;
		}

		// Test for anonymous Comment - usually generated by WordPress itself in multisite installs.
		if ( empty( $comment->comment_author ) ) {
			$comment->comment_author = __( 'Anonymous', 'commentpress-core' );
		}

		// Was it a registered User?
		if ( ! empty( $comment->user_id ) ) {

			// Get User details.
			$user = get_userdata( $comment->user_id );

			// Get User link.
			$user_link = commentpress_get_user_link( $user, $comment );

			// Construct author citation.
			$author = '<cite class="fn"><a href="' . $user_link . '">' . get_comment_author( $comment->comment_ID ) . '</a></cite>';

			// Construct link to User URL.
			$author = ( ! empty( $user_link ) && 'http://' !== $user_link ) ?
				'<cite class="fn"><a href="' . esc_url( $user_link ) . '">' . get_comment_author( $comment->comment_ID ) . '</a></cite>' :
				'<cite class="fn">' . get_comment_author( $comment->comment_ID ) . '</cite>';

		} else {

			// Construct link to commenter URL.
			$author = ( ! empty( $comment->comment_author_url ) && 'http://' !== $comment->comment_author_url ) ?
				'<cite class="fn"><a href="' . esc_url( $comment->comment_author_url ) . '">' . get_comment_author( $comment->comment_ID ) . '</a></cite>' :
				'<cite class="fn">' . get_comment_author( $comment->comment_ID ) . '</cite>';

		}

		// Approved comment?
		if ( 0 === (int) $comment->comment_approved ) {
			$comment_text = '<p><em>' . __( 'Comment awaiting moderation', 'commentpress-core' ) . '</em></p>';
		} else {
			$comment_text = get_comment_text( $comment->comment_ID );
		}

		// Default to not on Post.
		$is_on_current_post = '';

		// On current Post?
		if ( is_singular() && is_object( $post ) && (int) $comment->comment_post_ID === (int) $post->ID ) {

			// Access paging globals.
			global $multipage, $page;

			// Is it the same Page, if paged?
			if ( $multipage ) {

				// If it has a Text Signature.
				if ( ! empty( $comment->comment_signature ) ) {

					// Set key.
					$key = '_cp_comment_page';

					// If the custom field already has a value.
					if ( get_comment_meta( $comment->comment_ID, $key, true ) !== '' ) {

						// Get Comment's Page from meta.
						$page_num = get_comment_meta( $comment->comment_ID, $key, true );

						// Is it this one?
						if ( (int) $page_num === (int) $page ) {

							// Is the right Page.
							$is_on_current_post = ' comment_on_post';

						}

					}

				} else {

					// It's always the right Page for Page-level Comments.
					$is_on_current_post = ' comment_on_post';

				}

			} else {

				// Must be the right Page.
				$is_on_current_post = ' comment_on_post';

			}

		}

		// Open li.
		$item_html .= '<li><!-- item li -->' . "\n\n";

		// Show the Comment.
		$item_html .= '
		<div class="comment-wrapper">

			<div class="comment-identifier">
				' . get_avatar( $comment, $size = '32' ) . '
				' . $author . '
				<p class="comment_activity_date">' .
					'<a class="comment_activity_link' . $is_on_current_post . '" href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '">' .
						sprintf(
							/* translators: 1: Comment date, 2: Comment time. */
							__( '%1$s at %2$s', 'commentpress-core' ),
							get_comment_date( '', $comment->comment_ID ),
							commentpress_get_comment_time( $comment->comment_ID )
						) .
					'</a>' .
				'</p>
			</div><!-- /comment-identifier -->

			<div class="comment-content">
				' . apply_filters( 'comment_text', $comment_text ) . '
			</div><!-- /comment-content -->

			<div class="reply">' .
				'<p>' .
					'<a class="comment_activity_link' . $is_on_current_post . '" href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '">' .
						esc_html__( 'See in context', 'commentpress-core' ) .
					'</a>' .
				'</p>' .
			'</div><!-- /reply -->

		</div><!-- /comment-wrapper -->
		' . "\n\n";

		// Close li.
		$item_html .= '</li><!-- /item li -->' . "\n\n";

		// --<
		return $item_html;

	}

endif;



if ( ! function_exists( 'commentpress_comments_by_para_format_pings' ) ) :

	/**
	 * Format the markup for the "pingbacks and trackbacks" section of Comments.
	 *
	 * @since 3.8.10
	 *
	 * @param int $comment_count The number of Comments on the Block.
	 * @return array $return Data array containing the translated strings.
	 */
	function commentpress_comments_by_para_format_pings( $comment_count ) {

		// Init return.
		$return = [];

		// Construct entity text.
		$return['entity_text'] = __( 'pingback or trackback', 'commentpress-core' );

		// Construct permalink text.
		$return['permalink_text'] = __( 'Permalink for pingbacks and trackbacks', 'commentpress-core' );

		// Construct Comment count.
		$return['comment_text'] = sprintf(
			/* translators: %d: Comment count. */
			_n( '<span>%d</span> Pingback or trackback', '<span>%d</span> Pingbacks and trackbacks', $comment_count, 'commentpress-core' ),
			$comment_count // Substitution.
		);

		// Construct heading text.
		$return['heading_text'] = sprintf( '<span>%s</span>', $return['comment_text'] );

		// --<
		return $return;

	}

endif;



if ( ! function_exists( 'commentpress_comments_by_para_format_block' ) ) :

	/**
	 * Format the markup for "Comments by Block" section of Comments.
	 *
	 * @since 3.8.10
	 *
	 * @param int $comment_count The number of Comments on the Block.
	 * @param int $para_num The sequential number of the Block.
	 * @return array $return Data array containing the translated strings.
	 */
	function commentpress_comments_by_para_format_block( $comment_count, $para_num ) {

		// Init return.
		$return = [];

		// Get core plugin reference.
		$core = commentpress_core();

		// Get Block name.
		$block_name = __( 'paragraph', 'commentpress-core' );
		if ( ! empty( $core ) ) {
			$block_name = $core->parser->lexia_get();
		}

		// Construct entity text.
		$return['entity_text'] = sprintf(
			/* translators: 1: Block name, 2: Paragraph number. */
			__( '%1$s %2$s', 'commentpress-core' ),
			$block_name,
			$para_num
		);

		// Construct permalink text.
		$return['permalink_text'] = sprintf(
			/* translators: 1: Block name, 2: Paragraph number. */
			__( 'Permalink for comments on %1$s %2$s', 'commentpress-core' ),
			$block_name,
			$para_num
		);

		// Construct Comment text.
		$return['comment_text'] = sprintf(
			/* translators: %d: Number of comments. */
			_n( '<span class="cp_comment_num">%d</span> <span class="cp_comment_word">Comment</span>', '<span class="cp_comment_num">%d</span> <span class="cp_comment_word">Comments</span>', $comment_count, 'commentpress-core' ),
			$comment_count // Substitution.
		);

		// Construct heading text.
		$return['heading_text'] = sprintf(
			/* translators: 1: Comment label, 2: Block name, 3: Paragraph number. */
			__( '%1$s on <span class="source_block">%2$s %3$s</span>', 'commentpress-core' ),
			$return['comment_text'],
			$block_name,
			$para_num
		);

		// --<
		return $return;

	}

endif;



if ( ! function_exists( 'commentpress_get_comments_by_para' ) ) :

	/**
	 * Get Comments delimited by Paragraph.
	 *
	 * @since 3.0
	 */
	function commentpress_get_comments_by_para() {

		// Declare access to globals.
		global $content_width, $post, $wp_embed;

		/**
		 * Overwrite the content width for the Comments column.
		 *
		 * This is set to an arbitrary width that *sort of* works for the Comments
		 * column for all CommentPress themes. It can be overridden by this filter
		 * if a particular theme or child theme wants to do so. The content width
		 * determines the default width for oEmbedded content.
		 *
		 * @since 3.9
		 *
		 * @param int $content_width A general content width for all themes.
		 */
		$content_width = apply_filters( 'commentpress_comments_content_width', 380 );

		// Allow oEmbed in Comments.
		if ( $wp_embed instanceof WP_Embed ) {
			add_filter( 'comment_text', [ $wp_embed, 'autoembed' ], 1 );
		}

		// Get core plugin reference.
		$core = commentpress_core();
		if ( empty( $core ) ) {
			return;
		}

		/**
		 * Fires before scrollable Comments.
		 *
		 * @since 3.9
		 */
		do_action( 'commentpress_before_scrollable_comments' );

		// Get approved Comments for this Post, sorted Comments by Text Signature.
		$comments_sorted = $core->parser->comments_sorted_get( $post->ID );

		// Key for starting Paragraph Number.
		$key = '_cp_starting_para_number';

		// Default starting Paragraph Number.
		$start_num = 1;

		// Override if the custom field already has a value.
		if ( get_post_meta( $post->ID, $key, true ) !== '' ) {
			$start_num = absint( get_post_meta( $post->ID, $key, true ) );
		}

		// If we have any.
		if ( count( $comments_sorted ) > 0 ) {

			// Allow for BuddyPress registration.
			$registration = false;
			if ( function_exists( 'bp_get_signup_allowed' ) && bp_get_signup_allowed() ) {
				$registration = true;
			}

			// Maybe redirect to BuddyPress sign-up.
			if ( $registration ) {
				$redirect = bp_get_signup_page();
			} else {
				$redirect = wp_login_url( get_permalink() );
			}

			// Init allowed to Comment.
			$login_to_comment = false;

			// If we have to log in to Comment.
			if ( get_option( 'comment_registration' ) && ! is_user_logged_in() ) {
				$login_to_comment = true;
			}

			// Default Comment type to get.
			$comment_type = 'all';

			// The built in walker works just fine since WordPress 3.8.
			$args = [
				'style'    => 'ol',
				'type'     => $comment_type,
				'callback' => 'commentpress_comments',
			];

			// Get singular Post Type label.
			$current_type = get_post_type();
			$post_type    = get_post_type_object( $current_type );

			/**
			 * Assign name of Post Type.
			 *
			 * @since 3.8.10
			 *
			 * @param str $singular_name The singular label for this Post Type.
			 * @param str $current_type The Post Type identifier.
			 */
			$post_type_name = apply_filters( 'commentpress_lexia_post_type_name', $post_type->labels->singular_name, $current_type );

			// Init counter for text_signatures array.
			$sig_counter = 0;

			// Init array for tracking Text Signatures.
			$used_text_sigs = [];

			// Loop through each Paragraph.
			foreach ( $comments_sorted as $text_signature => $comments ) {

				// Count Comments.
				$comment_count = count( $comments );

				// Switch, depending on key.
				switch ( $text_signature ) {

					// Whole Page Comments.
					case 'WHOLE_PAGE_OR_POST_COMMENTS':
						// Clear Text Signature.
						$text_sig = '';

						// Clear the Paragraph Number.
						$para_num = '';

						// Get the markup we need for this.
						$markup = commentpress_comments_by_para_format_whole( $post_type_name, $current_type, $comment_count );

						break;

					// Pingbacks and trackbacks.
					case 'PINGS_AND_TRACKS':
						// Set "unique-enough" Text Signature.
						$text_sig = 'pingbacksandtrackbacks';

						// Clear the Paragraph Number.
						$para_num = '';

						// Get the markup we need for this.
						$markup = commentpress_comments_by_para_format_pings( $comment_count );

						break;

					// Textblock Comments.
					default:
						// Get Text Signature.
						$text_sig = $text_signature;

						// Paragraph Number.
						$para_num = $sig_counter + ( $start_num - 1 );

						// Get the markup we need for this.
						$markup = commentpress_comments_by_para_format_block( $comment_count, $para_num );

				}

				// Init no Comment class.
				$no_comments_class = '';

				// Override if there are no Comments (for print stylesheet to hide them).
				if ( 0 === $comment_count ) {
					$no_comments_class = ' class="no_comments"';
				}

				// Exclude pings if there are none.
				// phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedIf
				if ( 0 === $comment_count && 'PINGS_AND_TRACKS' === $text_signature ) {

					// Skip.

				} else {

					// Show heading.
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo '<h3 id="para_heading-' . esc_attr( $text_sig ) . '"' . $no_comments_class . '>' .
						'<a class="comment_block_permalink" title="' . esc_attr( $markup['permalink_text'] ) . '" href="#para_heading-' . esc_attr( $text_sig ) . '">' .
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							$markup['heading_text'] .
						'</a>' .
					'</h3>' . "\n\n";

					// Override if there are no Comments (for print stylesheet to hide them).
					if ( 0 === $comment_count ) {
						$no_comments_class = ' no_comments';
					}

					// Open Paragraph wrapper.
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo '<div id="para_wrapper-' . esc_attr( $text_sig ) . '" class="paragraph_wrapper' . $no_comments_class . '">' . "\n\n";

					// Have we already used this Text Signature?
					if ( in_array( $text_sig, $used_text_sigs, true ) ) {

						// Show some kind of message.
						// Should not be necessary now that we ensure unique Text Signatures.
						echo '<div class="reply_to_para" id="reply_to_para-' . esc_attr( $para_num ) . '">' . "\n" .
							'<p>' .
								esc_html__( 'It appears that this paragraph is a duplicate of a previous one.', 'commentpress-core' ) .
							'</p>' . "\n" .
						'</div>' . "\n\n";

					} else {

						// If we have Comments.
						if ( count( $comments ) > 0 ) {

							// Open commentlist.
							echo '<ol class="commentlist">' . "\n\n";

							// Use WordPress 2.7+ functionality.
							wp_list_comments( $args, $comments );

							// Close commentlist.
							echo '</ol>' . "\n\n";

						}

						/**
						 * Allow plugins to append to Paragraph-level Comments.
						 *
						 * @since 3.9
						 *
						 * @param str $text_sig The Text Signature of the Paragraph.
						 */
						do_action( 'commentpress_after_paragraph_comments', $text_sig );

						// Add to used array.
						$used_text_sigs[] = $text_sig;

						// Only add comment-on-para link if Comments are open and it's not the pingback section.
						if ( 'open' === $post->comment_status && 'PINGS_AND_TRACKS' !== $text_signature ) {

							// If we have to log in to Comment.
							if ( $login_to_comment ) {

								// The link text depending on whether we've got registration.
								if ( $registration ) {
									$prompt = sprintf(
										/* translators: %s: The name of the entity. */
										__( 'Create an account to leave a comment on %s', 'commentpress-core' ),
										$markup['entity_text']
									);
								} else {
									$prompt = sprintf(
										/* translators: %s: The name of the entity. */
										__( 'Login to leave a comment on %s', 'commentpress-core' ),
										$markup['entity_text']
									);
								}

								/**
								 * Filter the prompt text.
								 *
								 * @since 3.9
								 *
								 * @param str $prompt The link text when login is required.
								 * @param bool $registration True if registration is open, false otherwise.
								 */
								$prompt = apply_filters( 'commentpress_reply_to_prompt_text', $prompt, $registration );

								// Leave Comment link.
								echo '<div class="reply_to_para" id="reply_to_para-' . esc_attr( $para_num ) . '">' . "\n" .
									'<p><a class="reply_to_para" rel="nofollow" href="' . esc_url( $redirect ) . '">' .
										esc_html( $prompt ) .
									'</a></p>' . "\n" .
								'</div>' . "\n\n";

							} else {

								// Construct "onclick" content.
								$onclick = "return addComment.moveFormToPara( '$para_num', '$text_sig', '$post->ID' )";

								/**
								 * Filters the "onclick" attribute.
								 *
								 * @since 3.9
								 *
								 * @param str The default "onclick" attribute.
								 */
								$onclick = apply_filters( 'commentpress_reply_to_para_link_onclick', ' onclick="' . $onclick . '"' );

								// Just show replytopara.
								$query = remove_query_arg( [ 'replytocom' ] );

								// Add param to querystring.
								$query = esc_url(
									add_query_arg(
										[ 'replytopara' => $para_num ],
										$query
									)
								);

								/**
								 * Filters the "href" attribute.
								 *
								 * @since 3.9
								 *
								 * @param str The default "href" attribute.
								 * @param str $text_sig The Text Signature of the Paragraph.
								 */
								$href = apply_filters( 'commentpress_reply_to_para_link_href', $query . '#respond', $text_sig );

								// Construct link content.
								$link_content = sprintf(
										/* translators: %s: The name of the entity. */
									__( 'Leave a comment on %s', 'commentpress-core' ),
									$markup['entity_text']
								);

								/**
								 * Filters the link content.
								 *
								 * @since 3.9
								 *
								 * @param str $link_content The default link content.
								 * @param str The entity text.
								 */
								$link_content = apply_filters( 'commentpress_reply_to_para_link_text', $link_content, $markup['entity_text'] );

								// Leave Comment link.
								echo '<div class="reply_to_para" id="reply_to_para-' . esc_attr( $para_num ) . '">' . "\n" .
									// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									'<p><a class="reply_to_para" href="' . esc_url( $href ) . '"' . $onclick . '>' .
										esc_html( $link_content ) .
									'</a></p>' . "\n" .
								'</div>' . "\n\n";

							}

						}

					}

					/**
					 * Allow plugins to append to Paragraph wrappers.
					 *
					 * @since 3.9
					 *
					 * @param str $text_sig The Text Signature of the Paragraph.
					 */
					do_action( 'commentpress_after_paragraph_wrapper', $text_sig );

					// Close Paragraph wrapper.
					echo '</div>' . "\n\n\n\n";

				}

				// Increment signature array counter.
				$sig_counter++;

			}

		}

		/**
		 * Fires after scrollable Comments.
		 *
		 * @since 3.9
		 */
		do_action( 'commentpress_after_scrollable_comments' );

	}

endif;



if ( ! function_exists( 'commentpress_comment_form_title' ) ) :

	/**
	 * Alternative to the built-in WordPress function.
	 *
	 * @since 3.0
	 *
	 * @param str $no_reply_text The text to show when there are no Comments.
	 * @param str $reply_to_comment_text The text to show when there are Comments.
	 * @param str $reply_to_para_text The text to show on Paragraphs when there are Comments.
	 * @param str $link_to_parent The link to the parent Comment.
	 */
	function commentpress_comment_form_title( $no_reply_text = '', $reply_to_comment_text = '', $reply_to_para_text = '', $link_to_parent = true ) {

		// Sanity checks.
		if ( '' === $no_reply_text ) {
			$no_reply_text = __( 'Leave a reply', 'commentpress-core' );
		}
		if ( '' === $reply_to_comment_text ) {
			/* translators: %s: Comment author. */
			$reply_to_comment_text = __( 'Leave a reply to %s', 'commentpress-core' );
		}
		if ( '' === $reply_to_para_text ) {
			/* translators: %s: The paragraph identifer. */
			$reply_to_para_text = __( 'Leave a comment on %s', 'commentpress-core' );
		}

		// Get Comment ID to reply to from URL query string.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$reply_to_comment_id = isset( $_GET['replytocom'] ) ? (int) sanitize_text_field( wp_unslash( $_GET['replytocom'] ) ) : 0;

		// Get Paragraph Number to reply to from URL query string.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$reply_to_para_id = isset( $_GET['replytopara'] ) ? (int) sanitize_text_field( wp_unslash( $_GET['replytopara'] ) ) : 0;

		// If we have no Comment ID and no Paragraph ID to reply to.
		if ( 0 === $reply_to_comment_id && 0 === $reply_to_para_id ) {

			// Write default title to Page.
			echo esc_html( $no_reply_text );
			return;

		}

		// If we have a Comment ID and NO Paragraph ID to reply to.
		if ( 0 !== $reply_to_comment_id && 0 === $reply_to_para_id ) {

			// Get Comment.
			$comment = get_comment( $reply_to_comment_id );

			// Get link to Comment.
			$author = ( $link_to_parent ) ?
				'<a href="#comment-' . esc_attr( $comment->comment_ID ) . '">' . esc_html( get_comment_author( $comment->comment_ID ) ) . '</a>' :
				esc_html( get_comment_author( $comment->comment_ID ) );

			// Write to Page.
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			printf( $reply_to_comment_text, $author );
			return;

		}

		// Get core plugin reference.
		$core = commentpress_core();
		if ( empty( $core ) ) {
			return;
		}

		// Get Paragraph Text Signature.
		$text_sig = $core->parser->text_signature_get( $reply_to_para_id );

		// Get link to Paragraph.
		if ( $link_to_parent ) {

			// Construct link text.
			$para_text = sprintf(
				/* translators: 1: The block name, 2: Comment count. */
				_x( '%1$s %2$s', 'The first substitution is the block name e.g. "paragraph". The second is the numeric comment count.', 'commentpress-core' ),
				ucfirst( $core->parser->lexia_get() ),
				$reply_to_para_id
			);

			// Construct Paragraph.
			$paragraph = '<a href="#para_heading-' . esc_attr( $text_sig ) . '">' . $para_text . '</a>';

		} else {

			// Construct Paragraph without link.
			$paragraph = sprintf(
				/* translators: 1: The block name, 2: Comment count. */
				_x( '%1$s %2$s', 'The first substitution is the block name e.g. "paragraph". The second is the numeric comment count.', 'commentpress-core' ),
				ucfirst( $core->parser->lexia_get() ),
				$para_num
			);

		}

		// Write to Page.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		printf( $reply_to_para_text, $paragraph );

	}

endif;



if ( ! function_exists( 'commentpress_comment_reply_link' ) ) :

	/**
	 * Alternative to the built-in WordPress function.
	 *
	 * @since 3.0
	 *
	 * @param array      $args The reply links arguments.
	 * @param WP_Comment $comment The WordPress Comment object.
	 * @param WP_Post    $post The WordPress Post object.
	 */
	function commentpress_comment_reply_link( $args = [], $comment = null, $post = null ) {

		// Set some defaults.
		$defaults = [
			'add_below'  => 'comment',
			'respond_id' => 'respond',
			'reply_text' => __( 'Reply', 'commentpress-core' ),
			'login_text' => __( 'Log in to Reply', 'commentpress-core' ),
			'depth'      => 0,
			'before'     => '',
			'after'      => '',
		];

		// Parse them.
		$args = wp_parse_args( $args, $defaults );

		if ( 0 === $args['depth'] || $args['max_depth'] <= $args['depth'] ) {
			return;
		}

		// Get the obvious.
		$comment = get_comment( $comment );
		$post    = get_post( $post );

		// Kick out if Comments closed.
		if ( 'open' !== $post->comment_status ) {
			return false;
		}

		// Init link.
		$link = '';

		// If we have to log in to Comment.
		if ( get_option( 'comment_registration' ) && ! is_user_logged_in() ) {

			// Construct link.
			$link = '<a rel="nofollow" href="' . site_url( 'wp-login.php?redirect_to=' . get_permalink() ) . '">' .
				esc_html( $args['login_text'] ) .
			'</a>';

		} else {

			// Just show replytocom.
			$query = remove_query_arg( [ 'replytopara' ], get_permalink( $post->ID ) );

			// Define query string.
			$addquery = esc_url(
				add_query_arg(
					[ 'replytocom' => $comment->comment_ID ],
					$query
				)
			);

			// Build attributes.
			$href    = $addquery . '#' . $args['respond_id'];
			$onclick = 'return addComment.moveForm(' .
				"'" . $args['add_below'] . '-' . $comment->comment_ID . "', " .
				"'" . $comment->comment_ID . "', " .
				"'" . $args['respond_id'] . "', " .
				"'" . $post->ID . "', " .
				"'" . $comment->comment_signature . "'" .
			');';

			// Define link.
			$link = '<a rel="nofollow" class="comment-reply-link" href="' . esc_url( $href ) . '" onclick="' . $onclick . '">' .
				esc_html( $args['reply_text'] ) .
			'</a>';

		}

		// Wrap link in "before" and "after".
		$link = $args['before'] . $link . $args['after'];

		/**
		 * Filters the Comment Reply link.
		 *
		 * @since 3.4
		 *
		 * @param string $link The default the Comment Reply link.
		 * @param array $args The reply links arguments.
		 * @param WP_Comment $comment The WordPress Comment object.
		 * @param WP_Post $post The WordPress Post object.
		 */
		return apply_filters( 'comment_reply_link', $link, $args, $comment, $post );

	}

endif;



if ( ! function_exists( 'commentpress_comments' ) ) :

	/**
	 * Custom Comments display function.
	 *
	 * @since 3.0
	 *
	 * @param WP_Comment $comment The Comment object.
	 * @param array      $args The Comment arguments.
	 * @param int        $depth The Comment depth.
	 */
	function commentpress_comments( $comment, $args, $depth ) {

		// Build Comment as html.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo commentpress_get_comment_markup( $comment, $args, $depth );

	}

endif;



if ( ! function_exists( 'commentpress_get_comment_markup' ) ) :

	/**
	 * Wrap Comment in its markup.
	 *
	 * @since 3.0
	 *
	 * @param WP_Comment $comment The WordPress Comment object.
	 * @param array      $args The Comment arguments.
	 * @param int        $depth The Comment depth.
	 * @return string $html The Comment markup.
	 */
	function commentpress_get_comment_markup( $comment, $args, $depth ) {

		// Was it a registered User?
		if ( ! empty( $comment->user_id ) ) {

			// Get User details.
			$user = get_userdata( $comment->user_id );

			// Get User link.
			$user_link = commentpress_get_user_link( $user, $comment );

			// Construct author citation.
			$author = ( ! empty( $user_link ) && 'http://' !== $user_link ) ?
				'<cite class="fn"><a href="' . esc_url( $user_link ) . '">' . get_comment_author( $comment->comment_ID ) . '</a></cite>' :
				'<cite class="fn">' . get_comment_author( $comment->comment_ID ) . '</cite>';

		} else {

			// Construct link to commenter url for unregistered Users.
			if (
				! empty( $comment->comment_author_url ) &&
				'http://' !== $comment->comment_author_url &&
				0 !== (int) $comment->comment_approved
			) {
				$author = '<cite class="fn"><a href="' . $comment->comment_author_url . '">' . get_comment_author( $comment->comment_ID ) . '</a></cite>';
			} else {
				$author = '<cite class="fn">' . get_comment_author( $comment->comment_ID ) . '</cite>';
			}

		}

		// Check moderation status.
		if ( 0 === (int) $comment->comment_approved ) {
			$comment_text = '<p><em>' . __( 'Comment awaiting moderation', 'commentpress-core' ) . '</em></p>';
		} else {
			$comment_text = get_comment_text( $comment->comment_ID );
		}

		// Empty reply div by default.
		$comment_reply = '';

		// Enable access to Post.
		global $post;

		// Can we reply?
		if (

			// Not if Comments are closed.
			'open' === $post->comment_status &&

			// We don't want reply to on pingbacks.
			'pingback' !== $comment->comment_type &&

			// We don't want reply to on trackbacks.
			'trackback' !== $comment->comment_type &&

			// Nor on unapproved Comments.
			1 === (int) $comment->comment_approved

		) {

			// Are we threading Comments?
			if ( get_option( 'thread_comments', false ) ) {

				// Build custom Comment reply link.
				$comment_reply_link = array_merge(
					$args,
					[
						/* translators: %s: Comment author. */
						'reply_text' => sprintf( __( 'Reply to %s', 'commentpress-core' ), get_comment_author( $comment->comment_ID ) ),
						'depth'      => $depth,
						'max_depth'  => $args['max_depth'],
					]
				);

				// Get custom Comment reply link.
				$comment_reply = commentpress_comment_reply_link( $comment_reply_link );

				// Wrap in div.
				$comment_reply = '<div class="reply">' . $comment_reply . '</div><!-- /reply -->';

			}

		}

		// Init edit link.
		$editlink = '';

		// If logged in and has capability.
		if ( is_user_logged_in() && current_user_can( 'edit_comment', $comment->comment_ID ) ) {

			/**
			 * Filters default edit link title text.
			 *
			 * @since 3.4
			 *
			 * @param str The default edit link title text.
			 */
			$edit_title_text = apply_filters( 'cp_comment_edit_link_title_text', __( 'Edit this comment', 'commentpress-core' ) );

			/**
			 * Filters default edit link text.
			 *
			 * @since 3.4
			 *
			 * @param str The default edit link text.
			 */
			$edit_text = apply_filters( 'cp_comment_edit_link_text', __( 'Edit', 'commentpress-core' ) );

			// Get link.
			$edit_comment_link = get_edit_comment_link( $comment->comment_ID );

			// Get "Edit Comment" link.
			$editlink = '<span class="alignright comment-edit">' .
				'<a class="comment-edit-link" href="' . esc_url( $edit_comment_link ) . '" title="' . esc_attr( $edit_title_text ) . '">' .
					esc_html( $edit_text ) .
				'</a>' .
			'</span>';

			/**
			 * Filters default edit link.
			 *
			 * @since 3.4
			 *
			 * @param string The default edit link.
			 * @param WP_Comment $comment The WordPress Comment object.
			 */
			$editlink = apply_filters( 'cp_comment_edit_link', $editlink, $comment );

		}

		/**
		 * Filters default action links.
		 *
		 * @since 3.4
		 *
		 * @param string The default edit link.
		 * @param WP_Comment $comment The WordPress Comment object.
		 */
		$editlink = apply_filters( 'cp_comment_action_links', $editlink, $comment );

		// Get Comment class(es).
		$comment_class = comment_class( null, $comment->comment_ID, $post->ID, false );

		// If orphaned, add class to identify as such.
		$comment_orphan = isset( $comment->orphan ) ? ' comment-orphan' : '';

		// Construct permalink.
		$comment_permalink = sprintf(
			/* translators: 1: The Comment date, 2: The Comment time. */
			__( '%1$s at %2$s', 'commentpress-core' ),
			get_comment_date( '', $comment->comment_ID ),
			commentpress_get_comment_time( $comment->comment_ID )
		);

		// Rebuild Comment markup. The <li> element is closed by the Walker.
		$html = '<li id="li-comment-' . $comment->comment_ID . '" ' . $comment_class . '>
			<div class="comment-wrapper">
				<div id="comment-' . $comment->comment_ID . '">

					<div class="comment-identifier' . $comment_orphan . '">
						' . apply_filters( 'commentpress_comment_identifier_prepend', '', $comment ) . '
						' . get_avatar( $comment, $size = '32' ) . '
						' . $editlink . '
						' . $author . '
						<a class="comment_permalink" href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '" title="' . esc_attr__( 'Permalink for this comment', 'commentpress-core' ) . '"><span class="comment_permalink_copy"></span>' . $comment_permalink . '</a>
						' . apply_filters( 'commentpress_comment_identifier_append', '', $comment ) . '
					</div><!-- /comment-identifier -->

					<div class="comment-content' . $comment_orphan . '">
						' . apply_filters( 'comment_text', $comment_text ) . '
					</div><!-- /comment-content -->

					' . $comment_reply . '

				</div><!-- /comment-' . $comment->comment_ID . ' -->
			</div><!-- /comment-wrapper -->
			';

		// --<
		return $html;

	}

endif;



if ( ! function_exists( 'commentpress_comments_by_para_format_whole' ) ) :

	/**
	 * Format the markup for the "Whole Page" section of Comments.
	 *
	 * @since 3.8.10
	 *
	 * @param str $post_type_name The singular name of the Post Type.
	 * @param str $post_type The Post Type identifier.
	 * @param int $comment_count The number of Comments on the Block.
	 * @return array $return Data array containing the translated strings.
	 */
	function commentpress_comments_by_para_format_whole( $post_type_name, $post_type, $comment_count ) {

		// Init return.
		$return = [];

		// Construct entity text.
		$return['entity_text'] = sprintf(
			/* translators: %s: Name of the Post Type. */
			__( 'the whole %s', 'commentpress-core' ),
			$post_type_name
		);

		/**
		 * Allow "the whole entity" text to be filtered.
		 *
		 * This is primarily for "media", where it makes little sense to comment
		 * on "the whole image", for example.
		 *
		 * @since 3.9
		 *
		 * @param str $entity_text The current entity text.
		 * @param str $post_type_name The singular name of the Post Type.
		 * @return str $entity_text The modified entity text.
		 */
		$return['entity_text'] = apply_filters( 'commentpress_lexia_whole_entity_text', $return['entity_text'], $post_type_name, $post_type );

		// Construct permalink text.
		$return['permalink_text'] = sprintf(
			/* translators: %s: Name of the entity. */
			__( 'Permalink for comments on %s', 'commentpress-core' ),
			$return['entity_text']
		);

		// Construct Comment count.
		$return['comment_text'] = sprintf(
			/* translators: %d: The number of comments. */
			_n( '<span class="cp_comment_num">%d</span> <span class="cp_comment_word">Comment</span>', '<span class="cp_comment_num">%d</span> <span class="cp_comment_word">Comments</span>', $comment_count, 'commentpress-core' ),
			$comment_count // Substitution.
		);

		// Construct heading text.
		$return['heading_text'] = sprintf(
			/* translators: 1: Comment identifier, 2: Entity identifier. */
			__( '%1$s on <span class="source_block">%2$s</span>', 'commentpress-core' ),
			$return['comment_text'],
			$return['entity_text']
		);

		// --<
		return $return;

	}

endif;



if ( ! function_exists( 'commentpress_add_selection_classes' ) ) :

	/**
	 * Filter the Comment class to add selection data.
	 *
	 * @since 3.8
	 *
	 * @param array       $classes An array of Comment classes.
	 * @param string      $class A comma-separated list of additional classes added to the list.
	 * @param int         $comment_id The Comment ID.
	 * @param WP_Comment  $comment The WordPress Comment object.
	 * @param int|WP_Post $post_id The Post ID or WP_Post object.
	 */
	function commentpress_add_selection_classes( $classes, $class, $comment_id, $comment, $post_id = 0 ) {

		// Define key.
		$key = '_cp_comment_selection';

		// Get current.
		$data = get_comment_meta( $comment_id, $key, true );

		// If the Comment meta already has a value.
		if ( ! empty( $data ) ) {

			// Make into an array.
			$selection = explode( ',', $data );

			// Add to classes.
			$classes[] = 'selection-exists';
			$classes[] = 'sel_start-' . $selection[0];
			$classes[] = 'sel_end-' . $selection[1];

		}

		// --<
		return $classes;

	}

endif;

// Add callback for the above.
add_filter( 'comment_class', 'commentpress_add_selection_classes', 100, 4 );



if ( ! function_exists( 'commentpress_comment_post_redirect' ) ) :

	/**
	 * Filter Comment Post redirects for multipage Posts.
	 *
	 * @since 3.5
	 *
	 * @param str        $link The link to the Comment.
	 * @param WP_Comment $comment The WordPress Comment object.
	 */
	function commentpress_comment_post_redirect( $link, $comment ) {

		// Get URL of the Page that submitted the Comment.
		$page_url = isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';

		// Get anchor position.
		$hash = strpos( $page_url, '#' );

		// Well, do we have an anchor?
		if ( false !== $hash ) {

			// Yup, so strip it.
			$page_url = substr( $page_url, 0, $hash );

		}

		// Assume not AJAX.
		$ajax_token = '';

		// Is this an AJAX Comment form submission?
		if ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) {
			if ( 'XMLHttpRequest' === $_SERVER['HTTP_X_REQUESTED_WITH'] ) {

				// Yes, it's AJAX - some browsers cache POST, so invalidate.
				$ajax_token = '?cachebuster=' . time();

				// But, test for pretty permalinks.
				if ( false !== strpos( $page_url, '?' ) ) {

					// Pretty permalinks are off.
					$ajax_token = '&cachebuster=' . time();

				}

			}

		}

		// Construct cachebusting Comment redirect.
		$link = $page_url . $ajax_token . '#comment-' . $comment->comment_ID;

		// --<
		return $link;

	}

endif;

// Add callback for the above, making it run early so it can be overridden by AJAX commenting.
add_filter( 'comment_post_redirect', 'commentpress_comment_post_redirect', 4, 2 );



if ( ! function_exists( 'commentpress_image_caption_shortcode' ) ) :

	/**
	 * Rebuilds the "Caption" shortcode output.
	 *
	 * @since 3.5
	 *
	 * @param array $empty WordPress passes '' as the first param.
	 * @param array $attr Attributes attributed to the shortcode.
	 * @param str   $content Optional. Shortcode content.
	 * @return str $caption The modified caption.
	 */
	function commentpress_image_caption_shortcode( $empty, $attr, $content ) {

		// Set the shortcode defaults.
		$defaults = [
			'id'      => '',
			'align'   => 'alignnone',
			'width'   => '',
			'caption' => '',
		];

		// Get our shortcode vars.
		$atts = shortcode_atts( $defaults, $attr );

		if ( 1 > (int) $atts['width'] || empty( $atts['caption'] ) ) {
			return $content;
		}

		// Sanitise ID.
		$id = '';
		if ( $atts['id'] ) {
			$id = ' id="' . esc_attr( $atts['id'] ) . '" ';
		}

		// Add space prior to alignment.
		$alignment = ' ' . esc_attr( $atts['align'] );

		// Get width.
		$width = ( 0 + (int) $atts['width'] );

		// Allow a few tags.
		$tags_to_allow = [
			'em'     => [],
			'strong' => [],
			'a'      => [ 'href' ],
		];

		// Sanitise caption.
		$caption = wp_kses( $atts['caption'], $tags_to_allow );

		// Force balance those tags.
		$caption = force_balance_tags( $caption, true );

		// Construct.
		$caption = '<!-- cp_caption_start -->' .
			'<span class="captioned_image' . $alignment . '" style="width: ' . $width . 'px">' .
				'<span' . $id . ' class="wp-caption">' . do_shortcode( $content ) . '</span>' .
				'<small class="wp-caption-text">' . $caption . '</small>' .
			'</span>' .
		'<!-- cp_caption_end -->';

		// --<
		return $caption;

	}

endif;

// Add callback for the above.
add_filter( 'img_caption_shortcode', 'commentpress_image_caption_shortcode', 10, 3 );



if ( ! function_exists( 'commentpress_multipage_comment_link' ) ) :

	/**
	 * Filter Comment Permalinks for multipage Posts.
	 *
	 * @since 3.5
	 *
	 * @param string     $link The existing Comment link.
	 * @param WP_Comment $comment The WordPress Comment object.
	 * @param array      $args An array of extra arguments.
	 * @return string $link The modified Comment link.
	 */
	function commentpress_multipage_comment_link( $link, $comment, $args ) {

		// Get multipage and Post.
		global $multipage, $post;

		/*
		// Are there multiple (sub)pages?
		if ( is_object( $post ) && $multipage ) {
		*/

		// Exclude Page-level Comments.
		if ( '' !== $comment->comment_signature ) {

			// Init Page num.
			$page_num = 1;

			// Set key.
			$key = '_cp_comment_page';

			// Get the Page number if the custom field already has a value.
			if ( get_comment_meta( $comment->comment_ID, $key, true ) !== '' ) {
				$page_num = get_comment_meta( $comment->comment_ID, $key, true );
			}

			// Get current Comment info.
			$comment_path_info = wp_parse_url( $link );

			// Set Comment path.
			$link = commentpress_get_post_multipage_url( $page_num, get_post( $comment->comment_post_ID ) ) . '#' . $comment_path_info['fragment'];

		}

		/*
		// Close multiple (sub)pages test.
		}
		*/

		// --<
		return $link;

	}

endif;

// Add callback for the above.
add_filter( 'get_comment_link', 'commentpress_multipage_comment_link', 10, 3 );



if ( ! function_exists( 'commentpress_get_post_multipage_url' ) ) :

	/**
	 * Get the URL fo a Page in a multipage context.
	 *
	 * Copied from wp-includes/post-template.php _wp_link_page()
	 *
	 * @since 3.5
	 *
	 * @param int     $i The Page number.
	 * @param WP_Post $post The WordPress Post object.
	 * @return str $url The URL to the Sub-page.
	 */
	function commentpress_get_post_multipage_url( $i, $post = '' ) {

		// If we have no passed value.
		if ( '' === $post ) {

			// We assume we're in the loop.
			global $post, $wp_rewrite;

			if ( 1 === (int) $i ) {
				$url = get_permalink();
			} else {
				if ( ! get_option( 'permalink_structure' ) || in_array( $post->post_status, [ 'draft', 'pending' ], true ) ) {
					$url = add_query_arg( 'page', $i, get_permalink() );
				} elseif ( 'page' === get_option( 'show_on_front' ) && (int) get_option( 'page_on_front' ) === (int) $post->ID ) {
					$url = trailingslashit( get_permalink() ) . user_trailingslashit( "$wp_rewrite->pagination_base/" . $i, 'single_paged' );
				} else {
					$url = trailingslashit( get_permalink() ) . user_trailingslashit( $i, 'single_paged' );
				}
			}

		} else {

			// Use passed Post object.
			if ( 1 === (int) $i ) {
				$url = get_permalink( $post->ID );
			} else {
				if ( ! get_option( 'permalink_structure' ) || in_array( $post->post_status, [ 'draft', 'pending' ], true ) ) {
					$url = add_query_arg( 'page', $i, get_permalink( $post->ID ) );
				} elseif ( 'page' === get_option( 'show_on_front' ) && (int) get_option( 'page_on_front' ) === (int) $post->ID ) {
					$url = trailingslashit( get_permalink( $post->ID ) ) . user_trailingslashit( "$wp_rewrite->pagination_base/" . $i, 'single_paged' );
				} else {
					$url = trailingslashit( get_permalink( $post->ID ) ) . user_trailingslashit( $i, 'single_paged' );
				}
			}

		}

		return esc_url( $url );

	}

endif;



if ( ! function_exists( 'commentpress_add_wp_editor' ) ) :

	/**
	 * Adds the TinyMCE editor.
	 *
	 * @since 3.5
	 *
	 * @return bool True if the TinyMCE editor has been added, false otherwise.
	 */
	function commentpress_add_wp_editor() {

		// Get core plugin reference.
		$core = commentpress_core();
		if ( empty( $core ) ) {
			return false;
		}

		// Render the TinyMCE editor.
		return $core->editor->comments->editor_render();

	}

endif;



if ( ! function_exists( 'commentpress_assign_default_editor' ) ) :

	/**
	 * Makes TinyMCE the default editor on the front end.
	 *
	 * Callback is located here because it's only relevant in CommentPress themes.
	 *
	 * @since 3.0
	 *
	 * @param str $default_editor The default editor as set by WordPress.
	 * @return str 'tinymce' our overridden default editor.
	 */
	function commentpress_assign_default_editor( $default_editor ) {

		// Only on front-end.
		if ( is_admin() ) {
			return $default_editor;
		}

		// Always return 'tinymce' as the default editor, or else the Comment form will not show up.
		return 'tinymce';

	}

endif;

// Add callback for the above.
add_filter( 'wp_default_editor', 'commentpress_assign_default_editor', 10, 1 );



if ( ! function_exists( 'commentpress_add_tinymce_styles' ) ) :

	/**
	 * Adds our styles to the TinyMCE editor.
	 *
	 * @since 3.0
	 *
	 * @param str $mce_css The default TinyMCE stylesheets as set by WordPress.
	 * @return str $mce_css The list of stylesheets with ours added.
	 */
	function commentpress_add_tinymce_styles( $mce_css ) {

		// Only on front-end.
		if ( is_admin() ) {
			return $mce_css;
		}

		// Add comma if not empty.
		if ( ! empty( $mce_css ) ) {
			$mce_css .= ',';
		}

		// Add our editor styles.
		$mce_css .= get_template_directory_uri() . '/assets/css/comment-form.css';

		// --<
		return $mce_css;

	}

endif;

// Add callback for the above.
add_filter( 'mce_css', 'commentpress_add_tinymce_styles' );



if ( ! function_exists( 'commentpress_assign_editor_buttons' ) ) :

	/**
	 * Assign our buttons to TinyMCE in teeny mode.
	 *
	 * @since 3.5
	 *
	 * @param array $buttons The default editor buttons as set by WordPress.
	 * @return array The overridden editor buttons.
	 */
	function commentpress_assign_editor_buttons( $buttons ) {

		// Basic buttons.
		return [
			'bold',
			'italic',
			'underline',
			'|',
			'bullist',
			'numlist',
			'|',
			'link',
			'unlink',
			'|',
			'removeformat',
			'fullscreen',
		];

	}

endif;

// Add callback for the above.
add_filter( 'teeny_mce_buttons', 'commentpress_assign_editor_buttons' );



if ( ! function_exists( 'commentpress_get_comment_time' ) ) :

	/**
	 * Retrieves the comment time for a given Comment.
	 *
	 * Replacement for the WordPress "get_comment_time" function that accepts a
	 * Comment object or Comment ID.
	 *
	 * @since 4.0
	 *
	 * @param int|WP_Comment $comment_id Optional. WP_Comment or ID of the comment for which to print the text.
	 *                                   Default current comment.
	 * @param string         $format     Optional. PHP time format. Defaults to the 'time_format' option.
	 * @param bool           $gmt        Optional. Whether to use the GMT date. Default false.
	 * @param bool           $translate  Optional. Whether to translate the time (for use in feeds).
	 *                                   Default true.
	 * @return string The formatted time.
	 */
	function commentpress_get_comment_time( $comment_id = null, $format = '', $gmt = false, $translate = true ) {

		$comment = get_comment( $comment_id );

		$comment_date = $gmt ? $comment->comment_date_gmt : $comment->comment_date;

		$_format = ! empty( $format ) ? $format : get_option( 'time_format' );

		$date = mysql2date( $_format, $comment_date, $translate );

		/**
		 * Filters the returned comment time.
		 *
		 * @since 1.5.0
		 *
		 * @param string|int $date      The Comment time, formatted as a date string or Unix timestamp.
		 * @param string     $format    PHP date format.
		 * @param bool       $gmt       Whether the GMT date is in use.
		 * @param bool       $translate Whether the time is translated.
		 * @param WP_Comment $comment   The WordPress Comment object.
		 */
		return apply_filters( 'get_comment_time', $date, $format, $gmt, $translate, $comment );
	}

endif;
