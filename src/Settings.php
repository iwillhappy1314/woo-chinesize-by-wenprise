<?php

namespace WooChinesize;
/**
 * WC_Settings_Products.
 */
class Settings extends \WC_Settings_Page
{

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->id    = 'chinesize';
        $this->label = __('Chinesize', 'wc-chinesize');

        parent::__construct();
    }

    /**
     * Get sections.
     *
     * @return array
     */
    public function get_sections(): array
    {
        $sections = [
            ''                => __('Templates', 'wc-chinesize'),
            'checkout_fields' => __('Checkout Fields', 'wc-chinesize'),
        ];

        return apply_filters('woocommerce_get_sections_' . $this->id, $sections);
    }

    /**
     * Output the settings.
     */
    public function output()
    {
        global $current_section;

        $settings = $this->get_settings($current_section);

        \WC_Admin_Settings::output_fields($settings);
    }

    /**
     * Save settings.
     */
    public function save()
    {
        global $current_section;

        $settings = $this->get_settings($current_section);
        \WC_Admin_Settings::save_fields($settings);

        if ($current_section) {
            do_action('woocommerce_update_options_' . $this->id . '_' . $current_section);
        }
    }

    /**
     * Get settings array.
     *
     * @param string $current_section Current section name.
     *
     * @return array
     */
    public function get_settings(string $current_section = ''): array
    {
        if ('checkout_fields' === $current_section) {

            $settings = apply_filters(
                'wc_chinesize_checkout_fields_settings',
                [
                    [
                        'title' => __('Checkout Fields', 'wc-chinesize'),
                        'type'  => 'title',
                        'id'    => 'checkout_fields_options',
                    ],

                    [
                        'title'   => __('Chinesized address', 'wc-chinesize'),
                        'desc'    => __('Enable chinesized address fields', 'wc-chinesize'),
                        'id'      => 'wccn_chinesized_address_field_enabled',
                        'default' => 'yes',
                        'type'    => 'checkbox',
                    ],

                    [
                        'title'    => __('Remove company fields', 'wc-chinesize'),
                        'desc'     => __('Remove company fields', 'wc-chinesize'),
                        'id'       => 'wccn_remove_company_fields',
                        'type'     => 'checkbox',
                        'default'  => 'no',
                        'autoload' => false,
                    ],

                    [
                        'title'    => __('Remove postcode fields', 'wc-chinesize'),
                        'desc'     => __('Remove postcode fields', 'wc-chinesize'),
                        'id'       => 'wccn_remove_post_fields',
                        'type'     => 'checkbox',
                        'default'  => 'no',
                        'autoload' => false,
                    ],

                    [
                        'title'    => __('Disable Select2', 'wc-chinesize'),
                        'desc'     => __('Disable Select2 built in WooCommerce', 'wc-chinesize'),
                        'id'       => 'wccn_disable_select2',
                        'type'     => 'checkbox',
                        'default'  => 'no',
                        'autoload' => false,
                    ],

                    [
                        'type' => 'sectionend',
                        'id'   => 'digital_download_options',
                    ],

                ]
            );

        } else {

            $settings = apply_filters(
                'wc_chinesize_templates_settings',
                [
                    [
                        'title' => __('Orders Filter', 'wc-chinesize'),
                        'type'  => 'title',
                        'desc'  => '',
                        'id'    => 'wccn_order_filter_options',
                    ],

                    [
                        'title'   => __('Order filter by status', 'wc-chinesize'),
                        'desc'    => __('Enable order filter function in order list page.', 'wc-chinesize'),
                        'id'      => 'wccn_order_filter_enabled',
                        'default' => 'yes',
                        'type'    => 'checkbox',
                    ],

                    [
                        'title'             => __('Allowed order status', 'wc-chinesize'),
                        'desc'              => __('The order status allowed to filter.', 'wc-chinesize'),
                        'id'                => 'wccn_order_status_allowed_to_filter',
                        'default'           => array_keys(wc_get_order_statuses()),
                        'type'              => 'multiselect',
                        'options'           => wc_get_order_statuses(),
                        'custom_attributes' => [
                            'multiple' => 'multiple',
                        ],
                    ],

                    [
                        'type' => 'sectionend',
                        'id'   => 'catalog_options',
                    ],

                    [
                        'title' => __('Use custom template', 'wc-chinesize'),
                        'type'  => 'title',
                        'desc'  => '',
                        'id'    => 'wccn_order_list_template',
                    ],

                    [
                        'title'   => __('Order list template', 'wc-chinesize'),
                        'desc'    => __('Use optimized template for order list page', 'wc-chinesize'),
                        'id'      => 'wccn_order_list_template_enabled',
                        'default' => 'yes',
                        'type'    => 'checkbox',
                    ],

                    [
                        'title'   => __('Order detail template', 'wc-chinesize'),
                        'desc'    => __('Use optimized template for order detail page', 'wc-chinesize'),
                        'id'      => 'wccn_order_detail_template_enabled',
                        'default' => 'yes',
                        'type'    => 'checkbox',
                    ],

                    [
                        'type' => 'sectionend',
                        'id'   => 'product_inventory_options',
                    ],

                ]
            );

        }

        return apply_filters('woocommerce_get_settings_' . $this->id, $settings, $current_section);
    }
}