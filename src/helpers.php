<?php

namespace WooChinesize;

class Helper
{
    /**
     * 获取城市数据
     *
     * @return array|mixed|object
     */
    public static function get_location_data()
    {
        $data = file_get_contents(WENPRISE_WC_CHINESIZE_PATH . 'assets/scripts/city-code.json');

        return json_decode($data)->areas;
    }


    /**
     * 转换 WooCommerce 省份区码为实际区码
     *
     * @param null $wc_code
     *
     * @return array|mixed
     */
    public static function city_code_convert($wc_code = null)
    {

        $map = [
            'CN1'  => 530000,
            'CN2'  => 110000,
            'CN3'  => 120000,
            'CN4'  => 130000,
            'CN5'  => 140000,
            'CN6'  => 150000,
            'CN7'  => 210000,
            'CN8'  => 220200,
            'CN9'  => 230000,
            'CN10' => 310000,
            'CN11' => 320000,
            'CN12' => 330000,
            'CN13' => 340000,
            'CN14' => 350000,
            'CN15' => 360000,
            'CN16' => 370000,
            'CN17' => 410000,
            'CN18' => 420000,
            'CN19' => 430000,
            'CN20' => 440000,
            'CN21' => 450000,
            'CN22' => 460000,
            'CN23' => 500000,
            'CN24' => 510000,
            'CN25' => 520000,
            'CN26' => 610000,
            'CN27' => 620000,
            'CN28' => 630000,
            'CN29' => 640000,
            'CN30' => 820000,
            'CN31' => 540000,
            'CN32' => 650000,
        ];

        if ($wc_code) {
            return $map[ $wc_code ];
        }

        return $map;

    }


    /**
     * 获取省份中的城市数据
     *
     * @param $state_code
     *
     * @return array|mixed
     */
    public static function get_state_cities($state_code)
    {
        $location_data = self::get_location_data();
        $cites         = [];

        foreach ($location_data as $city) {
            if ($city->id == $state_code) {
                $cites = $city->children;
            }
        }

        if (empty($cites)) {
            $cites = $location_data[ 0 ]->children;
        }

        return $cites;
    }


    /**
     * 获取城市对应的区/县
     *
     * @param $state_code
     * @param $city_name
     *
     * @return array
     */
    public static function get_city_areas($state_code, $city_name)
    {
        $cites = self::get_state_cities($state_code);
        $areas = [];

        foreach ($cites as $city) {
            if ($city->name == $city_name) {
                $areas = $city->children;
            }
        }

        if (empty($areas)) {
            $areas = $cites[ 0 ]->children;
        }

        return $areas;
    }

}

