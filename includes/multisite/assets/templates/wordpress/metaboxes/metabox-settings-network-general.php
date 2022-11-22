<?php
/**
 * Multisite Network Settings Page "General Settings" metabox template.
 *
 * Handles markup for the Multisite Network Settings Page "General Settings" metabox.
 *
 * @package CommentPress_Core
 */

?>
<!-- includes/multisite/assets/templates/wordpress/metaboxes/metabox-settings-network-general.php -->
<table class="form-table">

	<?php

	/**
	 * Fires at the top of the "General Settings" metabox.
	 *
	 * @since 4.0
	 */
	do_action( 'commentpress/multisite/settings/network/metabox/general/before' );

	?>

	<tr valign="top">
		<th scope="row">
			<label for="cpmu_reset"><?php esc_html_e( 'Reset Multisite options', 'commentpress-core' ); ?>
		</label></th>
		<td>
			<input id="cpmu_reset" name="cpmu_reset" value="1" type="checkbox" />
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="cpmu_force_commentpress"><?php esc_html_e( 'Make all new sites CommentPress-enabled', 'commentpress-core' ); ?></label>
		</th>
		<td>
			<input id="cpmu_force_commentpress" name="cpmu_force_commentpress" value="1" type="checkbox"<?php echo ( $force_commentpress == '1' ? ' checked="checked"' : '' ); ?> />
		</td>
	</tr>

	<?php

	/**
	 * Fires at the bottom of the "General Settings" metabox.
	 *
	 * @since 4.0
	 */
	do_action( 'commentpress/multisite/settings/network/metabox/general/after' );

	?>

</table>