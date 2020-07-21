<?php

class Build {
    const APP_PATH = DIST_FOLDER . '/app';
    const SYSTEM_PATH = self::APP_PATH . '/system';
    const CONTROLLERS_PATH = self::APP_PATH . '/controllers';

    public function __construct(array $routers) {
        if (is_dir(DIST_FOLDER)) {
            shell_exec(sprintf("rm -r %s", DIST_FOLDER));
        }

        mkdir(DIST_FOLDER);
        mkdir(self::APP_PATH);
        mkdir(self::CONTROLLERS_PATH);
        mkdir(self::SYSTEM_PATH);

        foreach($routers as $router) {
            $router->controller_path = sprintf("%s/%s.php", self::CONTROLLERS_PATH, get_rand_code());
        }
        
        $code = $this->routers_to_code($routers);

        $routers_path = sprintf("%s/routes.php", self::SYSTEM_PATH);
        file_write($routers_path, $code);

        $this->code_export(array_map(function($item) {
            return (object) [
                "code" => $item->code,
                "path" => $item->controller_path
            ];
        }, $routers));

        file_write(
            sprintf("%s/router.php", self::SYSTEM_PATH), 
            $this->code_process(file_read(__DIR__ . '/router.php'))
        );


        $this->index_php();
    }

    private function index_php() {
        $code = "<?php ";
        $code .= "define('DEVELOPMENT_MODE', false);";
        $code .= "define('BUILD_MODE', false);";
        $code .= "define('SYSTEM_PATH', __DIR__ . '/app/system');";
        $code .= "require SYSTEM_PATH . '/routes.php';";
        $code .= "require SYSTEM_PATH . '/router.php';";
        $code .= 'foreach($routes as $route){';
        $code .= '$router = new Router(\'\', $route[\'controller_uri\'], $route[\'controller_name\']);';
        $code .= '$router->routes = $route[\'routes\'];';
        $code .= '$router->controller_path = $route[\'controller_path\'];';
        $code .= '$router->run();';
        $code .= "}";
        file_write(DIST_FOLDER . '/index.php', $code);
    }
    
    private function code_export(array $routers) {
        foreach($routers as $router) {
            $result = $this->code_process($router->code);
            $file = fopen($router->path, "w");
            fwrite($file, $result);
            fclose($file);
        }
    }
    
    private function code_process(string $_code) {
        $_code = preg_replace('@//.*\n@', '', $_code);
        $code = "";
        $string = false;
        $code_parse = str_split($_code);
        for($i = 0; $i < count($code_parse); $i++) {
            $c = $code_parse[$i];
            switch($c) {
                case " ":
                    if ($string || $code_parse[$i + 1] !== ' ') {
                        $code .= $c;
                    }
                break;
                case "\"":
                    $code .= $c;
                    $string = !$string;
                break;
                case "'":
                    $code .= $c;
                    $string = !$string;
                case "\n":
                    if (!$string && substr($code, -5) === '<?php') {
                        $code .= ' ';
                    }
                break;
                default:
                    $code .= $c;
            }
        }
        return $code;
    }

    private function routers_to_code(array $routers): string {
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
                'array("controller_name" => "%s", "controller_uri" => "%s", "controller_path" => "%s", "routes" => %s)', 
                $router->controller_name,
                $router->controller_uri,
                $router->controller_path,
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