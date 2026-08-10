<?php

class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->routes['GET'][] = [$path, $handler];
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->routes['POST'][] = [$path, $handler];
    }

    public function dispatch(string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes[$method] ?? [] as [$route, $handler]) {
            if ($route === $path) {
                $this->callHandler($handler);
                return;
            }
        }

        http_response_code(404);
        echo '404 - Page introuvable';
    }

    private function callHandler(callable|array $handler): void
    {
        if (is_array($handler)) {
            [$controllerName, $method] = $handler;
            require_once __DIR__ . '/../controllers/' . $controllerName . '.php';
            $controller = new $controllerName();
            $controller->$method();
            return;
        }

        $handler();
    }
}
