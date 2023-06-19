<?php

namespace WooChinesize;

class Helpers
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
     * 获取资源 URL
     *
     * @param string $path 资源组名称
     * @param string $manifest_directory
     *
     * @return string
     */
    public static function get_assets_url(string $path, string $manifest_directory = WENPRISE_WC_CHINESIZE_PATH): string
    {
        static $manifest;
        static $manifest_path;

        if ( ! $manifest_path) {
            $manifest_path = $manifest_directory . 'frontend/mix-manifest.json';
        }

        if ( ! $manifest) {
            // @codingStandardsIgnoreLine
            $manifest = json_decode(file_get_contents($manifest_path), true);
        }

        // Remove manifest directory from path
        $path = str_replace($manifest_directory, '', $path);
        // Make sure there’s a leading slash
        $path = '/' . ltrim($path, '/');

        // Get file URL from manifest file
        $path = $manifest[ $path ];
        // Make sure there’s no leading slash
        $path = ltrim($path, '/');

        return WENPRISE_WC_CHINESIZE_URL . 'frontend/' . $path;
    }


    /**
     * 转换 WooCommerce 省份区码为实际区码
     *
     * @param null $wc_code
     *
     * @return array|int
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
     * @return array
     */
    public static function get_state_cities($state_code): array
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
    public static function get_city_areas($state_code, $city_name): array
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


    /**
     * 获取指定值的默认值
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public static function value($value)
    {
        return $value instanceof \Closure ? $value() : $value;
    }


    /**
     * 使用点注释获取数据
     *
     * @param array       $array
     * @param string|null $key
     * @param mixed       $default
     *
     * @return mixed
     */
    public static function data_get(array $array, string $key = null, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }

        if (isset($array[ $key ])) {
            return $array[ $key ];
        }

        foreach (explode('.', $key) as $segment) {
            if ( ! is_array($array) || ! array_key_exists($segment, $array)) {
                return static::value($default);
            }

            $array = $array[ $segment ];
        }

        return $array;
    }

}

