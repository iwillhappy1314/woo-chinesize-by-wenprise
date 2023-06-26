<?php
/**
 * Plugin Name: WooCommerce Chinesize by Wenprise
 * Plugin URI: https://www.wpzhiku.com/woocommerce-chinesize
 * Description: 优化 WooCommerce 在中国的使用体验，地址字段重新排序，实现省市关联选择
 * Version: 1.3.1
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

const WENPRISE_WC_CHINESIZE_VERSION = '1.0.3';
const WENPRISE_WC_CHINESIZE_FILE    = __FILE__;
define('WENPRISE_WC_CHINESIZE_PATH', plugin_dir_path(__FILE__));
define('WENPRISE_WC_CHINESIZE_URL', plugin_dir_url(__FILE__));


add_action('plugins_loaded', function ()
{
    load_plugin_textdomain('wc-chinesize', false, dirname(plugin_basename(__FILE__)) . '/languages/');

    require WENPRISE_WC_CHINESIZE_PATH . 'vendor/autoload.php';

    new \WooChinesize\Init();
});