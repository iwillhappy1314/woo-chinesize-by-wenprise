<?php


/**
 * 优化地址
 */

use WooChinesize\Helper;

/**
 * 修改默认字段
 */
add_filter('woocommerce_default_address_fields', function ($fields)
{
    if (get_option('wccn_remove_company_fields', 'no') === 'yes') {
        unset($fields[ 'company' ]);
    }

    if (get_option('wccn_remove_post_fields', 'no') === 'yes') {
        unset($fields[ 'postcode' ]);
    }

    if (get_option('wccn_chinesized_address_field_enabled', 'yes') !== 'yes') {
        return $fields;
    }

    $user_id = get_current_user_id();

    $state_code = get_user_meta($user_id, 'billing_state', true);

    // 国家
    $fields[ 'country' ][ 'class' ][] = 'wccn-is-hidden';

    // 省/直辖市/自治区
    $fields[ 'state' ][ 'label' ]                               = '省份';
    $fields[ 'state' ][ 'class' ][]                             = 'form-row-first';
    $fields[ 'state' ][ 'class' ][]                             = 'wc-select';
    $fields[ 'state' ][ 'custom_attributes' ][ 'data-default' ] = $state_code;
    unset($fields[ 'state' ][ 'class' ][ 0 ]);

    // 城市
    $fields[ 'city' ][ 'priority' ] = 81;
    $fields[ 'city' ][ 'label' ]    = '城市';
    $fields[ 'city' ][ 'type' ]     = 'select';
    $fields[ 'city' ][ 'class' ][]  = 'form-row-last';
    $fields[ 'city' ][ 'class' ][]  = 'wc-select';

    // 区/县
    $fields[ 'address_1' ][ 'priority' ] = 82;
    $fields[ 'address_1' ][ 'label' ]    = '区/县';
    $fields[ 'address_1' ][ 'type' ]     = 'select';
    $fields[ 'address_1' ][ 'class' ][]  = 'wc-select form-row-addon';

    // 详细地址
    $fields[ 'address_2' ][ 'label' ]       = '详细地址';
    $fields[ 'address_2' ][ 'type' ]        = 'textarea';
    $fields[ 'address_2' ][ 'priority' ]    = 83;
    $fields[ 'address_2' ][ 'required' ]    = true;
    $fields[ 'address_2' ][ 'placeholder' ] = '请输入详细地址';

    // 收件人
    $fields[ 'first_name' ][ 'class' ][ 0 ] = 'form-row-wide';
    $fields[ 'first_name' ][ 'label' ]      = '收货人';
    $fields[ 'first_name' ][ 'priority' ]   = 90;

    unset($fields[ 'city' ][ 'class' ][ 0 ]);
    unset($fields[ 'last_name' ]);

    return $fields;
}, 20, 1);


/**
 * 设置收件地址字段值
 */
add_filter('woocommerce_shipping_fields', function ($fields)
{

    $user_id = get_current_user_id();

    $state_code = get_user_meta($user_id, 'shipping_state', true);
    $city_name  = get_user_meta($user_id, 'shipping_city', true);
    $area_name  = get_user_meta($user_id, 'shipping_address_1', true);

    $cities = Helper::get_state_cities($state_code);
    $cities = wp_list_pluck($cities, 'name', 'name');

    $areas = Helper::get_city_areas($state_code, $city_name);
    $areas = wp_list_pluck($areas, 'name', 'name');

    $fields[ 'shipping_city' ][ 'options' ]                             = $cities;
    $fields[ 'shipping_city' ][ 'custom_attributes' ][ 'data-default' ] = $city_name;

    $fields[ 'shipping_address_1' ][ 'options' ]                             = $areas;
    $fields[ 'shipping_address_1' ][ 'custom_attributes' ][ 'data-default' ] = $area_name;

    return $fields;
});


/**
 * 设置账单地址字段值
 */
add_filter('woocommerce_billing_fields', function ($fields)
{
    $user_id = get_current_user_id();

    $state_code = get_user_meta($user_id, 'billing_state', true);
    $city_name  = get_user_meta($user_id, 'billing_city', true);
    $area_name  = get_user_meta($user_id, 'billing_address_1', true);

    $cities = Helper::get_state_cities($state_code);
    $cities = wp_list_pluck($cities, 'name', 'name');

    $areas = Helper::get_city_areas($state_code, $city_name);
    $areas = wp_list_pluck($areas, 'name', 'name');

    $fields[ 'billing_city' ][ 'options' ]                             = $cities;
    $fields[ 'billing_city' ][ 'custom_attributes' ][ 'data-default' ] = $city_name;

    $fields[ 'billing_address_1' ][ 'options' ]                             = $areas;
    $fields[ 'billing_address_1' ][ 'custom_attributes' ][ 'data-default' ] = $area_name;

    return $fields;
});


/**
 * 修改地址显示格式
 */
add_filter('woocommerce_localisation_address_formats', function ($formats)
{
    $formats[ 'CN' ] = "{state}{city}{address_1}{address_2}\n{company}\n{name}";

    return $formats;
});


/**
 * 兼容 fr-address-book-for-woocommerce 插件
 */
add_filter('fr_address_book_for_woocommerce_address_fields', function ($fields, $address_id, $saved_addresses)
{
    $state_code = $saved_addresses[ $address_id ][ 'state' ];
    $city_name  = $saved_addresses[ $address_id ][ 'city' ];
    $area_name  = $saved_addresses[ $address_id ][ 'address_1' ];

    $cities = Helper::get_state_cities($state_code);
    $cities = wp_list_pluck($cities, 'name', 'name');

    $areas = Helper::get_city_areas($state_code, $city_name);
    $areas = wp_list_pluck($areas, 'name', 'name');

    $fields[ 'billing_city' ][ 'options' ]                             = $cities;
    $fields[ 'billing_city' ][ 'custom_attributes' ][ 'data-default' ] = $city_name;

    $fields[ 'billing_address_1' ][ 'options' ]                             = $areas;
    $fields[ 'billing_address_1' ][ 'custom_attributes' ][ 'data-default' ] = $area_name;

    return $fields;
}, 10, 3);


// add_filter('woocommerce_checkout_fields');

add_action('wp_head', function ()
{
    echo "<style type='text/css'>
                .wccn-is-hidden {
                    display: none !important;
                }
            </style>";
});


/**
 * 移除 Select2 - Woocommerce 3.2.1+
 */
add_action('wp_enqueue_scripts', function ()
{

    if (get_option('wccn_disable_select2', 'no') === 'yes') {
        if (class_exists('woocommerce')) {
            wp_dequeue_style('select2');
            wp_deregister_style('select2');

            wp_dequeue_script('selectWoo');
            wp_deregister_script('selectWoo');
        }
    }

}, 100);