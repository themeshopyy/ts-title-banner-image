<?php
/*
 Plugin Name: TS Title Banner Image
 Plugin URI: https://www.themeshopy.com/
 Description: Use to update banner and show/hide title of internal pages.
 Author:  Themeshopy
 Version: 1.0
 Author URI: https://www.themeshopy.com/
*/

define( 'TS_TITLE_BANNER_IMAGE_VERSION', '0.1' );

/** Add side meta box for Banner image **/
function ts_title_banner_image_add_custom_meta_boxes() {

	// Define the Banner Image for custom post, posts & pages.
	$post_types = array ( 'post', 'page', 'destination' );
	add_meta_box(
		'ts_title_banner_image_metabox',
		__( 'Banner Image', 'lt-title-banner-image' ),
		'ts_title_banner_image_render_metabox',
		$post_types,
		'side'
	);

}

add_action('add_meta_boxes', 'ts_title_banner_image_add_custom_meta_boxes');

function ts_title_banner_image_render_metabox($post_id) {

	wp_nonce_field(basename(__FILE__), 'ts_title_banner_image_metabox_nonce');

	$ts_title_banner_image_title_on_off = get_post_meta($post_id->ID, 'ts_title_banner_image_title_on_off', true);
	$ts_title_banner_image_title_below_on_off = get_post_meta($post_id->ID, 'ts_title_banner_image_title_below_on_off', true);
	$ts_title_banner_image_wp_custom_attachment = get_post_meta($post_id->ID, 'ts_title_banner_image_wp_custom_attachment', true);
	$banner_url = '';
	if ( isset( $ts_title_banner_image_wp_custom_attachment ) ) {
		$banner_url = $ts_title_banner_image_wp_custom_attachment;
	}

	?>
	<div class="meta-wrapper">
		<div>
			<p><strong><?php esc_html_e("Upload Image","lt-title-banner-image") ?></strong></p>
			<input type="hidden" class="img widefat" name="ts_title_banner_image_wp_custom_attachment" id="ts_title_banner_image_wp_custom_attachment" value="<?php echo esc_url( $banner_url ); ?>" />
			<input type="button" class="select-img button button-primary" value="<?php esc_attr_e( 'Upload', 'lt-title-banner-image' ); ?>" data-uploader_title="<?php esc_attr_e( 'Select Image', 'lt-title-banner-image' ); ?>" data-uploader_button_text="<?php esc_attr_e( 'Choose Image', 'lt-title-banner-image' ); ?>" />
			<?php
			$wrap_style = '';
			if ( empty( $banner_url ) ) {
				$wrap_style = ' style="display:none;" ';
			}
			?>
			<div class="custom-theme-preview-wrap" <?php echo $wrap_style; ?>>
				<img src="<?php echo esc_url( $banner_url ); ?>" alt="" />
			</div>
		</div>

		<p><input type="checkbox" name="wp_custom_attachment_remove" id="wp_custom_attachment_remove" value="on" />&nbsp;<?php esc_html_e("Remove banner","lt-title-banner-image") ?></p>
		<hr />
		<p><input type="checkbox" name="ts_title_banner_image_title_on_off" id="ts_title_banner_image_title_on_off" value="on" <?php checked( $ts_title_banner_image_title_on_off, 'on' ); ?> />&nbsp;<?php esc_html_e("Hide Title above banner","lt-title-banner-image") ?></p>
		<p><input type="checkbox" name="ts_title_banner_image_title_below_on_off" id="ts_title_banner_image_title_below_on_off" value="on" <?php checked( $ts_title_banner_image_title_below_on_off, 'on' ); ?> />&nbsp;<?php esc_html_e("Show Title below banner","lt-title-banner-image") ?></p>
	</div><!-- .meta-wrapper -->
	<?php
}

function ts_title_banner_image_save_custom_meta_data($post_id) {
	if (!isset($_POST['ts_title_banner_image_metabox_nonce']) || !wp_verify_nonce($_POST['ts_title_banner_image_metabox_nonce'], basename(__FILE__))) {
		return;
	}

	if (!current_user_can('edit_post', $post_id)) {
		return;
	}

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	$ts_title_banner_image_title_on_off = ( isset( $_POST['ts_title_banner_image_title_on_off'] ) && 'on' === $_POST['ts_title_banner_image_title_on_off'] ) ? 'on' : '';
	update_post_meta($post_id, 'ts_title_banner_image_title_on_off', $ts_title_banner_image_title_on_off);

	$ts_title_banner_image_title_below_on_off = ( isset( $_POST['ts_title_banner_image_title_below_on_off'] ) && 'on' === $_POST['ts_title_banner_image_title_below_on_off'] ) ? 'on' : '';
	update_post_meta($post_id, 'ts_title_banner_image_title_below_on_off', $ts_title_banner_image_title_below_on_off);

	if ( isset( $_POST['ts_title_banner_image_wp_custom_attachment'] ) ) {
		$upload = esc_url_raw( $_POST['ts_title_banner_image_wp_custom_attachment'] );
		update_post_meta($post_id, 'ts_title_banner_image_wp_custom_attachment', $upload);
	}

	$wp_custom_attachment_remove = ( isset( $_POST['wp_custom_attachment_remove'] ) && 'on' === $_POST['wp_custom_attachment_remove'] ) ? 'on' : '';

	if ( 'on' === $wp_custom_attachment_remove ) {
		// Remove banner image.
		update_post_meta($post_id, 'ts_title_banner_image_wp_custom_attachment', '');
	}

}

add_action('save_post', 'ts_title_banner_image_save_custom_meta_data');

function ts_title_banner_image_metabox_enqueue($hook) {
	if ( 'post.php' === $hook || 'post-new.php' === $hook ) {
		wp_enqueue_style('lt-title-banner-image-metabox', plugin_dir_url( __FILE__ ) . '/css/admin.css');
		wp_enqueue_script('lt-title-banner-image-metabox', plugin_dir_url( __FILE__ ) . '/js/admin.js', array('jquery'));
	}
}

add_action('admin_enqueue_scripts', 'ts_title_banner_image_metabox_enqueue');
