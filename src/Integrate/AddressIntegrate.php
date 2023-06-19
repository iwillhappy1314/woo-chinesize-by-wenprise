<?php

namespace WooChinesize\Integrate;


use WooChinesize\Helpers;

class AddressIntegrate
{

    public function __construct()
    {
        /**
         * 添加 alpine data wrapper
         */
        add_action('woocommerce_before_checkout_form', [$this, 'add_alpine_start_wrapper']);
        add_action('woocommerce_after_checkout_form', [$this, 'add_alpine_end_wrapper']);

        add_action('woocommerce_before_edit_account_address_form', [$this, 'add_alpine_start_wrapper']);
        add_action('woocommerce_after_edit_account_address_form', [$this, 'add_alpine_end_wrapper']);

        add_action('woocommerce_before_shipping_calculator', [$this, 'add_alpine_start_wrapper']);
        add_action('woocommerce_after_shipping_calculator', [$this, 'add_alpine_end_wrapper']);

        /**
         * 禁用计算器中的国家选择
         */
        add_filter('woocommerce_shipping_calculator_enable_country', '__return_false');


        /**
         * 添加省市区选择表单
         */
        add_filter('woocommerce_form_field_text', [$this, 'add_dist_picker_fields'], 10, 4);

        /**
         * 修改默认结账地址表单
         */
        add_filter('woocommerce_default_address_fields', [$this, 'modify_default_fields'], 20, 1);
        add_filter('woocommerce_shipping_fields', [$this, 'modify_shipping_fields'], 20, 1);
        add_filter('woocommerce_billing_fields', [$this, 'modify_billing_fields'], 20, 1);

        /**
         * 优化地址显示格式
         */
        add_filter('woocommerce_localisation_address_formats', [$this, 'modify_address_formats'], 10, 3);

        /**
         * 调整前端资源
         */
        add_filter('wp_enqueue_scripts', [$this, 'remove_select2'], 100);
        add_filter('wp_head', [$this, 'add_global_styles'], 100);
    }


    /**
     * 添加省市区关联选择字段
     */
    function add_dist_picker_fields($field, $key, $args, $value)
    {
        if ($args['id'] === 'billing_distpicker') {
            $field = '<div class="wccn-billing-distpicker">
            <p class="form-row address-field form-row-first wc-select validate-required validate-state">
                 <label for="wccn-state">省/直辖市/自治区*</label>
                 <select data-bind="billing-state" x-on:change="selectBillingState(billingAddress.state)" x-model="billingAddress.state" id="wccn-state"></select>
            </p>
            
            <p class="form-row address-field form-row-last wc-select validate-required validate-state">
                 <label for="wccn-city">城市 *</label>
                 <select data-bind="billing-city" x-model="billingAddress.city" id="wccn-city"></select>
            </p>
            
            <p class="form-row address-field form-row-addon wc-select validate-required validate-state">
                 <label for="wccn-area">区/县 *</label>
                 <select data-bind="billing-area" x-model="billingAddress.address_1"  id="wccn-area"></select>
            </p>
        </div>';
        }

        if ($args['id'] === 'shipping_distpicker') {
            $field = '<div class="wccn-shipping-distpicker">
            <p class="form-row address-field form-row-first wc-select validate-required validate-state">
                 <label for="wccn-state">省/直辖市/自治区*</label>
                 <select data-bind="shipping-state" x-on:change="selectShippingState(shippingAddress.state)" x-model="shippingAddress.state" id="wccn-state"></select>
            </p>
            
            <p class="form-row address-field form-row-last wc-select validate-required validate-state">
                 <label for="wccn-city">城市 *</label>
                 <select data-bind="shipping-city" x-model="shippingAddress.city" id="wccn-city"></select>
            </p>
            
            <p class="form-row address-field form-row-addon wc-select validate-required validate-state">
                 <label for="wccn-area">区/县 *</label>
                 <select data-bind="shipping-area" x-model="shippingAddress.address_1" id="wccn-area"></select>
            </p>
        </div>';
        }

        return $field;
    }


    /**
     * 修改默认字段
     */
    function modify_default_fields($fields)
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

        // 国家
        $fields[ 'country' ][ 'class' ][] = 'wccn-is-hidden';

        $fields[ 'distpicker' ][ 'priority' ] = 60;
        $fields[ 'distpicker' ][ 'type' ]     = 'text';

        // 省/直辖市/自治区
        $fields[ 'state' ][ 'label' ]   = '省份';
        $fields[ 'state' ][ 'type' ]    = 'text';
        $fields[ 'state' ][ 'class' ][] = 'form-row-first';
        $fields[ 'state' ][ 'class' ][] = 'wccn-is-hidden';
        unset($fields[ 'state' ][ 'class' ][ 0 ]);

        // 城市
        $fields[ 'city' ][ 'priority' ] = 81;
        $fields[ 'city' ][ 'label' ]    = '城市';
        $fields[ 'city' ][ 'class' ][]  = 'form-row-last wccn-is-hidden';

        // 区/县
        $fields[ 'address_1' ][ 'priority' ] = 82;
        $fields[ 'address_1' ][ 'label' ]    = '区/县';
        $fields[ 'address_1' ][ 'class' ][]  = 'form-row-addon wccn-is-hidden';

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
    }


    /**
     * 添加 x-model 到收货地址字段中
     */
    function modify_shipping_fields($fields): array
    {
        $fields[ 'shipping_state' ][ 'custom_attributes' ][ 'x-model' ]     = 'shippingAddress.state2';
        $fields[ 'shipping_state' ][ 'custom_attributes' ][ 'x-on:change' ] = 'selectShippingState2(shippingAddress.state2)';
        $fields[ 'shipping_state' ][ 'custom_attributes' ][ 'x-on:input.change' ] = 'console.log("test");';

        $fields[ 'shipping_city' ][ 'custom_attributes' ][ 'x-model' ]      = 'shippingAddress.city';

        $fields[ 'shipping_address_1' ][ 'custom_attributes' ][ 'x-model' ] = 'shippingAddress.address_1';

        return $fields;
    }


    /**
     * 添加 x-model 到账单地址字段中
     *
     * @param $fields
     *
     * @return array
     */
    function modify_billing_fields($fields): array
    {
        $fields[ 'billing_state' ][ 'custom_attributes' ][ 'x-model' ]     = 'billingAddress.state2';
        $fields[ 'billing_state' ][ 'custom_attributes' ][ 'x-on:change' ] = 'selectBillingState2(billingAddress.state2)';

        $fields[ 'billing_city' ][ 'custom_attributes' ][ 'x-model' ]      = 'billingAddress.city';

        $fields[ 'billing_address_1' ][ 'custom_attributes' ][ 'x-model' ] = 'billingAddress.address_1';

        return $fields;
    }


    function modify_address_formats($formats)
    {
        $formats[ 'CN' ] = "{state}{city}{address_1}{address_2}\n{company}\n{name}";

        return $formats;
    }


    function add_alpine_start_wrapper()
    {
        echo '<div x-data="wccnHandler()">';
    }

    function add_alpine_end_wrapper()
    {
        echo '</div>';
    }

    function remove_select2()
    {
        if (get_option('wccn_disable_select2', 'no') === 'yes') {
            if (class_exists('woocommerce')) {
                wp_dequeue_style('select2');
                wp_deregister_style('select2');

                wp_dequeue_script('selectWoo');
                wp_deregister_script('selectWoo');
            }
        }

        // 移除这两个JS防止省份选择数据丢失, 不能移除，wc-checkout 依赖这两个文件
        wp_deregister_script('wc-country-select');
        wp_deregister_script('wc-address-i18n');

	    /**
	     * 直接移除wc-country-select和wc-address-i18n会导致 checkout.js 无法加载，移除后，添加两个假的js以满足 checkout.js 的依赖
	     */
        wp_register_script('wc-country-select', Helpers::get_assets_url('/dist/scripts/main.js'), ['jquery'], WENPRISE_WC_CHINESIZE_VERSION, true);
        wp_register_script('wc-address-i18n', Helpers::get_assets_url('/dist/scripts/main.js'), ['jquery'], WENPRISE_WC_CHINESIZE_VERSION, true);
    }


    function add_global_styles()
    {
        echo "<style>
            .wccn-is-hidden {
                display: none !important;
            }
        </style>";
    }

}