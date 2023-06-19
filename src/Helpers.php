<?php

namespace WooChinesize;

class Helpers {
	/**
	 * 获取城市数据
	 *
	 * @return array|mixed|object
	 */
	public static function get_location_data() {
		$data = file_get_contents( WENPRISE_WC_CHINESIZE_PATH . 'assets/scripts/city-code.json' );

		return json_decode( $data )->areas;
	}


	/**
	 * 获取资源 URL
	 *
	 * @param string $path 资源组名称
	 * @param string $manifest_directory
	 *
	 * @return string
	 */
	public static function get_assets_url( string $path, string $manifest_directory = WENPRISE_WC_CHINESIZE_PATH ): string {
		static $manifest;
		static $manifest_path;

		if ( ! $manifest_path ) {
			$manifest_path = $manifest_directory . 'frontend/mix-manifest.json';
		}

		if ( ! $manifest ) {
			// @codingStandardsIgnoreLine
			$manifest = json_decode( file_get_contents( $manifest_path ), true );
		}

		// Remove manifest directory from path
		$path = str_replace( $manifest_directory, '', $path );
		// Make sure there’s a leading slash
		$path = '/' . ltrim( $path, '/' );

		// Get file URL from manifest file
		$path = $manifest[ $path ];
		// Make sure there’s no leading slash
		$path = ltrim( $path, '/' );

		return WENPRISE_WC_CHINESIZE_URL . 'frontend/' . $path;
	}


	/**
	 * 获取指定值的默认值
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public static function value( $value ) {
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
	public static function data_get( array $array, string $key = null, $default = null ) {
		if ( is_null( $key ) ) {
			return $array;
		}

		if ( isset( $array[ $key ] ) ) {
			return $array[ $key ];
		}

		foreach ( explode( '.', $key ) as $segment ) {
			if ( ! is_array( $array ) || ! array_key_exists( $segment, $array ) ) {
				return static::value( $default );
			}

			$array = $array[ $segment ];
		}

		return $array;
	}

}

