<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd5b0e0555607608a3cef550f9d14d71a
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Carbon_Fields\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Carbon_Fields\\' => 
        array (
            0 => __DIR__ . '/..' . '/htmlburger/carbon-fields/core',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd5b0e0555607608a3cef550f9d14d71a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd5b0e0555607608a3cef550f9d14d71a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitd5b0e0555607608a3cef550f9d14d71a::$classMap;

        }, null, ClassLoader::class);
    }
}
