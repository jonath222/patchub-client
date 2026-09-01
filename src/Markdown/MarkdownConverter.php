<?php

namespace Patchub\Client\Markdown;

use League\CommonMark\CommonMarkConverter;

class MarkdownConverter
{
    private static ?CommonMarkConverter $converter = null;

    public static function convert(string $markdown): string
    {
        if (self::$converter === null) {
            self::$converter = new CommonMarkConverter([
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ]);
        }

        return self::$converter->convert($markdown)->getContent();
    }

    public static function convertWithoutHtml(string $markdown): string
    {
        return strip_tags(self::convert($markdown));
    }
}
