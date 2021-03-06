<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit647ff11bd6acc80605e2fa5d32827a3b
{
    public static $prefixLengthsPsr4 = array (
        'g' => 
        array (
            'grandt\\ResizeGif\\Structure\\' => 27,
            'grandt\\ResizeGif\\Files\\' => 23,
            'grandt\\ResizeGif\\Debug\\' => 23,
            'grandt\\ResizeGif\\' => 17,
        ),
        'Z' => 
        array (
            'ZipMerge\\' => 9,
        ),
        'P' => 
        array (
            'PHPePub\\' => 8,
            'PHPZip\\Zip\\' => 11,
        ),
        'M' => 
        array (
            'Masterminds\\' => 12,
        ),
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'grandt\\ResizeGif\\Structure\\' => 
        array (
            0 => __DIR__ . '/..' . '/grandt/phpresizegif/src/ResizeGif/Structure',
        ),
        'grandt\\ResizeGif\\Files\\' => 
        array (
            0 => __DIR__ . '/..' . '/grandt/phpresizegif/src/ResizeGif/Files',
        ),
        'grandt\\ResizeGif\\Debug\\' => 
        array (
            0 => __DIR__ . '/..' . '/grandt/phpresizegif/src/ResizeGif/Debug',
        ),
        'grandt\\ResizeGif\\' => 
        array (
            0 => __DIR__ . '/..' . '/grandt/phpresizegif/src/ResizeGif',
        ),
        'ZipMerge\\' => 
        array (
            0 => __DIR__ . '/..' . '/grandt/phpzipmerge/src/ZipMerge',
        ),
        'PHPePub\\' => 
        array (
            0 => __DIR__ . '/..' . '/grandt/phpepub/src/PHPePub',
        ),
        'PHPZip\\Zip\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpzip/phpzip/src/Zip',
        ),
        'Masterminds\\' => 
        array (
            0 => __DIR__ . '/..' . '/masterminds/html5/src',
        ),
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
    );

    public static $prefixesPsr0 = array (
        'S' => 
        array (
            'SimplePie' => 
            array (
                0 => __DIR__ . '/..' . '/simplepie/simplepie/library',
            ),
        ),
    );

    public static $classMap = array (
        'RelativePath' => __DIR__ . '/..' . '/grandt/relativepath/RelativePath.php',
        'UUID' => __DIR__ . '/..' . '/grandt/phpepub/src/lib.uuid.php',
        'UUIDException' => __DIR__ . '/..' . '/grandt/phpepub/src/lib.uuid.php',
        'UUIDStorage' => __DIR__ . '/..' . '/grandt/phpepub/src/lib.uuid.php',
        'UUIDStorageException' => __DIR__ . '/..' . '/grandt/phpepub/src/lib.uuid.php',
        'UUIDStorageStable' => __DIR__ . '/..' . '/grandt/phpepub/src/lib.uuid.php',
        'UUIDStorageVolatile' => __DIR__ . '/..' . '/grandt/phpepub/src/lib.uuid.php',
        'WP_Async_Request' => __DIR__ . '/..' . '/a5hleyrich/wp-background-processing/classes/wp-async-request.php',
        'WP_Background_Process' => __DIR__ . '/..' . '/a5hleyrich/wp-background-processing/classes/wp-background-process.php',
        'com\\grandt\\BinString' => __DIR__ . '/..' . '/grandt/binstring/BinString.php',
        'com\\grandt\\BinStringStatic' => __DIR__ . '/..' . '/grandt/binstring/BinStringStatic.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit647ff11bd6acc80605e2fa5d32827a3b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit647ff11bd6acc80605e2fa5d32827a3b::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit647ff11bd6acc80605e2fa5d32827a3b::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit647ff11bd6acc80605e2fa5d32827a3b::$classMap;

        }, null, ClassLoader::class);
    }
}
