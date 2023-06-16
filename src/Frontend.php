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
            wp_enqueue_style('woo-chinesize-style', Helpers::get_assets_url('/dist/styles/styles.css'));

            wp_enqueue_script('woo-chinesize-main', Helpers::get_assets_url('/dist/scripts/main.js'), ['jquery'], WENPRISE_WC_CHINESIZE_VERSION, true);
        }

    }

}
