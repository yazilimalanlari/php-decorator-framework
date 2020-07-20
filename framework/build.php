<?php


class Build {
    public function __construct(array $routers) {
        if (is_dir(DIST_FOLDER)) {
            shell_exec(sprintf("rm -r %s", DIST_FOLDER));
        }

        mkdir(DIST_FOLDER);
        
        $code = $this->routers_to_code($routers);

        $routers_path = sprintf("%s/routes.php", DIST_FOLDER);        
        touch($routers_path);
        $routers_file = fopen($routers_path, "w");
        fwrite($routers_file, $code);
        fclose($routers_file);
    }

    public function routers_to_code(array $routers): string {
        $code = "<?php \$routes = array(";

        foreach($routers as $i => $router) {
            $routes = "array(";
            foreach($router->routes as $c => $route) {
                $routes .= sprintf(
                    'array("request_method" => "%s", "uri" => "%s", "method_name" => "%s")',
                    $route['request_method'],
                    $route['uri'],
                    $route['method_name'] 
                );
                if ($c < count($router->routes) - 1) {
                    $routes .= ", ";
                }
            }
            $routes .= ")";

            $code .= sprintf(
                'array("controller_name" => "%s", "controller_uri" => "%s", "routes" => %s)', 
                $router->controller_name,
                $router->controller_uri,
                $routes
            );

            if ($i < count($routers) - 1) {
                $code .= ", ";
            }
        }

        $code .= ");";
        return $code;
    }
}