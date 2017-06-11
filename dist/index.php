<?php
/**
 * @package woocommerce-product-magnifier
 * @version 1.0
 */
/*
Plugin Name: woocommerce product magnifier
Plugin URI: https://github.com/hexboy/woocommerce-product-magnifier
Description: simple wordpress plugin, zoom image of product
Author: Hexboy
Version: 1.0.
Author URI: https://github.com/hexboy/
 */
function hex_zoom_product_image() {
	global $product, $post;
	if ( !function_exists( 'is_woocommerce' ) ) return;
	if ( !is_woocommerce() || !is_product() ) return;
	if ( $product->get_image_id() < 1 ) return;

	// enqueue style and script
	wp_enqueue_style('Zoom-Product-Style', plugins_url('/css/Zoom-Product.min.css', __FILE__));
	wp_enqueue_script('Zoom-Product-Script', plugins_url('/js/Zoom-Product.min.js', __FILE__), array('jquery'), '2.0.0', true);

	// get plugin options

	$iscircle = get_option('iplug_iscircle',1);
	$transparent = get_option('iplug_transparent',0);
	$zoom_level = get_option('iplug_zoom_level',1.2);
	$zoom_size = get_option('iplug_zoom_size',300);
	?>
	<script type='text/javascript'>
		var ipzo = {'transparent': <?=$transparent?>, 'iscircle': <?=$iscircle?>, 'zoomLevel': <?=$zoom_level?>, 'zoomSize': <?=$zoom_size?>,'bg': "<?=plugins_url('/images/bg.jpg', __FILE__)?>"};
	</script>
	<?php
}

add_action('wp_footer', "hex_zoom_product_image");

//Remove prettyPhoto lightbox
add_action('wp_enqueue_scripts', 'hex_remove_woo_lightbox', 99);
function hex_remove_woo_lightbox() {
	wp_dequeue_style('woocommerce_prettyPhoto_css');
	wp_dequeue_script('prettyPhoto');
	wp_dequeue_script('prettyPhoto-init');
}

//LAB GLAD

add_action('admin_menu', 'register_iplug_zoom_menu_page');

function register_iplug_zoom_menu_page() {
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

//---------------------------

function display_zoom_level() {
	?>
    	<input type="text"  type="number" name="iplug_zoom_level" id="iplug_zoom_level" value="<?php echo get_option('iplug_zoom_level',1); ?>" />
    <?php
}
function display_zoom_size() {
	?>
    	<input type="text" name="iplug_zoom_size" id="iplug_zoom_size" value="<?php echo get_option('iplug_zoom_size',300); ?>" />
    <?php
}

function display_transparent() {
	?>
    	<input type="checkbox" name="iplug_transparent" value="1" <?php checked(1, get_option('iplug_transparent',1), true);?> />
    <?php
}

function display_iscircle() {
	?>
		<input type="checkbox" name="iplug_iscircle" value="1" <?php checked(1, get_option('iplug_iscircle',1), true);?> />
	<?php
}

function display_addon_panel_fields() {
	add_settings_section("iplug-zoom-image", "تنظیمات بزرگنمایی تصویر", null, "iplug-zoom");
	add_settings_field("iplug_zoom_level", "Set Zoom Level", "display_zoom_level", "iplug-zoom", "iplug-zoom-image");
	add_settings_field("iplug_zoom_size", "Set Zoom Width", "display_zoom_size", "iplug-zoom", "iplug-zoom-image");
	add_settings_field("iplug_transparent", "Transparent", "display_transparent", "iplug-zoom", "iplug-zoom-image");
	add_settings_field("iplug_iscircle", "Do you want show Circle ?", "display_iscircle", "iplug-zoom", "iplug-zoom-image");

	register_setting("iplug-zoom-image", "iplug_zoom_level");
	register_setting("iplug-zoom-image", "iplug_zoom_size");
	register_setting("iplug-zoom-image", "iplug_transparent");
	register_setting("iplug-zoom-image", "iplug_iscircle");
}

add_action("admin_init", "display_addon_panel_fields");

add_action('admin_head', 'hex_zoom_product_admin_style');
function hex_zoom_product_admin_style() {
  echo '<style>
    li#toplevel_page_zoom-image .wp-menu-image img {
      height: 100%;
      padding: 0;
    }
  </style>';
}