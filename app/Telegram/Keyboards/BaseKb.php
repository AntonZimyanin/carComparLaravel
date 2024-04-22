<?php 

namespace App\Telegram\Keyboards;

use DefStudio\Telegraph\Keyboard\Keyboard;

    abstract class BaseKb { 

        public function getButtons() : array { return []; }
        public function getInlineKb() : Keyboard { return Keyboard::make(); }     
    }