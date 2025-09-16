<?php

namespace App\Services;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

class RichTextParser
{
    private MarkdownConverter $markdownConverter;

    public function __construct()
    {
        // Configure CommonMark with default settings
        $environment = new Environment([
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 50,
        ]);

        $environment->addExtension(new CommonMarkCoreExtension());

        $this->markdownConverter = new MarkdownConverter($environment);
    }

    /**
     * Parse rich text content that could be HTML or Markdown
     */
    public function parse(string $content): string
    {
        if (empty($content)) {
            return $content;
        }

        // Check if content is already HTML (contains HTML tags)
        if ($this->isHtml($content)) {
            return $content; // Return as-is if it's already HTML
        }

        // Otherwise, treat as Markdown and convert to HTML
        return $this->markdownConverter->convert($content)->getContent();
    }

    /**
     * Parse content specifically as Markdown
     */
    public function parseMarkdown(string $content): string
    {
        if (empty($content)) {
            return $content;
        }

        return $this->markdownConverter->convert($content)->getContent();
    }

    /**
     * Check if content contains HTML tags
     */
    private function isHtml(string $content): bool
    {
        // Check for common HTML tags
        $htmlPatterns = [
            '/<\s*\w+[^>]*>/',  // Opening tags like <p>, <div class="...">
            '/<\s*\/\s*\w+\s*>/', // Closing tags like </p>, </div>
        ];

        foreach ($htmlPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get plain text from rich content (strips HTML and Markdown)
     */
    public function toPlainText(string $content, int $limit = null): string
    {
        // First parse to HTML (handles both HTML and Markdown)
        $html = $this->parse($content);

        // Strip HTML tags
        $plainText = strip_tags($html);

        // Clean up extra whitespace
        $plainText = preg_replace('/\s+/', ' ', trim($plainText));

        // Apply limit if specified
        if ($limit && strlen($plainText) > $limit) {
            return substr($plainText, 0, $limit) . '...';
        }

        return $plainText;
    }
}