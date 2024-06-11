<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class TranslationController extends Controller
{
    protected $sourceLang = 'en';
    protected $targetLang = 'np';
    protected $langPath = 'lang';

    public function index(Request $request)
    {
        $files = $this->getTranslationFiles();
        $selectedFile = $request->query('file');

        if ($selectedFile) {
            $sourceTranslations = include base_path("{$this->langPath}/{$this->sourceLang}/{$selectedFile}");
            $targetTranslations = include base_path("{$this->langPath}/{$this->targetLang}/{$selectedFile}");
        } else {
            $sourceTranslations = $targetTranslations = [];
        }

        // Flatten the translation keys
        $flattenedSourceTranslations = $this->flattenTranslations($sourceTranslations);
        $flattenedTargetTranslations = $this->flattenTranslations($targetTranslations);

        return view('translations.index', compact('files', 'selectedFile', 'flattenedSourceTranslations', 'flattenedTargetTranslations'));
    }

    public function update(Request $request)
    {
        $file = $request->input('file');
        $translations = $request->input('translations');

        $sourceFilePath = base_path("{$this->langPath}/{$this->sourceLang}/{$file}");
        $targetFilePath = base_path("{$this->langPath}/{$this->targetLang}/{$file}");

        $sourceTranslations = include $sourceFilePath;
        $targetTranslations = include $targetFilePath;

        foreach ($translations as $key => $translation) {
            // Reconstruct nested keys
            $nestedKey = explode('.', $key);
            $sourceTranslations = Arr::set($sourceTranslations, $nestedKey, $translation['en']);
            $targetTranslations = Arr::set($targetTranslations, $nestedKey, $translation['np']);
        }

        file_put_contents($sourceFilePath, "<?php\n\nreturn " . var_export($sourceTranslations, true) . ";\n");
        file_put_contents($targetFilePath, "<?php\n\nreturn " . var_export($targetTranslations, true) . ";\n");

        return redirect()->route('translations.index')->with('success', 'Translations updated successfully.');
    }

    protected function getTranslationFiles()
    {
        $sourcePath = base_path("{$this->langPath}/{$this->sourceLang}");
        return array_diff(scandir($sourcePath), ['..', '.']);
    }

    protected function flattenTranslations($translations, $prefix = '')
    {
        $flattenedTranslations = [];

        foreach ($translations as $key => $value) {
            if (is_array($value)) {
                $flattenedTranslations += $this->flattenTranslations($value, $prefix . $key . '.');
            } else {
                $flattenedTranslations[$prefix . $key] = $value;
            }
        }

        return $flattenedTranslations;
    }
}
