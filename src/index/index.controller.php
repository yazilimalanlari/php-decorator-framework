<?php

@Controller('/')
class IndexController {
    @Get('/')
    public function index() {
        echo "index method";
    }
}