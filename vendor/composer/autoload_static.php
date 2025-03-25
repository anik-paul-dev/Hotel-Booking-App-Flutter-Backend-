<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit63b8755fc07f68cd1aa5c07d29e22bf9
{
    public static $prefixLengthsPsr4 = array (
        'p' => 
        array (
            'paytm\\paytmchecksum\\' => 20,
        ),
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'paytm\\paytmchecksum\\' => 
        array (
            0 => __DIR__ . '/..' . '/paytm/paytmchecksum/paytmchecksum',
        ),
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit63b8755fc07f68cd1aa5c07d29e22bf9::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit63b8755fc07f68cd1aa5c07d29e22bf9::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit63b8755fc07f68cd1aa5c07d29e22bf9::$classMap;

        }, null, ClassLoader::class);
    }
}
