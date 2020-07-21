<?php

@Controller('/blog')
class BlogController {
    
    @Get("/")
    public function index() {
        echo "blog index method";
    }
    
    @Get('/:link')
    public function blog_detail(object $args) {
        echo "Girilen link: $args->link";
    }

    @Post('/add')
    public function add() {
        echo "blog ekle";
    }
}