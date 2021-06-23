<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5fd60179f05628c12cd299ca8181536d
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Palasthotel\\WordPress\\MigrateToGutenberg\\' => 41,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Palasthotel\\WordPress\\MigrateToGutenberg\\' => 
        array (
            0 => __DIR__ . '/../..' . '/classes',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5fd60179f05628c12cd299ca8181536d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5fd60179f05628c12cd299ca8181536d::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit5fd60179f05628c12cd299ca8181536d::$classMap;

        }, null, ClassLoader::class);
    }
}
