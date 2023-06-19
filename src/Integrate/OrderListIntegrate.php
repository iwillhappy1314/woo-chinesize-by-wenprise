<?php

namespace WooChinesize\Integrate;


class OrderListIntegrate
{

    public function __construct()
    {

        add_filter('woocommerce_locate_template', [$this, 'locate_template'], 1, 3);
        add_filter('woocommerce_order_item_name', [$this, 'add_order_item_image'], 10, 3);
        add_filter('woocommerce_before_account_orders', [$this, 'add_status_filter'], 10, 3);

        add_filter('woocommerce_order_query_args', [$this, 'modify_query_args']);

        add_filter('woocommerce_order_item_class', [$this, 'append_item_class'], 12, 3);

    }


    /**
     * 加载插件中自定义的WooCommerce模板
     *
     * @param $template
     * @param $template_name
     * @param $template_path
     *
     * @return mixed|string
     */
    function locate_template($template, $template_name, $template_path)
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
    }


    /**
     * 添加产品图像到订单列表中
     *
     * @param $html
     * @param $item
     * @param $is_visible
     *
     * @return mixed|string
     */
    function add_order_item_image($html, $item, $is_visible)
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

    }

    /**
     * 添加订单状态选择到订单列表中
     *
     * @return false|void
     */
    function add_status_filter()
    {
        if (get_option('wccn_order_filter_enabled') !== 'yes') {
            return false;
        }

        $order_status   = wc_get_order_statuses();
        $allowed_status = (array)get_option('wccn_order_status_allowed_to_filter', array_keys(wc_get_order_statuses()));

        $status = $_GET[ 'wccn-status' ] ?? false;

        $html = '<div class="wccn-order__filter">';

        if ( ! $status) {
            $html .= '<a class="wccn-order__filter-active" href="' . remove_query_arg('wccn-status') . '">' . __('All', 'wc-chinesize') . '</a>';
        } else {
            $html .= '<a href="' . remove_query_arg('wccn-status') . '">' . __('All', 'wc-chinesize') . '</a>';
        }

        foreach ($order_status as $key => $name) {
            $status_slug = substr($key, 3, strlen($key) - 3);

            $args = [
                'number posts' => -1,
                'meta_key'     => '_customer_user',
                'meta_value'   => get_current_user_id(),
                'post_type'    => wc_get_order_types('view-orders'),
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
    }


    function modify_query_args($args)
    {
        $status = $_GET[ 'wccn-status' ] ?? false;

        if ($status) {
            $args[ 'status' ] = [$status];
        }

        return $args;
    }


    function append_item_class($class, $item, $order): string
    {
        return $class . ' wccn-order-detail-item';
    }
}