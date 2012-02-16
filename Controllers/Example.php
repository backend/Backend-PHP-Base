<?php
namespace Backend\Base\Controllers;

class Example implements \Backend\Core\Interfaces\ControllerInterface
{
    public function homeAction() {
        return 'Some string';
    }

    public function getAction($id) {
    }
}
