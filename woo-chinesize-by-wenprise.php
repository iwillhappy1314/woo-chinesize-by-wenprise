<?php
/**
 * Plugin Name: WooCommerce Chinesize by Wenprise
 * Plugin URI: https://www.wpzhiku.com/woocommerce-chinesize
 * Description: 优化 WooCommerce 在中国的使用体验，地址字段重新排序，实现省市关联选择
 * Version: 1.0.3
 * Author: WordPress智库
 * Author URI: https://www.wpzhiku.com
 * Text Domain: wprs-wc-chinesize
 * Domain Path: /languages
 */

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if ( ! in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    return false;
}

define('WENPRISE_WC_CHINESIZE_VERSION', '1.0.3');
define('WENPRISE_WC_CHINESIZE_FILE_PATH', __FILE__);
define('WENPRISE_WC_CHINESIZE_PATH', plugin_dir_path(__FILE__));
define('WENPRISE_WC_CHINESIZE_URL', plugin_dir_url(__FILE__));

require WENPRISE_WC_CHINESIZE_PATH . 'src/helpers.php';
require WENPRISE_WC_CHINESIZE_PATH . 'src/actions.php';

add_action('plugins_loaded', function ()
{
    load_plugin_textdomain('wc-chinesize', false, dirname(plugin_basename(__FILE__)) . '/languages/');
});

add_action('wp_enqueue_scripts', function ()
{
    if (is_checkout() || is_wc_endpoint_url('edit-address')) {
        wp_register_script('wccn-city-picker', WENPRISE_WC_CHINESIZE_URL . 'assets/scripts/city-picker.js', ['jquery'], WENPRISE_WC_CHINESIZE_VERSION, true);
        wp_enqueue_script('wccn-city-picker');
    }

    wp_enqueue_style('wccn-style', WENPRISE_WC_CHINESIZE_URL . 'assets/styles/style.css', [], WENPRISE_WC_CHINESIZE_VERSION, '');
}, 999);


add_filter('woocommerce_locate_template', function ($template, $template_name, $template_path)
{
    global $woocommerce;

    $_template = $template;

    if ( ! $template_path) {
        $template_path = $woocommerce->template_url;
    }

    $plugin_path = untrailingslashit(WENPRISE_WC_CHINESIZE_PATH) . '/templates/woocommerce/';

    // Look within passed path within the theme - this is priority
    $template = locate_template([$template_path . $template_name, $template_name,]
    );

    if ( ! $template && file_exists($plugin_path . $template_name)) {
        $template = $plugin_path . $template_name;
    }

    if ( ! $template) {
        $template = $_template;
    }

    return $template;
}, 1, 3);


/**
 * 订单详情中添加图像
 */
add_filter('woocommerce_order_item_name', function ($html, $item, $is_visible)
{
    $item_data  = $item->get_data();
    $product_id = $item_data[ 'product_id' ];

    $addon = '<a class="wccn-position-left wccn-mr-4" target=_blank href="' . get_permalink($product_id) . '">';
    $addon .= wp_get_attachment_image(get_post_thumbnail_id($product_id), [64, 64]);
    $addon .= '</a>';

    return $addon . $html;

}, 10, 3);


add_action('woocommerce_account_dashboard', function ()
{
    echo 'sfiefinijnv';
});

add_action('woocommerce_before_account_orders', function ()
{
    $order_status = wc_get_order_statuses();
    $status       = isset($_GET[ 'wccn-status' ]) ? $_GET[ 'wccn-status' ] : false;

    $html = '<div class="wccn-order__filter">';

    if(!$status){
        $html .= '<a class="wccn-order__filter-active" href="' . remove_query_arg('wccn-status') . '">' . __('All', 'wc-chinesize') . '</a>';
    } else{
        $html .= '<a href="' . remove_query_arg('wccn-status') . '">' . __('All', 'wc-chinesize') . '</a>';
    }

    foreach ($order_status as $key => $name) {
        $link = add_query_arg('wccn-status', substr($key, 3, strlen($key) - 3));

        if ($key === 'wc-' . $status) {
            $html .= '<a class="wccn-order__filter-active" href="' . $link . '">' . $name . '</a>';
        } else {
            $html .= '<a href="' . $link . '">' . $name . '</a>';
        }

    }

    $html .= '</div>';

    echo $html;
});


add_filter('woocommerce_order_query_args', function ($args)
{
    $status = isset($_GET[ 'wccn-status' ]) ? $_GET[ 'wccn-status' ] : false;

    if ($status) {
        $args[ 'status' ] = [$status];
    }

    return $args;
});