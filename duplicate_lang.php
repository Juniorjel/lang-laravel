<?php

use Illuminate\Support\Str;

// Bootstrap the Laravel application
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sourceLang = 'en'; // The source language directory
$targetLang = 'np'; // The target language directory

$sourcePath = resource_path("lang/{$sourceLang}");
$targetPath = resource_path("lang/{$targetLang}");

if (!is_dir($sourcePath)) {
    exit("Source language directory does not exist.\n");
}

if (!is_dir($targetPath)) {
    mkdir($targetPath, 0755, true);
}

$files = scandir($sourcePath);

foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        $filePath = "{$sourcePath}/{$file}";
        if (is_file($filePath)) {
            $translations = include $filePath;
            $emptyTranslations = array_map(function($value) {
                return is_array($value) ? array_map(function() { return ''; }, $value) : '';
            }, $translations);

            $targetFilePath = "{$targetPath}/{$file}";
            file_put_contents($targetFilePath, "<?php\n\nreturn " . var_export($emptyTranslations, true) . ";\n");
        }
    }
}

echo "Language files duplicated and values set to empty in '{$targetLang}' directory.\n";
