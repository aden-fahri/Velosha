<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

class HtmlSanitizer
{
    /**
     * Allowed HTML tag names.
     */
    protected const ALLOWED_TAGS = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u',
        'ul', 'ol', 'li', 'h2', 'h3', 'h4', 'blockquote', 'a',
    ];

    /**
     * Dangerous tags whose content must also be stripped completely.
     */
    protected const DANGEROUS_TAGS = [
        'script', 'style', 'iframe', 'object', 'embed', 'applet', 'meta', 'link',
    ];

    /**
     * Allowed attributes on 'a' tag.
     */
    protected const ALLOWED_A_ATTRIBUTES = [
        'href', 'title', 'target', 'rel',
    ];

    public static function clean(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        if (strpos($html, '<') === false) {
            return e($html);
        }

        $libxmlState = libxml_use_internal_errors(true);

        $dom = new DOMDocument('1.0', 'UTF-8');
        $wrapped = '<?xml encoding="utf-8" ?><div>' . $html . '</div>';
        $dom->loadHTML($wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        libxml_clear_errors();
        libxml_use_internal_errors($libxmlState);

        $root = $dom->getElementsByTagName('div')->item(0);
        if (! $root) {
            return '';
        }

        self::sanitizeNode($root);

        $output = '';
        foreach ($root->childNodes as $child) {
            $output .= $dom->saveHTML($child);
        }

        return $output;
    }

    protected static function sanitizeNode(DOMNode $node): void
    {
        $toRemove = [];

        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE) {
                /** @var DOMElement $child */
                $tagName = strtolower($child->nodeName);

                if (in_array($tagName, self::DANGEROUS_TAGS, true)) {
                    $toRemove[] = ['node' => $child, 'mode' => 'delete'];
                    continue;
                }

                if (! in_array($tagName, self::ALLOWED_TAGS, true)) {
                    self::sanitizeNode($child);
                    $toRemove[] = ['node' => $child, 'mode' => 'unwrap'];
                    continue;
                }

                self::sanitizeAttributes($child);
                self::sanitizeNode($child);
            }
        }

        foreach ($toRemove as $item) {
            $target = $item['node'];
            if ($item['mode'] === 'unwrap') {
                while ($target->hasChildNodes()) {
                    $target->parentNode->insertBefore($target->firstChild, $target);
                }
            }
            if ($target->parentNode) {
                $target->parentNode->removeChild($target);
            }
        }
    }

    protected static function sanitizeAttributes(DOMElement $element): void
    {
        $tagName = strtolower($element->nodeName);
        $attributesToRemove = [];

        foreach ($element->attributes as $attr) {
            $name = strtolower($attr->name);
            $value = $attr->value;

            if (str_starts_with($name, 'on')) {
                $attributesToRemove[] = $name;
                continue;
            }

            if ($tagName === 'a') {
                if (! in_array($name, self::ALLOWED_A_ATTRIBUTES, true)) {
                    $attributesToRemove[] = $name;
                    continue;
                }

                if ($name === 'href') {
                    $cleanHref = trim($value);
                    $decoded = rawurldecode(html_entity_decode($cleanHref, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                    $decoded = preg_replace('/[\x00-\x1F\x7F]/u', '', $decoded);

                    if (preg_match('/^\s*(javascript|data|vbscript):/i', $decoded)) {
                        $attributesToRemove[] = 'href';
                        continue;
                    }
                }
            } else {
                $attributesToRemove[] = $name;
            }
        }

        foreach ($attributesToRemove as $attrName) {
            $element->removeAttribute($attrName);
        }

        if ($tagName === 'a' && $element->hasAttribute('href')) {
            $href = $element->getAttribute('href');
            $element->setAttribute('rel', 'noopener noreferrer');
            if (preg_match('#^https?://#i', $href) || str_starts_with($href, '//')) {
                $element->setAttribute('target', '_blank');
            }
        }
    }
}
