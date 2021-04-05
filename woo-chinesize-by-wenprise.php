<?php
/**
 * Plugin Name: WooCommerce Chinesize by Wenprise
 * Plugin URI: https://www.wpzhiku.com/woocommerce-chinesize
 * Description: 优化 WooCommerce 在中国的使用体验，地址字段重新排序，实现省市关联选择
 * Version: 1.1.2
 * Author: WordPress智库
 * Author URI: https://www.wpzhiku.com
 * Text Domain: wc-chinesize
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


add_action('plugins_loaded', function ()
{
    load_plugin_textdomain('wc-chinesize', false, dirname(plugin_basename(__FILE__)) . '/languages/');

    require WENPRISE_WC_CHINESIZE_PATH . 'src/helpers.php';
    require WENPRISE_WC_CHINESIZE_PATH . 'src/actions.php';

    add_filter('woocommerce_get_settings_pages', function ($settings)
    {
        $settings[] = include WENPRISE_WC_CHINESIZE_PATH . 'src/settings.php';

        return $settings;
    });

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

    if (get_option('wccn_order_list_template_enabled', 'yes') !== 'yes') {
        return $template;
    }

    $_template = $template;

    if ( ! $template_path) {
        $template_path = $woocommerce->template_url;
    }

    $plugin_path = untrailingslashit(WENPRISE_WC_CHINESIZE_PATH) . '/templates/woocommerce/';

    // Look within passed path within the theme - this is priority
    $template = locate_template([$template_path . $template_name, $template_name,]);

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
    if (get_option('wccn_order_detail_template_enabled', 'yes') !== 'yes') {
        return $html;
    }

    $item_data  = $item->get_data();
    $product_id = $item_data[ 'product_id' ];

    $addon = '<a class="wccn-float-left wccn-mr-4" target=_blank href="' . get_permalink($product_id) . '">';
    $addon .= wp_get_attachment_image(get_post_thumbnail_id($product_id), [64, 64]);
    $addon .= '</a>';

    return $addon . $html;

}, 10, 3);


add_action('woocommerce_before_account_orders', function ()
{
    if (get_option('wccn_order_filter_enabled') !== 'yes') {
        return false;
    }

    $order_status   = wc_get_order_statuses();
    $allowed_status = (array)get_option('wccn_order_status_allowed_to_filter', array_keys(wc_get_order_statuses()));

    $status = isset($_GET[ 'wccn-status' ]) ? $_GET[ 'wccn-status' ] : false;

    $html = '<div class="wccn-order__filter">';

    if ( ! $status) {
        $html .= '<a class="wccn-order__filter-active" href="' . remove_query_arg('wccn-status') . '">' . __('All', 'wc-chinesize') . '</a>';
    } else {
        $html .= '<a href="' . remove_query_arg('wccn-status') . '">' . __('All', 'wc-chinesize') . '</a>';
    }

    foreach ($order_status as $key => $name) {
        $status_slug = substr($key, 3, strlen($key) - 3);

        $args = [
            'numberposts' => -1,
            'meta_key'    => '_customer_user',
            'meta_value'  => get_current_user_id(),
            'post_type'   => wc_get_order_types('view-orders'),
        ];

        if ($status_slug) {
            $args[ 'post_status' ] = [$key];
        }

        $customer_orders = get_posts(apply_filters('woocommerce_my_account_my_orders_query', $args));

        if (wc_orders_count($status_slug) !== 0 && in_array($key, $allowed_status) && $customer_orders) {
            $link = add_query_arg('wccn-status', $status_slug);

            $status_name_html = '<span class="wccn-order__filter-name">' . $name . '</span>';

            $status_count_html = '<span class="wccn-order__filter-count">';
            $status_count_html .= count($customer_orders);
            $status_count_html .= '</span>';

            if ($key === 'wc-' . $status) {
                $html .= '<a class="wccn-order__filter-active" href="' . $link . '">' . $status_name_html . $status_count_html . '</a>';
            } else {
                $html .= '<a href="' . $link . '">' . $status_name_html . $status_count_html . '</a>';
            }
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


add_filter('woocommerce_order_item_class', function ($class, $item, $order)
{
    return $class . ' wccn-order-detail-item';
}, 12, 3);