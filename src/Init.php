<?php

namespace WooChinesize;


use WooChinesize\Integrate\AddressIntegrate;
use WooChinesize\Integrate\OrderListIntegrate;

class Init
{

    /**
     * constructor.
     */
    public function __construct()
    {
        $classes = [
            AddressIntegrate::class,
            OrderListIntegrate::class,
            Frontend::class
        ];

        foreach ($classes as $class) {
            new $class;
        }

        add_filter('woocommerce_get_settings_pages', function ($settings)
        {
            $settings[] = new Settings();

            return $settings;
        });
    }

}