<?php

namespace App\Helpers;

class IconHelper
{
    /**
     * Load SVG icon from resources/icons folder
     * @param string $name Icon filename (without .svg extension)
     * @param array $attributes HTML attributes (width, height, class, stroke, etc)
     * @return string HTML SVG markup
     */
    public static function icon($name, $attributes = [])
    {
        $iconPath = resource_path("icons/{$name}.svg");
        
        if (!file_exists($iconPath)) {
            return "<!-- Icon not found: {$name} -->";
        }
        
        $svg = file_get_contents($iconPath);
        
        // Extract only the path/content from SVG
        // This allows us to modify attributes
        $attributes = array_merge([
            'width' => '24',
            'height' => '24',
            'viewBox' => '0 0 512 512',
            'fill' => 'none',
            'stroke' => '#999',
            'stroke-width' => '1.5',
            'stroke-linecap' => 'round',
            'stroke-linejoin' => 'round',
        ], $attributes);
        
        // Build attributes string
        $attrString = '';
        foreach ($attributes as $key => $value) {
            $attrString .= " {$key}=\"{$value}\"";
        }
        
        // Extract content from original SVG and wrap with our attributes
        preg_match('/<svg[^>]*>(.*?)<\/svg>/s', $svg, $matches);
        $content = $matches[1] ?? '';
        
        return "<svg{$attrString}>{$content}</svg>";
    }
}
