<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<string, array<int, array{pattern:string, handler:mixed, params:array<int,string>}>> */
    private array $routes = ['GET' => [], 'POST' => []];

    public function get(string $path, mixed $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, mixed $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, mixed $handler): void
    {
        $params = [];
        $pattern = preg_replace_callback(
            '#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#',
            function ($m) use (&$params) {
                $params[] = $m[1];
                return '([^/]+)';
            },
            $path
        );
        $this->routes[$method][] = [
            'pattern' => '#^' . $pattern . '$#',
            'handler' => $handler,
            'params'  => $params,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        // Normalize: strip query string, remove trailing slash (except root)
        $uri = strtok($uri, '?');
        if ($uri !== '/' && str_ends_with($uri, '/')) {
            $uri = rtrim($uri, '/');
        }

        // HEAD acts like GET
        if ($method === 'HEAD') {
            $method = 'GET';
        }

        $candidates = $this->routes[$method] ?? [];
        foreach ($candidates as $route) {
            if (preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches);
                $args = [];
                foreach ($route['params'] as $i => $name) {
                    $args[$name] = $matches[$i] ?? null;
                }
                $this->call($route['handler'], $args);
                return;
            }
        }

        // 404
        http_response_code(404);
        $this->call(['App\\Controllers\\LegalController', 'notFound'], []);
    }

    private function call(mixed $handler, array $args): void
    {
        if (is_array($handler) && count($handler) === 2) {
            [$class, $method] = $handler;
            $instance = new $class();
            $instance->$method($args);
            return;
        }
        if (is_callable($handler)) {
            $handler($args);
            return;
        }
        throw new \RuntimeException('Invalid route handler.');
    }
}
