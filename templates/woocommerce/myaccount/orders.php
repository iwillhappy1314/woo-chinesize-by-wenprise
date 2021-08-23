<?php
/**
 * Orders
 *
 * Shows orders on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/orders.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.7.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_account_orders', $has_orders); ?>

<?php if ($has_orders) : ?>

    <div class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">

        <div>
            <?php
            foreach ($customer_orders->orders as $customer_order) {
                $order      = wc_get_order($customer_order); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                $item_count = $order->get_item_count() - $order->get_item_count_refunded();
                ?>

                <div class="wccn-order-row">
                    <div class="wccn-flex wccn-order">
                        <div class="wccn-mr-6 wccn-order__date">
                            <time datetime="<?php echo esc_attr($order->get_date_created()->date('c')); ?>">
                                <?php echo esc_html(wc_format_datetime($order->get_date_created())); ?>
                            </time>
                        </div>
                        <div class="wccn-mr-6 wccn-order__number">
                            <?= __('Order Number', 'wc-chinesize'); ?>
                            <a href="<?php echo esc_url($order->get_view_order_url()); ?>">
                                <?php echo esc_html(_x('#', 'hash before order number', 'woocommerce') . $order->get_order_number()); ?>
                            </a>
                        </div>

                        <div class="wccn-order__status wccn-order--<?= $order->get_status(); ?>">
                            <?php echo esc_html(wc_get_order_status_name($order->get_status())); ?>
                        </div>
                    </div>

                    <div class='wccn-flex wccn-order__items wccn-sm-flex-wrap'>

                        <div class="wccn-flex-grow wccn-sm-full">
                            <?php
                            $items            = $order->get_items();
                            foreach ($items as $item):
                                $item_data = $item->get_data();
                                $product_name = $item_data[ 'name' ];
                                $product_id   = $item_data[ 'product_id' ];
                                $variation_id = $item_data[ 'variation_id' ];
                                $quantity     = $item_data[ 'quantity' ];
                                $subtotal     = $item_data[ 'subtotal' ];
                                $total        = $item_data[ 'total' ];

                                $product = wc_get_product($product_id);
                                ?>
                                <div class='wccn-flex wccn-flex-grow wccn-item'>

                                    <div class='wccn-item__image'>
                                        <a target=_blank href="<?= get_permalink($product_id); ?>">
                                            <?= wp_get_attachment_image(get_post_thumbnail_id($product_id), [64, 64]); ?>
                                        </a>
                                    </div>

                                    <div class="wccn-item__name">
                                        <a target=_blank href="<?= get_permalink($product_id); ?>">
                                            <?= $product_name; ?>
                                        </a>

                                        <?php if ($variation_id) : ?>
                                            <?php wc_display_item_meta($item); ?>
                                        <?php endif; ?>
                                    </div>

                                    <div class="wccn-item__quantity">
                                        <?= ($product && $product->get_price()) ? get_woocommerce_currency_symbol() . $product->get_price() : '' ?> x <?= $quantity; ?>
                                    </div>

                                    <!--                                    <div class='wccn-item__total'>-->
                                    <!--                                        --><? //= $total;
                                    ?>
                                    <!--                                    </div>-->
                                </div>
                            <?php endforeach; ?>
                        </div>


                        <?php foreach (wc_get_account_orders_columns() as $column_id => $column_name) : ?>
                            <?php if (has_action('woocommerce_my_account_my_orders_column_' . $column_id)) : ?>
                                <div class="wccn-order__cell wccn-order__cell-<?php echo esc_attr($column_id); ?>" data-title="<?php echo esc_attr($column_name); ?>">
                                    <?php do_action('woocommerce_my_account_my_orders_column_' . $column_id, $order); ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>


                        <div class='wccn-order__cell wccn-order__total'>
                            <span class="wccn-hide wccn-sm-inline-block"><?= __('Total', 'wc-chinesize'); ?></span>
                            <?= get_woocommerce_currency_symbol(get_option('woocommerce_currency')); ?> <?= $order->get_total(); ?>
                        </div>

                        <div class='wccn-order__cell wccn-order__action wccn-sm-justify-end wccn-flex wccn-flex-col wccn-sm-flex-row'>
                            <?php
                            $actions = wc_get_account_orders_actions($order);

                            if ( ! empty($actions)) {
                                foreach ($actions as $key => $action) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                                    echo '<a href="' . esc_url($action[ 'url' ]) . '" class="woocommerce-button ' . sanitize_html_class($key) . '">' . esc_html($action[ 'name' ]) . '</a>';
                                }
                            }
                            ?>
                        </div>

                    </div>

                </div>

            <?php } ?>


        </div>
    </div>

    <?php do_action('woocommerce_before_account_orders_pagination'); ?>

    <?php if (1 < $customer_orders->max_num_pages) : ?>
        <div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
            <?php if (1 !== $current_page) : ?>
                <a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url(wc_get_endpoint_url('orders', $current_page - 1)); ?>">
                    <?php esc_html_e('Previous', 'woocommerce'); ?>
                </a>
            <?php endif; ?>

            <?php if (intval($customer_orders->max_num_pages) !== $current_page) : ?>
                <a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url(wc_get_endpoint_url('orders', $current_page + 1)); ?>">
                    <?php esc_html_e('Next', 'woocommerce'); ?>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

<?php else : ?>
    <div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
        <a class="woocommerce-Button button" href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>">
            <?php esc_html_e('Browse products', 'woocommerce'); ?>
        </a>
        <?php esc_html_e('No order has been made yet.', 'woocommerce'); ?>
    </div>
<?php endif; ?>

<?php do_action('woocommerce_after_account_orders', $has_orders); ?>
