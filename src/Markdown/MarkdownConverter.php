<?php

namespace Patchub\Client\Markdown;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter as LeagueMarkdownConverter;

class MarkdownConverter
{
    private static ?LeagueMarkdownConverter $converter = null;

    public static function convert(string $markdown): string
    {
        if (self::$converter === null) {
            $environment = new Environment([
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ]);

            $environment->addExtension(new CommonMarkCoreExtension());
            $environment->addExtension(new TableExtension());

            self::$converter = new LeagueMarkdownConverter($environment);
        }

        return self::$converter->convert($markdown)->getContent();
    }

    public static function convertWithoutHtml(string $markdown): string
    {
        return strip_tags(self::convert($markdown));
    }
}
