<?php
/*
Template Name: Group
*/

// The get_users_of_blog() function is deprecated in WordPress 3.1+.
if ( version_compare( $wp_version, '3.1', '>=' ) ) {

	// Set args.
	$args = [
		'orderby' => 'nicename',
	];

	// Get users of this blog (blog_id is provided by default).
	$_users = get_users( $args );

} else {

	// Get the users of this blog.
	$_users = get_users_of_blog();

}



get_header(); ?>



<!-- group.php -->

<div id="wrapper">



<div id="main_wrapper" class="clearfix">



<div id="page_wrapper">



<div id="content">



<div class="post">



<h2 class="post_title"><?php _e( 'Group Members', 'commentpress-core' ); ?></h2>



<?php

// Did we get any?
if ( count( $_users ) > 0 ) {

	// Open list.
	echo '<ul id="group_list">' . "\n";

	// Loop.
	foreach( $_users AS $_user ) {

		// Exclude admin.
		if( $_user->user_id != '1' ) {

			// Open item.
			echo '<li>' . "\n";

			// Show display name.
			echo  '<a href="' . home_url() . '/author/' . $_user->user_login . '/">' . $_user->display_name . '</a>';

			// Close item.
			echo '</li>' . "\n\n";

		}

	}

	// Close list.
	echo '</ul>' . "\n\n";

} ?>


</div><!-- /post -->



</div><!-- /content -->



</div><!-- /page_wrapper -->



</div><!-- /main_wrapper -->



</div><!-- /wrapper -->



<?php get_sidebar(); ?>



<?php get_footer(); ?>
