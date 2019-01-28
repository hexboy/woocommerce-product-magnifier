<?php

/**
 *  Plugin Name: woocommerce product magnifier
 *  Plugin URI: https://github.com/hexboy/woocommerce-product-magnifier
 *  Description: simple wordpress plugin, zoom image of product
 *  Author: Hexboy
 *  Version: 1.0.
 *  Author URI: https://github.com/hexboy/
 */

 /**
 * Load CMB2 plugin
 */
require_once WP_PLUGIN_DIR . '/cmb2/init.php';

add_action('wp_footer', 'ipzo_product_image');
function ipzo_product_image()
{
    global $product, $post;
    if (!function_exists('is_woocommerce')) {
        return;
    }
    if (!is_woocommerce() || !is_product()) {
        return;
    }
    if ($product->get_image_id() < 1) {
        return;
    }

    // enqueue style and script
    wp_enqueue_style('Zoom-Product-Style', plugins_url('/css/Zoom-Product.css', __FILE__));
    wp_enqueue_script('Zoom-Product-Script', plugins_url('/js/Zoom-Product.js', __FILE__), ['jquery'], '2.0.0', true);
}

add_action('wp_enqueue_scripts', 'ipzo_frontend_assets');
function ipzo_frontend_assets()
{
    // get plugin options
    $ipzo = [
        'transparency' => ipzo_get_option('_transparency', 0),
        'isCircle' => ipzo_get_option('_is_circle', 1),
        'zoomLevel' => ipzo_get_option('_zoom_level', 1.2),
        'magnifierSize' => ipzo_get_option('_magnifier_size', 300),
        'maxWidth' => ipzo_get_option('_max_width', 1),
        'maxHeight' => ipzo_get_option('_max_height', 1),
        'bg' => plugins_url('/images/bg.jpg', __FILE__)
    ];

    wp_localize_script('jquery', 'ipzo', $ipzo);
}

//Remove prettyPhoto lightbox
add_action('wp_enqueue_scripts', 'ipzo_remove_woo_lightbox', 99);
function ipzo_remove_woo_lightbox()
{
    wp_dequeue_style('woocommerce_prettyPhoto_css');
    // wp_dequeue_script('prettyPhoto');
    // wp_dequeue_script('prettyPhoto-init');
    // wp_dequeue_script('flexslider');
    // wp_dequeue_script('photoswipe');
    // wp_dequeue_script('photoswipe-ui-default');
    wp_dequeue_script('zoom');
}

add_action('cmb2_admin_init', 'cmb2_ipzo_option_metabox');

/**
 * Define the metabox and field configurations.
 */
function cmb2_ipzo_option_metabox()
{
    // Start with an underscore to hide fields from custom fields list
    $prefix = '_ipzo';

    /**
     * Initiate the metabox
     */
    $cmb = new_cmb2_box([
        'id' => $prefix,
        'title' => 'IPlug zoom Image',
        'object_types' => ['options-page'],
        'context' => 'normal',
        'priority' => 'high',
        'show_names' => true, // Show field names on the left
        'option_key' => 'ipzo-wpm-options', // The option key and admin menu page slug.
        'menu_title' => 'zoom Image',
        'icon_url' => 'dashicons-search'
    ]);

    $cmb->add_field([
        'name' => 'میزان بزرگنمایی',
        'id' => $prefix . '_zoom_level',
        'type' => 'text',
        'default' => '1.2'
    ]);

    $cmb->add_field([
        'name' => 'قطر ذره‌بین (پیکسل)',
        'id' => $prefix . '_magnifier_size',
        'type' => 'text',
        'default' => '300'
    ]);

    $cmb->add_field([
        'name' => ' حد اکثر عرض (پیکسل)',
        'id' => $prefix . '_max_width',
        'type' => 'text',
        'default' => '800'
    ]);

    $cmb->add_field([
        'name' => 'حداکثر ارتفاع (پیکسل)',
        'id' => $prefix . '_max_height',
        'type' => 'text',
        'default' => '800'
    ]);

    $cmb->add_field([
        'name' => 'شفافیت ذره‌بین',
        'id' => $prefix . '_transparency',
        'type' => 'radio_inline',
        'options' => [
            '1' => __('فعال', 'cmb2'),
            '0' => __('غیرفعال', 'cmb2'),
        ],
        'default' => '1'
    ]);

    $cmb->add_field([
        'name' => 'شکل ذره‌بین',
        'id' => $prefix . '_is_circle',
        'type' => 'radio_inline',
        'options' => [
            '1' => __('دایره', 'cmb2'),
            '0' => __('مربع', 'cmb2'),
        ],
        'default' => '1'
    ]);
}

/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 * @param  string $key     Options array key
 * @param  mixed  $default Optional default value
 * @return mixed           Option value
 */
function ipzo_get_option($key = '', $default = false)
{
    $prefix = '_ipzo';
    $key    = $prefix . $key;
    if (function_exists('cmb2_get_option')) {
        // Use cmb2_get_option as it passes through some key filters.
        return cmb2_get_option('ipzo-wpm-options', $key, $default);
    }
    // Fallback to get_option if CMB2 is not loaded yet.
    $opts = get_option('ipzo-wpm-options', $default);
    $val  = $default;
    if ('all' == $key) {
        $val = $opts;
    } elseif (is_array($opts) && array_key_exists($key, $opts) && false !== $opts[$key]) {
        $val = $opts[$key];
    }
    return $val;
}
