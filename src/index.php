<?php

/**
 *  Plugin Name: woocommerce product magnifier
 *  Plugin URI: https://github.com/hexboy/woocommerce-product-magnifier
 *  Description: simple wordpress plugin, zoom image of product
 *  Author: Hexboy
 *  Version: 1.0.
 *  Author URI: https://github.com/hexboy/
 */

function iplug_zoom_product_image() {
	global $product, $post;
	if ( !function_exists( 'is_woocommerce' ) ) return;
	if ( !is_woocommerce() || !is_product() ) return;
	if ( $product->get_image_id() < 1 ) return;

	// enqueue style and script
	wp_enqueue_style('Zoom-Product-Style', plugins_url('/css/Zoom-Product.min.css', __FILE__));
	wp_enqueue_script('Zoom-Product-Script', plugins_url('/js/Zoom-Product.min.js', __FILE__), array('jquery'), '2.0.0', true);

	// get plugin options

	$iscircle    = get_option('iplug_iscircle',1) ? 1 : 0;
	$transparent = get_option('iplug_transparent',0) ? 1 : 0;
	$zoom_level  = get_option('iplug_zoom_level',1.2);
	$zoom_size   = get_option('iplug_zoom_size',300);
	?>
	<script type='text/javascript'>
		var ipzo = {'transparent': <?=$transparent?>, 'iscircle': <?=$iscircle?>, 'zoomLevel': <?=$zoom_level?>, 'zoomSize': <?=$zoom_size?>,'bg': "<?=plugins_url('/images/bg.jpg', __FILE__)?>"};
	</script>
	<?php
}

add_action('wp_footer', "iplug_zoom_product_image");

//Remove prettyPhoto lightbox
add_action('wp_enqueue_scripts', 'iplug_remove_woo_lightbox', 99);
function iplug_remove_woo_lightbox() {
	wp_dequeue_style('woocommerce_prettyPhoto_css');
	// wp_dequeue_script('prettyPhoto');
	// wp_dequeue_script('prettyPhoto-init');
	// wp_dequeue_script('flexslider');
	// wp_dequeue_script('photoswipe');
	// wp_dequeue_script('photoswipe-ui-default');
	wp_dequeue_script('zoom');
}


add_action('admin_menu', 'iplug_register_zoom_menu_page');

function iplug_register_zoom_menu_page() {
	$plugin_dir_path = dirname(__FILE__);
	add_menu_page('Zoom Image', 'Zoom Image', 'manage_options', 'zoom-image', 'iplug_zoom_image_menu_page', plugins_url('/images/icon.svg', __FILE__), 6);
}

function iplug_zoom_image_menu_page() {
	?>
	    <div class="wrap">
	    <h1>IPlug zoom Image</h1>
	    <form method="post" action="options.php">
	        <?php
				settings_fields("iplug-zoom-image");
				do_settings_sections("iplug-zoom");
				submit_button();
			?>
	    </form>
		</div>
	<?php
}


function iplug_display_zoom_level() {
    echo '<input type="text"  type="number" name="iplug_zoom_level" id="iplug_zoom_level" value="'. get_option('iplug_zoom_level',1). '"/>';
}

function iplug_display_zoom_size() {
    echo '<input type="text" name="iplug_zoom_size" id="iplug_zoom_size" value="' . get_option('iplug_zoom_size',300) . '" />';
}

function iplug_display_transparent() {
	echo '<input type="checkbox" name="iplug_transparent" value="' . get_option('iplug_transparent',1) . '"  ' . checked(1, get_option('iplug_transparent',1), false) .'/>';
}

function iplug_display_iscircle() {
	echo '<input type="checkbox" name="iplug_iscircle" value="'. get_option('iplug_iscircle',1) .'" '. checked(1, get_option('iplug_iscircle',1), false) .'/>';
}

function iplug_display_addon_panel_fields() {
	add_settings_section("iplug-zoom-image", "تنظیمات بزرگنمایی تصویر", null, "iplug-zoom");

	add_settings_field("iplug_zoom_level", "Set Zoom Level", "iplug_display_zoom_level", "iplug-zoom", "iplug-zoom-image");
	add_settings_field("iplug_zoom_size", "Set Zoom Width", "iplug_display_zoom_size", "iplug-zoom", "iplug-zoom-image");
	add_settings_field("iplug_transparent", "Transparent", "iplug_display_transparent", "iplug-zoom", "iplug-zoom-image");
	add_settings_field("iplug_iscircle", "Do you want show Circle ?", "iplug_display_iscircle", "iplug-zoom", "iplug-zoom-image");

	register_setting("iplug-zoom-image", "iplug_zoom_level");
	register_setting("iplug-zoom-image", "iplug_zoom_size");
	register_setting("iplug-zoom-image", "iplug_transparent");
	register_setting("iplug-zoom-image", "iplug_iscircle");
}

add_action("admin_init", "iplug_display_addon_panel_fields");

add_action('admin_head', 'iplug_zoom_product_admin_style');
function iplug_zoom_product_admin_style() {
  echo	'<style>
		    li#toplevel_page_zoom-image .wp-menu-image img {
		      height: 100%;
		      padding: 0;
		    }
		</style>';
}