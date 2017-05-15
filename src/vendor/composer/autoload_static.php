<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0cb4294e73f6519e73f693de6d02b776
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'SpotifyWebAPI\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'SpotifyWebAPI\\' => 
        array (
            0 => __DIR__ . '/..' . '/jwilsson/spotify-web-api-php/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit0cb4294e73f6519e73f693de6d02b776::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit0cb4294e73f6519e73f693de6d02b776::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
