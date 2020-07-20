<?php

class Router {
    public string $code;
    public string $controller_name;
    public string $controller_uri;
    public array $routes = array();
    private array $request_uri_array = array();

    public function __construct(string &$code, string $controller_uri, string $controller_name) {
        $this->code = &$code;
        $this->controller_uri = $controller_uri;
        $this->controller_name = $controller_name;

        if (!BUILD_MODE) {
            $this->request_uri_array = array_map(function(string $item) {
                return "/$item";
            }, array_values(array_filter(explode('/', $_SERVER['REQUEST_URI']))));
        
            if (empty($this->request_uri_array)) {
                $this->request_uri_array[0] = '/';
            }
        }
    }

    public function add_route(string $request_method, string $uri, string $method_name) {
        array_push($this->routes, [
            "request_method" => $request_method,
            "uri" => $uri,
            "method_name" => $method_name
        ]);
    }
    
    private function uri_to_pattern(string $uri): string {
        $uri = preg_replace('@/@', '\/', $uri);
        $uri = preg_replace_callback('/:([a-z]+)/', function($key) {
            // print_r($key);

            return "[a-zA-Z0-9-_.]+";
        }, $uri);
        return "/^$uri\/?$/";
    }

    public function run() {
        $controller_pattern = $this->uri_to_pattern($this->controller_uri);
        
        if (preg_match($controller_pattern, $this->request_uri_array[0])) {
            foreach($this->routes as $route) {
                if (strtoupper($route['request_method']) !== $_SERVER['REQUEST_METHOD']) {
                    continue;
                }

                $method_pattern = rtrim($controller_pattern, '\/?$/') . ltrim($this->uri_to_pattern($route['uri']), '/^');
                
                if (preg_match($method_pattern, $_SERVER['REQUEST_URI'])) {
                    $this->code = trim($this->code, '<?php');
                    eval($this->code);

                    if (class_exists($this->controller_name)) {
                        $controller = new $this->controller_name();
                        if (method_exists($controller, $route['method_name'])) {
                            call_user_func(array($controller, $route['method_name']));
                        }
                    }
                }
            }
        }
    }
}