<?php
/**
 * Plugin Name: WooCommerce Chinesize by Wenprise
 * Plugin URI: https://www.wpzhiku.com/woocommerce-chinesize
 * Description: 优化 WooCommerce 在中国的使用体验，地址字段重新排序，实现省市关联选择
 * Version: 1.0.2
 * Author: WordPress智库
 * Author URI: https://www.wpzhiku.com
 * Text Domain: wprs-wc-chinesize
 * Domain Path: /languages
 */

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ))) {
    return false;
}

define('WENPRISE_WC_CHINESIZE_FILE_PATH', __FILE__);
define('WENPRISE_WC_CHINESIZE_PATH', plugin_dir_path(__FILE__));
define('WENPRISE_WC_CHINESIZE_URL', plugin_dir_url(__FILE__));

require WENPRISE_WC_CHINESIZE_PATH . 'src/helpers.php';
require WENPRISE_WC_CHINESIZE_PATH . 'src/actions.php';


add_action(
    'wp_enqueue_scripts',
    function ()
    {
        if (is_checkout() || is_wc_endpoint_url('edit-address')) {
            wp_register_script('wprs-city-picker', WENPRISE_WC_CHINESIZE_URL.'assets/scripts/city-picker.js', ['jquery'], 1.0, true);
            wp_enqueue_script('wprs-city-picker');
        }
    },
    999
);