<?php
namespace Backend\Base\Controllers;

class ExamplesController extends \Backend\Core\Controller
{
    public function homeAction() {
        return 'Some string';
    }

    public function paramsAction($id, $another = false) {
        var_dump($id, $another);
    }
}
