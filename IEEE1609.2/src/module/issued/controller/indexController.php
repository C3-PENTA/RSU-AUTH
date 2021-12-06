<?php
/**
* System Monitoring
**/

namespace module\systemInfo\controller;
use Exception;

class indexController
{
    public function indexAction()
    {
		header("Location:/issued/root");
		exit;
    }
}
?>
