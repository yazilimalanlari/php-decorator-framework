<?php

@Controller('/uye')
class ProfileController {
    @Get('/:username')
    public function index() {
        echo "Burası profil sayfası.";
    }
}