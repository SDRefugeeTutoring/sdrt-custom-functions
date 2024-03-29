<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\Support;

/**
 * Helper for enqueueing scripts and styles compiled by Laravel Mix.
 */
class Mix
{
    public static function enqueueScript($file, $inFooter = true): void
    {
        $manifestFile = self::getManifestFile($file);
        wp_enqueue_script(
            sanitize_title("sdrt-$file"),
            untrailingslashit(SDRT_ASSETS_URL) . $manifestFile,
            [],
            '',
            $inFooter
        );
    }

    public static function addInlineScript(string $file, string $variableName, array $data): void {
        $json = wp_json_encode($data);
        wp_add_inline_script(
            sanitize_title("sdrt-$file"),
            "window.$variableName = $json;",
            'before'
        );
    }

    private static function getManifestFile($file): string
    {
        static $manifest = null;

        if ($manifest === null) {
            $manifest = json_decode(
                file_get_contents(SDRT_ASSETS_DIR . 'mix-manifest.json'),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        }

        return $manifest["/$file"];
    }
}