<?php
class Router
{
    private $routes = [];

    public function add(string $method, string $pattern, string $handler, array $middleware = []): void
    {
        $regex = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $pattern) . '$#';
        $this->routes[] = [$method, $regex, $handler, $middleware];
    }
    public function get(string $p, string $h, array $m = []): void  { $this->add('GET', $p, $h, $m); }
    public function post(string $p, string $h, array $m = []): void { $this->add('POST', $p, $h, $m); }

    public function dispatch(string $method, string $path): void
    {
        foreach ($this->routes as [$m, $regex, $handler, $middleware]) {
            if ($m !== $method || !preg_match($regex, $path, $matches)) continue;

            // CSRF check for every POST
            if ($method === 'POST') {
                $sent = $_POST['_csrf'] ?? '';
                if (!is_string($sent) || !hash_equals(csrfToken(), $sent)) {
                    (new Controller())->renderCsrf();
                    return;
                }
            }
            foreach ($middleware as $mw) {
                if ($mw() === false) return;
            }
            [$class, $action] = explode('@', $handler);
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            (new $class())->$action(...array_values($params));
            return;
        }
        (new Controller())->renderNotFoundPage();
    }
}
