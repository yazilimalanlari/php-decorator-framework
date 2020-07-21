<?php

class Router {
    public string $code;
    public string $controller_name;
    public string $controller_uri;
    public string $controller_path;
    public array $routes = array();
    private array $request_uri_array = array();

    public function __construct(string $code = "", string $controller_uri, string $controller_name) {        
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
    
    private function uri_to_pattern(string $uri, string $start = '/^', string $end = '\/?$/'): object {
        $request_args = array();
        
        $pattern = '/:([a-zA-Z_]{1}[a-zA-Z0-9_]+)/';
        if (preg_match_all($pattern, $uri, $custom_uri_list)) {
            $request_args = $custom_uri_list[1];
            $uri = preg_replace(['@/@', $pattern], ['\/', '([a-zA-Z0-9-_@.]+)'], $uri);
        } else {
            $uri = preg_replace('@/@', '\/', $uri);
        }

        return (object)[
            "pattern" => "$start$uri$end",
            "default_pattern" => $uri,
            "request_args" => $request_args
        ];
    }

    private function set_request_args(array $request_uri_match, array &$request_args) {
        
    }

    public function run() {
        $controller_uri = $this->uri_to_pattern($this->controller_uri === '/' ? '' : $this->controller_uri, '/^', '\//');
        $request_args = $controller_uri->request_args;

        $request_uri = $_SERVER['REQUEST_URI'];
        if ($request_uri[strlen($request_uri)-1] !== '/') {
            $request_uri .= '/';
        }
        
        if (preg_match_all($controller_uri->pattern, $request_uri, $request_uri_match, PREG_SET_ORDER)) {            
            foreach($this->routes as $route) {
                if (strtoupper($route['request_method']) !== $_SERVER['REQUEST_METHOD']) {
                    continue;
                }
                
                $method_uri = $this->uri_to_pattern($route['uri'] === '/' ? '' : $route['uri'], '', '\/?$/');
                
                $method_pattern = sprintf(
                    '/^%s%s',
                    $controller_uri->default_pattern,
                    $method_uri->pattern
                );                
                
                if (preg_match_all($method_pattern, $_SERVER['REQUEST_URI'], $request_uri_match, PREG_SET_ORDER)) {
                    $request_uri_match = $request_uri_match[0];
                    array_shift($request_uri_match);

                    $this->code = trim($this->code, '<?php');
                    
                    if (DEVELOPMENT_MODE) {
                        eval($this->code);
                    } else if(file_exists($this->controller_path)) {
                        require $this->controller_path;
                    }

                    if (class_exists($this->controller_name)) {
                        $request_args = array_merge($request_args, $method_uri->request_args);
                        $request_args = (object)array_combine($request_args, $request_uri_match);
                        

                        $controller = new $this->controller_name();
                        if (method_exists($controller, $route['method_name'])) {
                            call_user_func(array($controller, $route['method_name']), $request_args);
                            break;
                        }
                    }
                }
            }
        }
    }
}