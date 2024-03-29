<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit592708b336f4957abc3ace0ea9594658
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'WooChinesize\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'WooChinesize\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit592708b336f4957abc3ace0ea9594658::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit592708b336f4957abc3ace0ea9594658::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit592708b336f4957abc3ace0ea9594658::$classMap;

        }, null, ClassLoader::class);
    }
}
