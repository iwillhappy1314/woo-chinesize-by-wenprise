<?php


/**
 * 优化地址
 */

use WooChinesize\Helper;

add_filter('woocommerce_shipping_calculator_enable_country', '__return_false');

add_filter('woocommerce_form_field_text', function ($field, $key, $args, $value)
{
    if ($key === 'billing_distpicker') {
        $field = '<div class="wccn-billing-distpicker">
            <p class="form-row address-field form-row-first wc-select validate-required validate-state">
                 <label for="wccn-state">省/直辖市/自治区*</label>
                 <select data-bind="billing-state" id="wccn-state"></select>
            </p>
            
            <p class="form-row address-field form-row-last wc-select validate-required validate-state">
                 <label for="wccn-city">城市 *</label>
                 <select data-bind="billing-city" id="wccn-city"></select>
            </p>
            
            <p class="form-row address-field form-row-addon wc-select validate-required validate-state">
                 <label for="wccn-area">区/县 *</label>
                 <select data-bind="billing-area" id="wccn-area"></select>
            </p>
        </div>';
    }

    if ($key === 'shipping_distpicker') {
        $field = '<div class="wccn-shipping-distpicker">
            <p class="form-row address-field form-row-first wc-select validate-required validate-state">
                 <label for="wccn-state">省/直辖市/自治区*</label>
                 <select data-bind="shipping-state" id="wccn-state"></select>
            </p>
            
            <p class="form-row address-field form-row-last wc-select validate-required validate-state">
                 <label for="wccn-city">城市 *</label>
                 <select data-bind="shipping-city" id="wccn-city"></select>
            </p>
            
            <p class="form-row address-field form-row-addon wc-select validate-required validate-state">
                 <label for="wccn-area">区/县 *</label>
                 <select data-bind="shipping-area" id="wccn-area"></select>
            </p>
        </div>';
    }


    return $field;
}, 10, 4);

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

    $address_id    = get_query_var('address-book-edit');
    $saved_address = [];

    if ($address_id) {
        $address_data = wc()->customer->get_meta('fabfw_address', false);

        $addresses = array_filter(array_values($address_data), function ($address) use ($address_id)
        {
            return $address->get_data()[ 'id' ] == $address_id;
        });

        if ($addresses) {
            $saved_address = array_values($addresses)[ 0 ]->get_data()[ 'value' ];
        }
    }

    $countries = new WC_Countries();

    // 国家
    $fields[ 'country' ][ 'class' ][] = 'wccn-is-hidden';

    $fields[ 'distpicker' ][ 'priority' ] = 60;
    $fields[ 'distpicker' ][ 'type' ]     = 'text';

    // 省/直辖市/自治区
    $fields[ 'state' ][ 'label' ]   = '省份';
    $fields[ 'state' ][ 'type' ]    = 'text';
    $fields[ 'state' ][ 'options' ] = $countries->get_states('CN');
    $fields[ 'state' ][ 'class' ][] = 'form-row-first';
    $fields[ 'state' ][ 'class' ][] = 'wc-select wccn-is-hidden';
    unset($fields[ 'state' ][ 'class' ][ 0 ]);

    // 城市
    $fields[ 'city' ][ 'priority' ]                           = 81;
    $fields[ 'city' ][ 'label' ]                              = '城市';
    $fields[ 'city' ][ 'class' ][]                            = 'form-row-last wccn-is-hidden';

    // 区/县
    $fields[ 'address_1' ][ 'priority' ]                           = 82;
    $fields[ 'address_1' ][ 'label' ]                              = '区/县';
    $fields[ 'address_1' ][ 'class' ][]                            = 'form-row-addon wccn-is-hidden';

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

    $fields[ 'shipping_state' ][ 'custom_attributes' ][ 'data-update' ]     = 'shipping-state';
    $fields[ 'shipping_city' ][ 'custom_attributes' ][ 'data-update' ]      = 'shipping-city';
    $fields[ 'shipping_address_1' ][ 'custom_attributes' ][ 'data-update' ] = 'shipping-area';

    return $fields;
});


/**
 * 设置账单地址字段值
 */
add_filter('woocommerce_billing_fields', function ($fields)
{
    $fields[ 'billing_state' ][ 'custom_attributes' ][ 'data-update' ]     = 'billing-state';
    $fields[ 'billing_city' ][ 'custom_attributes' ][ 'data-update' ]      = 'billing-city';
    $fields[ 'billing_address_1' ][ 'custom_attributes' ][ 'data-update' ] = 'billing-area';

    return $fields;
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

    $fields[ 'billing_city' ][ 'options' ] = $cities;
    // $fields[ 'billing_city' ][ 'custom_attributes' ][ 'data-default' ] = $city_name;

    $fields[ 'billing_address_1' ][ 'options' ] = $areas;

    // $fields[ 'billing_address_1' ][ 'custom_attributes' ][ 'data-default' ] = $area_name;

    return $fields;
}, 10, 3);


/**
 * 修改地址显示格式
 */
add_filter('woocommerce_localisation_address_formats', function ($formats)
{
    $formats[ 'CN' ] = "{state}{city}{address_1}{address_2}\n{company}\n{name}";

    return $formats;
});


add_action('wp_head', function ()
{
    echo "<style>
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


    // 移除这两个JS防止省份选择数据丢失
    wp_deregister_script('wc-country-select');
    wp_deregister_script('wc-address-i18n');
}, 100);