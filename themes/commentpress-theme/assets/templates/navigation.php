<?php
/**
 * Navigation Template.
 *
 * @package CommentPress_Core
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Get core plugin reference.
$core = commentpress_core();

// Get the ID and URL for the "Welcome Page".
if ( ! empty( $core ) ) {
	$title_id = $core->db->option_get( 'cp_welcome_page' );
	$title_url = $core->get_page_url( 'cp_welcome_page' );
}

?>
<!-- navigation.php -->
<div id="book_nav">
	<div id="book_nav_wrapper">

	<div id="cp_book_nav">

		<?php

		/**
		 * Filters the Older Entries title.
		 *
		 * @since 3.4
		 *
		 * @param str The default Older Entries title.
		 */
		$previous_title = apply_filters( 'cp_nav_previous_link_title', __( 'Older Entries', 'commentpress-core' ) );

		/**
		 * Filters the Newer Entries title.
		 *
		 * @since 3.4
		 *
		 * @param str The default Newer Entries title.
		 */
		$next_title = apply_filters( 'cp_nav_next_link_title', __( 'Newer Entries', 'commentpress-core' ) );

		?>

		<?php if ( is_page() ) : ?>

			<?php

			/**
			 * Filters the custom Page Navigation markup.
			 *
			 * @since 3.4
			 *
			 * @param str The default custom Page Navigation markup.
			 */
			$cp_page_nav = apply_filters( 'cp_template_page_navigation', commentpress_page_navigation() );

			?>

			<?php if ( ! empty( $cp_page_nav ) ) : ?>
				<ul>
					<?php echo $cp_page_nav; ?>
				</ul>
			<?php endif; ?>

			<div id="cp_book_info">
				<p><?php echo commentpress_page_title(); ?></p>
			</div>

		<?php elseif ( is_single() ) : ?>

			<ul id="blog_navigation">
				<?php next_post_link( '<li class="alignright">%link</li>' ); ?>
				<?php previous_post_link( '<li class="alignleft">%link</li>' ); ?>
			</ul>

			<div id="cp_book_info">
				<p><?php echo commentpress_page_title(); ?></p>
			</div>

		<?php elseif ( is_home() || is_post_type_archive() ) : ?>

			<?php

			$nl = get_next_posts_link( '&laquo; ' . $previous_title );
			$pl = get_previous_posts_link( $next_title . ' &raquo;' );

			?>

			<?php if ( ! empty( $nl ) || ! empty( $pl ) ) : ?>
				<ul id="blog_navigation">
					<?php if ( ! empty( $nl ) ) : ?>
						<li class="alignright"><?php echo $nl; ?></li>
					<?php endif; ?>
					<?php if ( ! empty( $pl ) ) : ?>
						<li class="alignleft"><?php echo $pl; ?></li>
					<?php endif; ?>
				</ul>
			<?php endif; ?>

			<div id="cp_book_info">
				<p><?php echo __( 'Blog', 'commentpress-core' ); ?></p>
			</div>

		<?php elseif ( is_day() || is_month() || is_year() ) : ?>

			<?php

			$nl = get_next_posts_link( '&laquo; ' . $previous_title );
			$pl = get_previous_posts_link( $next_title . ' &raquo;' );

			?>

			<?php if ( ! empty( $nl ) || ! empty( $pl ) ) : ?>
				<ul id="blog_navigation">
					<?php if ( ! empty( $nl ) ) : ?>
						<li class="alignright"><?php echo $nl; ?></li>
					<?php endif; ?>
					<?php if ( ! empty( $pl ) ) : ?>
						<li class="alignleft"><?php echo $pl; ?></li>
					<?php endif; ?>
				</ul>
			<?php endif; ?>

			<div id="cp_book_info">
				<p><?php echo __( 'Blog Archives:', 'commentpress-core' ); ?> <?php wp_title( '' ); ?></p>
			</div>

		<?php elseif ( is_search() ) : ?>

			<?php

			$nl = get_next_posts_link( '&laquo; ' . __( 'More Results', 'commentpress-core' ) );
			$pl = get_previous_posts_link( __( 'Previous Results', 'commentpress-core' ) . ' &raquo;' );

			?>

			<?php if ( ! empty( $nl ) || ! empty( $pl ) ) : ?>
				<ul id="blog_navigation">
					<?php if ( ! empty( $nl ) ) : ?>
						<li class="alignright"><?php echo $nl; ?></li>
					<?php endif; ?>
					<?php if ( ! empty( $pl ) ) : ?>
						<li class="alignleft"><?php echo $pl; ?></li>
					<?php endif; ?>
				</ul>
			<?php endif; ?>

			<div id="cp_book_info">
				<p><?php wp_title( '' ); ?></p>
			</div>

		<?php elseif ( is_category() || is_tag() || is_tax() ) : ?>

			<?php

			$nl = get_next_posts_link( '&laquo; ' . __( 'More Results', 'commentpress-core' ) );
			$pl = get_previous_posts_link( __( 'Previous Results', 'commentpress-core' ) . ' &raquo;' );

			?>

			<?php if ( ! empty( $nl ) || ! empty( $pl ) ) : ?>
				<ul id="blog_navigation">
					<?php if ( ! empty( $nl ) ) : ?>
						<li class="alignright"><?php echo $nl; ?></li>
					<?php endif; ?>
					<?php if ( ! empty( $pl ) ) : ?>
						<li class="alignleft"><?php echo $pl; ?></li>
					<?php endif; ?>
				</ul>
			<?php endif; ?>

			<div id="cp_book_info">
				<p><?php wp_title( '' ); ?></p>
			</div>

		<?php else : /* Catch-all for other Page Types. */ ?>

			<div id="cp_book_info">
				<p><?php wp_title( '' ); ?></p>
			</div>

		<?php endif; ?>

	</div><!-- /cp_book_nav -->

	<ul id="nav">

		<?php if ( ! empty( $core ) ) : ?>

			<?php if ( is_multisite() ) : ?>

				<?php

				// TODO: We need to account for situations where no CommentPress Core Special Pages exist.

				/**
				 * Filters the Network Home title.
				 *
				 * @since 3.4
				 *
				 * @param str The default Network Home title.
				 */
				$site_title = apply_filters( 'cp_nav_network_home_title', __( 'Site Home Page', 'commentpress-core' ) );

				?>

				<li>
					<a href="<?php echo network_home_url(); ?>" id="btn_home" class="css_btn" title="<?php echo esc_attr( $site_title ); ?>"><?php echo esc_html( $site_title ); ?></a>
				</li>

				<?php if ( $core->bp->is_groupblog() ) : ?>

					<?php

					// Link to Group in multisite Group Blog.

					// Check if this Blog is a Group Blog.
					$group_id = get_groupblog_group_id( get_current_blog_id() );

					?>

					<?php if ( ! empty( $group_id ) && is_numeric( $group_id ) ) : ?>

						<?php

						// When this Blog is a Group Blog.
						$group = groups_get_group( [ 'group_id' => $group_id ] );
						$group_url = bp_get_group_permalink( $group );

						/**
						 * Filters the Group Home Page title.
						 *
						 * @since 3.4
						 *
						 * @param str The default Group Home Page title.
						 */
						$group_title = apply_filters( 'cp_nav_group_home_title', __( 'Group Home Page', 'commentpress-core' ) );

						?>

						<li>
							<a href="<?php echo $group_url; ?>" id="btn_grouphome" class="css_btn" title="<?php echo $group_title; ?>"><?php echo $group_title; ?></a>
						</li>

					<?php endif; ?>

				<?php endif; ?>

			<?php else : ?>

				<?php if ( (int) $title_id !== (int) get_option( 'page_on_front' ) ) : ?>

					<?php

					/**
					 * Filters the Home Page title.
					 *
					 * Used if Blog home is not CommentPress Core Welcome Page.
					 *
					 * @since 3.4
					 *
					 * @param str The default Home Page title.
					 */
					$home_title = apply_filters( 'cp_nav_blog_home_title', __( 'Home Page', 'commentpress-core' ) );

					?>

					<li>
						<a href="<?php echo home_url(); ?>" id="btn_home" class="css_btn" title="<?php echo $home_title; ?>"><?php echo $home_title; ?></a>
					</li>

				<?php endif; ?>

			<?php endif; ?>

			<?php if ( ! empty( $title_url ) ) : ?>

				<?php

				/**
				 * Filters the Title Page title.
				 *
				 * @since 3.4
				 *
				 * @param str The default Title Page title.
				 */
				$title_title = apply_filters( 'cp_nav_title_page_title', __( 'Title Page', 'commentpress-core' ) );

				?>

				<li>
					<a href="<?php echo $title_url; ?>" id="btn_cover" class="css_btn" title="<?php echo $title_title; ?>"><?php echo $title_title; ?></a>
				</li>

			<?php endif; ?>

			<?php

			// Show link to General Comments Page if we have one.
			echo $core->pages_legacy->get_page_link( 'cp_general_comments_page' );

			// Show link to All Comments Page if we have one.
			echo $core->pages_legacy->get_page_link( 'cp_all_comments_page' );

			// Show link to Comments-by-User Page if we have one.
			echo $core->pages_legacy->get_page_link( 'cp_comments_by_page' );

			// Show link to book Blog Page if we have one.
			echo $core->pages_legacy->get_page_link( 'cp_blog_page' );

			// Show link to book Blog Archive Page if we have one.
			echo $core->pages_legacy->get_page_link( 'cp_blog_archive_page' );

			?>

		<?php endif; ?>

	</ul>

	<ul id="minimiser_trigger">
		<?php if ( ! empty( $core ) ) : ?>
			<?php echo $core->display->get_header_min_link(); /* Show minimise header button. */ ?>
		<?php endif; ?>
	</ul>

	</div><!-- /book_nav_wrapper -->
</div><!-- /book_nav -->
