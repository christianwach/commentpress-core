<?php
/**
 * Multisite Network Settings Page "Submit" metabox template.
 *
 * Handles markup for the Multisite Network Settings Page "Submit" metabox.
 *
 * @package CommentPress_Core
 */

?>
<!-- includes/commentpress-multisite/assets/templates/wordpress/metaboxes/metabox-network-settings-submit.php -->
<div class="submitbox">
	<div id="minor-publishing">
		<div id="misc-publishing-actions">
			<div class="misc-pub-section">
				<span><?php esc_html_e( 'Save your settings here.', 'commentpress-core' ); ?></span>
			</div>
		</div>
		<div class="clear"></div>
	</div>

	<div id="major-publishing-actions">
		<div id="publishing-action">
			<?php submit_button( esc_html__( 'Update', 'commentpress-core' ), 'primary', 'cpmu_submit', false ); ?>
			<input type="hidden" name="action" value="update" />
		</div>
		<div class="clear"></div>
	</div>
</div>
