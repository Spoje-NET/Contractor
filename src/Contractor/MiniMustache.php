<?php

declare(strict_types=1);

namespace AbraFlexi\Contractor;

/**
 * Minimal implementation of Mustache for DOCX templates
 */
class MiniMustache
{
    /**
     * Render template
     *
     * @param string $template
     * @param array $data
     * @return string
     */
    public function render(string $template, array $data): string
    {
        // Handle Sections
        // {{#key}} ... {{/key}}
        // Supports arrays (iteration) and booleans (if/else)
        
        $template = preg_replace_callback(
            '/{{#\s*([a-zA-Z0-9_.]+)\s*}}(.*?){{\/\s*\1\s*}}/s',
            function ($matches) use ($data) {
                $key = $matches[1];
                $content = $matches[2];
                $value = $this->getValue($key, $data);

                if (is_array($value) && !empty($value) && isset($value[0])) {
                    // List
                    $buffer = '';
                    foreach ($value as $item) {
                        // Merge item with parent data to allow access to parent scope if needed (basic mustache doesn't do this easily but use context stack)
                        // Simple approach: Item is context.
                        $context = is_array($item) ? array_merge($data, $item) : $data; 
                        // Actually mustache spec: context stack.
                        // Let's just pass item as data if it's array, plus parent?
                        // For simplicity: If item is array, use it as data.
                        if (is_array($item)) {
                           $buffer .= $this->render($content, $item);
                        } else {
                           // item is scalar, usually used as {{.}}
                           $buffer .= $this->render($content, array_merge($data, ['.' => $item]));
                        }
                    }
                    return $buffer;
                } elseif (!empty($value) && $value !== false) {
                    // Truthy or Non-empty hash
                    // If hash, push to context
                    $context = is_array($value) ? array_merge($data, $value) : $data;
                    return $this->render($content, $context);
                } else {
                    // Falsey
                    return '';
                }
            },
            $template
        );

        // Handle Variables
        $template = preg_replace_callback(
            '/{{(?!\/|#)\s*([a-zA-Z0-9_.]+)\s*}}/',
            function ($matches) use ($data) {
                $key = $matches[1];
                $val = $this->getValue($key, $data);
                return htmlspecialchars((string)$val, ENT_XML1, 'UTF-8');
            },
            $template
        );

        return $template;
    }

    private function getValue(string $key, array $data)
    {
        if ($key === '.') {
            return $data['.'] ?? null;
        }

        $parts = explode('.', $key);
        $current = $data;

        foreach ($parts as $part) {
            if (is_array($current) && array_key_exists($part, $current)) {
                $current = $current[$part];
            } else {
                return null; // Not found
            }
        }
        return $current;
    }
}
