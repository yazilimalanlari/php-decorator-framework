<?php

@Controller('/blog')
class BlogController {
    
    @Get("/test")
    public function index() {
        echo "blog index method";
    }
    
    @Get('/merhaba-dunya')
    public function hello_world() {
        echo "hello world methodu!";
    }

    @Post('/add')
    public function add() {
        echo "blog ekle";
    }
}