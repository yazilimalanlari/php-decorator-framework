<?php
require __DIR__ . '/helper.php';
require __DIR__ . '/router.php';
require __DIR__ . '/build.php';

class Kernel {
    private array $routers = array();
    public function __construct() {
        $directory_list = array_diff(scandir(SRC_FOLDER), array('.', '..'));
        
        foreach($directory_list as $item) {
            $controller_path = SRC_FOLDER . "/$item/$item.controller.php";
            if (file_exists($controller_path)) {
                $code = file_read($controller_path);
                if ($code != null) {
                    $this->controller_process($code);
                }
            }
        }
    }
    
    private function controller_process(string $code) {
        $controller_decorator_pattern = "/@Controller\(['\"]+([a-zA-Z0-9-_.:\/]+)['\"]+\)\\nclass ([a-zA-Z_]{1}[a-zA-Z0-9_]+)/";
        preg_match($controller_decorator_pattern, $code, $controller_decorator);
        $code = preg_replace($controller_decorator_pattern, "class $2", $code);
        

        $method_decorator_pattern = "/@([A-Z]{1}[a-z]+)\(['\"]+([a-zA-Z0-9-_.:\/]+)['\"]+\)\\n\s+public function ([a-zA-Z_]+[a-zA-Z0-9_]+)/";
        preg_match_all($method_decorator_pattern, $code, $out, PREG_SET_ORDER);
        $code = preg_replace($method_decorator_pattern, "public function $3", $code);

        $router = new Router($code, $controller_decorator[1], $controller_decorator[2]);

        foreach($out as $item) {
            $router->add_route($item[1], $item[2], $item[3]);
        }
        
        array_push($this->routers, $router);

        if (!BUILD_MODE)
            $router->run();
    }

    public function build() {
        new Build($this->routers);
    }
}



$init = function() {
    $kernel = new Kernel();

    if (BUILD_MODE) {
        $kernel->build();
    }
};