<?php


/**
 * 优化地址
 */
add_filter('woocommerce_default_address_fields', function ($fields)
{
    $user_id = get_current_user_id();

    $state_code = get_user_meta($user_id, 'billing_state', true);
    if ( ! $state_code) {
        $state_code = 'CN2';
    }

    $cities = \WooChinesize\Helper::get_state_cities($state_code);
    $cities = wp_list_pluck($cities, 'name', 'name');

    if (get_option('wccn_remove_company_fields') === 'yes') {
        unset($fields[ 'company' ]);
    }

    if (get_option('wccn_remove_post_fields') === 'yes') {
        unset($fields[ 'postcode' ]);
    }

    $fields[ 'first_name' ][ 'class' ][ 0 ] = 'form-row-wide';
    $fields[ 'first_name' ][ 'label' ]      = '收货人';

    $fields[ 'address_1' ][ 'priority' ] = 82;
    $fields[ 'address_1' ][ 'label' ]    = '详细地址';

    $fields[ 'country' ][ 'class' ][] = 'is-hidden';

    $fields[ 'state' ][ 'label' ]   = '省份';
    $fields[ 'state' ][ 'class' ][] = 'form-row-first';
    $fields[ 'state' ][ 'class' ][] = 'wc-select';
    unset($fields[ 'state' ][ 'class' ][ 0 ]);

    $fields[ 'city' ][ 'priority' ] = 81;
    $fields[ 'city' ][ 'label' ]    = '城市';
    $fields[ 'city' ][ 'type' ]     = 'select';
    $fields[ 'city' ][ 'class' ][]  = 'form-row-last';
    $fields[ 'city' ][ 'class' ][]  = 'wc-select';
    $fields[ 'city' ][ 'options' ]  = $cities;

    unset($fields[ 'city' ][ 'class' ][ 0 ]);

    unset($fields[ 'last_name' ]);
    unset($fields[ 'address_2' ]);

    return $fields;
}, 20, 1);


add_action('wp_head', function ()
{
    echo "<style type='text/css'>
                .is-hidden {
                    display: none !important;
                }
            </style>";
}
);