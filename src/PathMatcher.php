<?php

namespace CipherGinx;

/**
 * Path Matcher
 * Matches URL paths against configured patterns (substring, regex, wildcard)
 */
class PathMatcher
{
    /**
     * Check if path matches a pattern
     * Supports:
     * - Empty string: matches all paths
     * - Substring: partial URL path matching
     * - Regex: patterns wrapped in / / delimiters
     */
    public static function matches(string $path, string $pattern): bool
    {
        // Empty pattern matches all
        if (empty($pattern)) {
            return true;
        }

        // Regex pattern (wrapped in / /)
        if (preg_match('/^\/.*\/$/', $pattern)) {
            return (bool) preg_match($pattern, $path);
        }

        // Substring match
        return strpos($path, $pattern) !== false;
    }

    /**
     * Remove query string from path
     */
    public static function stripQueryString(string $path): string
    {
        return explode('?', $path)[0];
    }
}
