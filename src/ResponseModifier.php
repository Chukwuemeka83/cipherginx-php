<?php

namespace CipherGinx;

use Monolog\Logger;

/**
 * Response Modifier
 * Modifies responses from target server before sending to client
 */
class ResponseModifier
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Modify response headers, body, and content
     */
    public function modify(string $response, array $config): string
    {
        $this->logger->debug('Modifying response');

        // Parse response
        [$headers, $body] = $this->parseResponse($response);

        // Inject domain replacements in response
        if (isset($config['inject_domain'])) {
            [$headers, $body] = $this->injectDomainReplacements($headers, $body, $config);
        }

        // Modify response headers
        if (isset($config['resp_headers'])) {
            $headers = $this->modifyHeaders($headers, $config);
        }

        // Modify response body
        if (isset($config['resp_body'])) {
            $body = $this->modifyBody($body, $config);
        }

        // Reconstruct response
        return $this->reconstructResponse($headers, $body);
    }

    /**
     * Parse HTTP response into headers and body
     */
    private function parseResponse(string $response): array
    {
        [$headers_str, $body] = explode("\r\n\r\n", $response, 2);
        $headers = [];

        foreach (explode("\r\n", $headers_str) as $line) {
            if (!empty($line)) {
                $headers[] = $line;
            }
        }

        return [$headers, $body];
    }

    /**
     * Reconstruct response from headers and body
     */
    private function reconstructResponse(array $headers, string $body): string
    {
        return implode("\r\n", $headers) . "\r\n\r\n" . $body;
    }

    /**
     * Replace domain references in response
     */
    private function injectDomainReplacements(array $headers, string $body, array $config): array
    {
        foreach ($config['inject_domain'] as [$original, $replacement]) {
            // Replace in headers
            foreach ($headers as &$header) {
                $header = str_replace($original, $replacement, $header);
            }
            unset($header);

            // Replace in body
            $body = str_replace($original, $replacement, $body);
        }

        return [$headers, $body];
    }

    /**
     * Modify response headers based on path rules
     */
    private function modifyHeaders(array $headers, array $config): array
    {
        // TODO: Implement header modification based on path patterns
        return $headers;
    }

    /**
     * Modify response body based on path rules
     */
    private function modifyBody(string $body, array $config): string
    {
        foreach ($config['resp_body'] as [$path_pattern, $search, $replace]) {
            $body = str_replace($search, $replace, $body);
            $this->logger->info("Replaced '{$search}' with '{$replace}'");
        }

        return $body;
    }
}
