<?php $routes = array(array("controller_name" => "BlogController", "controller_uri" => "/blog", "routes" => array(array("request_method" => "Get", "uri" => "/test", "method_name" => "index"), array("request_method" => "Get", "uri" => "/merhaba-dunya", "method_name" => "hello_world"), array("request_method" => "Post", "uri" => "/add", "method_name" => "add"))), array("controller_name" => "IndexController", "controller_uri" => "/", "routes" => array(array("request_method" => "Get", "uri" => "/", "method_name" => "index"))), array("controller_name" => "TestController", "controller_uri" => "/test", "routes" => array(array("request_method" => "Get", "uri" => "/", "method_name" => "index"))));