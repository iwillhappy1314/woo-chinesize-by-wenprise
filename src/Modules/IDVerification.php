<?php

namespace WooChinesize\Modules;

class IDVerification
{

    /**
     * constructor.
     */
    public function __construct()
    {

        add_filter('woocommerce_get_sections_chinesize', [$this, 'settings_tab']);


        add_filter('woocommerce_get_settings_chinesize', [$this, 'settings_fields'], 10, 2);


        add_action('woocommerce_before_order_notes', [$this, 'additional_field']);

        /**
         * 提交时验证身份证号码
         */
        add_action('woocommerce_checkout_process', [$this, 'process_addition_fields']);
    }


    /**
     * 附加字段
     *
     * @param $checkout
     */
    function additional_field($checkout)
    {
        woocommerce_form_field('wciv-licence-type', [
            'type'     => 'select',
            'class'    => ['form-row-wide'],
            'label'    => '证件类型',
            'required' => true,
            'options'  => [
                'id_card'  => '身份证',
                'passport' => '护照',
            ],
            'default'  => 'id_card',
        ], $checkout->get_value('wciv-real-name'));

        woocommerce_form_field('wciv-real-name', [
            'type'        => 'text',
            'class'       => ['form-row-wide'],
            'label'       => '真实姓名',
            'placeholder' => '请输入真实姓名',
            'required'    => true,
            'default'     => '',
        ], $checkout->get_value('wciv-real-name'));

        woocommerce_form_field('wciv-id-number', [
            'type'        => 'text',
            'class'       => ['form-row-wide'],
            'label'       => '证件号码',
            'placeholder' => '准确的证件号码',
            'required'    => true,
            'default'     => '',
        ], $checkout->get_value('wciv-id-number'));
    }


    /**
     * 验证字段数据
     */
    function process_addition_fields()
    {
        $real_name    = $_POST[ 'wciv-real-name' ] ?? false;
        $id_number    = $_POST[ 'wciv-id-number' ] ?? false;
        $licence_type = $_POST[ 'wciv-licence-type' ] ?? false;

        if ( ! $real_name) {
            wc_add_notice('请输入真实姓名', 'error');
        }

        if ( ! $id_number) {
            wc_add_notice('请输入证件号码', 'error');
        }

        if ($licence_type === 'id_card' && ! $this->validate_id_number($id_number)) {
            wc_add_notice('请输入正确的证件号码', 'error');
        }

        if ($licence_type === 'id_card' && ! $this->check_id_authenticity($real_name, $id_number)) {
            wc_add_notice('实名验证不通过，请检查。', 'error');
        }
    }


    /**
     * 验证身份证号码的有效性
     *
     * @param $number
     *
     * @return bool
     */
    function validate_id_number($number)
    {

        // 只能是18位
        if (strlen($number) != 18) {
            return false;
        }

        // 取出本体码
        $number_base = substr($number, 0, 17);

        // 取出校验码
        $verify_code = substr($number, 17, 1);

        // 加权因子
        $factor = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];

        // 校验码对应值
        $verify_code_list = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];

        // 根据前17位计算校验码
        $total = 0;
        for ($i = 0; $i < 17; $i++) {
            $total += substr($number_base, $i, 1) * $factor[ $i ];
        }

        // 取模
        $mod = $total % 11;

        return $verify_code == $verify_code_list[ $mod ];
    }


    /**
     * 验证身份证号码真实性
     *
     * @param $name
     * @param $number
     *
     * @return bool|string
     */
    function check_id_authenticity($name, $number)
    {

        $url        = 'http://checkone.market.alicloudapi.com/chinadatapay/1882';
        $app_key    = '203961880';
        $app_secret = 'lAgRl0IYwcV8Xmmlg5IFjEvNYp7R26Bo';
        $app_code   = '2fa903b84f7f4b2d9e0ab516ff28c37e';

        $response = wp_remote_post($url, [
            'headers' => [
                'content-type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                'Authorization:APPCODE ' . $app_code,
            ],
            'body'    => [
                'key'    => $app_key,
                'name'   => $name,
                'idcard' => $number,
            ],
        ]);

        dd($response);

        update_option('xxxx', $response);

        if ( ! is_wp_error($response)) {
            $result = json_decode(wp_remote_retrieve_body($response));

            update_option('xxxx', $result);

            return (int)$result->data->result === 1;
        } else {
            return $response->get_error_message();
        }

    }


    function settings_tab($sections)
    {
        $sections[ 'id_verification' ] = __('身份认证', 'text-domain');

        return $sections;
    }


    function settings_fields($settings, $current_section)
    {
        if ($current_section == 'id_verification') {
            $settings_slider = [];

            $settings_slider[] = [
                'id'   => 'id_verification',
                'name' => __('启用实名认证', 'text-domain'),
                'type' => 'title',
                'desc' => __('启用公安联网实名认证。', 'text-domain'),
            ];

            $settings_slider[] = [
                'name' => __('启用实名认证', 'text-domain'),
                'id'   => 'id_verification',
                'type' => 'checkbox',
                'css'  => 'min-width:300px;',
                'desc' => __('启用公安联网实名认证', 'text-domain'),
            ];

            $settings_slider[] = [
                'name' => __('Slider Title', 'text-domain'),
                'id'   => 'wcslider_title',
                'type' => 'text',
                'desc' => __('Any title you want can be added to your slider with this option!', 'text-domain'),
            ];

            $settings_slider[] = [
                'type' => 'sectionend',
                'id'   => 'id_verification',
            ];

            return $settings_slider;

        } else {
            return $settings;
        }
    }

}