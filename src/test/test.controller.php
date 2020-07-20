<?php

@Controller('/test')
class TestController {
    
    @Get('/')
    public function index() {
        echo "test index method";
    }
}