<?php

namespace WooChinesize;

class Frontend
{

    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }


    public function enqueue_scripts()
    {

        if (is_checkout() || is_account_page()) {
            wp_enqueue_style('woo-chinesize-by-wenprise', Helpers::get_assets_url('/dist/styles/styles.css'));
            wp_enqueue_script('woo-chinesize-by-wenprise', Helpers::get_assets_url('/dist/scripts/main.js'), ['jquery'], '1.0.0', true);
        }

    }

}
