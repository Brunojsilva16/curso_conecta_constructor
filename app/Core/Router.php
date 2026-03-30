<?php

namespace App\Core;

class Router
{
    protected $routes = [
        'GET' => [],
        'POST' => [],
        'DELETE' => []
    ];

    public function get($uri, $controllerAction)
    {
        $this->routes['GET'][$uri] = $controllerAction;
    }

    public function post($uri, $controllerAction)
    {
        $this->routes['POST'][$uri] = $controllerAction;
    }

    public function delete($uri, $controllerAction)
    {
        $this->routes['DELETE'][$uri] = $controllerAction;
    }

    public function dispatch($url, $method)
    {
        $uri = trim($url, '/');

        foreach ($this->routes[$method] as $route => $action) {
            $regex = "#^" . preg_replace('/\{\w+\}/', '([^\/]+)', $route) . "$#";

            if (preg_match($regex, $uri, $matches)) {
                array_shift($matches);

                $controllerName = $action[0];
                $methodName = $action[1];

                $controllerInstance = new $controllerName();
                call_user_func_array([$controllerInstance, $methodName], $matches);
                return;
            }
        }
        
        http_response_code(404);
        $controller = new \App\Controllers\PageController();
        $controller->notFound();
    }
}
