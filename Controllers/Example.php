<?php
namespace Backend\Base\Controllers;

class Example extends \Backend\Core\Controller
{
    public function homeAction() {
        return 'Some string';
    }

    public function paramsAction($id, $another = false) {
        var_dump($id, $another);
    }
}
