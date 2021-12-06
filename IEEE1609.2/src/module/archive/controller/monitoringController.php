<?php
/**
* System Monitoring
**/

namespace module\systemInfo\controller;
use Exception;
use module\systemInfo\model\monitoring;

class monitoringController
{
    public function indexAction()
    {

    }

	public function getDataAction()
	{
		$monitoring = new monitoring;
		$mem = $monitoring->getMeminfo();
		$cpu = $monitoring->getCPUinfo();
		$storage = $monitoring->getStorageinfo();
		$process = $monitoring->getProcessList();

		$data = array(
			 "cpu" 		=> $cpu
			,"mem" 		=> $mem
			,"storage" 	=> $storage
			,"process" 	=> $process
		);

		echo json_encode($data);

		die();
	
	}
}
?>
