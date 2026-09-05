<?php

namespace CipherGinx;

use Monolog\Logger;

/**
 * Request Modifier
 * Modifies outgoing requests based on configuration rules
 */
class RequestModifier
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Modify request headers, body, and domain
     */
    public function modify(array $request, array $config): array
    {
        $this->logger->debug('Modifying request to ' . $request['path']);

        // Inject domain replacements
        $request = $this->injectDomainReplacements($request, $config);

        // Modify headers
        $request = $this->modifyHeaders($request, $config);

        // Modify body if POST/PUT
        if (in_array($request['method'], ['POST', 'PUT', 'PATCH'])) {
            $request = $this->modifyBody($request, $config);
        }

        return $request;
    }

    /**
     * Replace domain references in request
     */
    private function injectDomainReplacements(array $request, array $config): array
    {
        if (!isset($config['inject_domain'])) {
            return $request;
        }

        foreach ($config['inject_domain'] as [$original, $replacement]) {
            // Replace in headers
            foreach ($request['headers'] as &$header_value) {
                $header_value = str_replace($replacement, $original, $header_value);
            }

            // Replace in body
            $request['body'] = str_replace($replacement, $original, $request['body']);
        }

        return $request;
    }

    /**
     * Modify request headers based on path rules
     */
    private function modifyHeaders(array $request, array $config): array
    {
        if (!isset($config['req_headers'])) {
            return $request;
        }

        foreach ($config['req_headers'] as [$path_pattern, $header_modifications]) {
            if ($this->pathMatches($request['path'], $path_pattern)) {
                $this->logger->info("Injecting headers for path: {$request['path']}");
                $request['headers'] = array_merge($request['headers'], $header_modifications);
            }
        }

        return $request;
    }

    /**
     * Modify request body based on path rules
     */
    private function modifyBody(array $request, array $config): array
    {
        if (!isset($config['req_body'])) {
            return $request;
        }

        foreach ($config['req_body'] as [$path_pattern, $search, $replace]) {
            if ($this->pathMatches($request['path'], $path_pattern)) {
                $this->logger->info("Modifying body for path: {$request['path']}");
                $request['body'] = preg_replace($search, $replace, $request['body']);
            }
        }

        return $request;
    }

    /**
     * Check if path matches pattern (supports partial matching and regex)
     */
    private function pathMatches(string $path, string $pattern): bool
    {
        if (empty($pattern)) {
            return true; // Empty pattern matches all paths
        }

        if (strpos($pattern, '/') === 0 && preg_match($pattern, $path)) {
            return true; // Regex pattern
        }

        return strpos($path, $pattern) !== false; // Substring match
    }
}
