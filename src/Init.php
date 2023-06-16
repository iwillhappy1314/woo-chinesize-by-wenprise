<?php

namespace WooChinesize;


class Init
{

    /**
     * constructor.
     */
    public function __construct()
    {
        $classes = [
            Integrate::class,
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