<?php
/**
* System Monitoring
**/

namespace module\archive\controller;
use Exception;
use classes\system\framework\dkFrameWork;

class indexController extends dkFrameWork
{
    public function indexAction()
    {
		$dirlist = $this->dirToArray("/workdir/srcs/output");

		krsort($dirlist);

		/*
		$ls = array(
			 "Root"	=> $dirlist["root"]
			,"CME"	=> $dirlist["cme"]
			,"Enrolment"	=> $dirlist["enrol"]
			,"Anonymous"	=> $dirlist["anony"]
			,"Named"	=> $dirlist["named"]
		);
		*/
		$ls = $dirlist;
		
		return array(
			"ls"	=> $ls,
		);

    }

	public function removeDirAction()
	{
		$path = "/workdir/srcs/output/".$_POST["dir"];

		exec("rm -rf ".$path);
		
		$result = array(
			"result" => "success"
		);
		echo json_encode($result);

		die();
	}

	public function certViewAction()
	{
		$cert_file = "/workdir/srcs/output/".$_POST["path"];

		$tmp_cert_file = explode("/", $cert_file);

		$cert = file_get_contents($cert_file);
		$cert = preg_replace('/(\W)(\d+)(\W)/', '\\1"\\2"\\3', $cert);
		$cert = json_decode($cert, true, 512, JSON_BIGINT_AS_STRING);

		$cert_value = @$cert["cert"];

		chdir("/workdir/srcs");
		exec("/workdir/srcs/cert_tool parse-cert ".$cert_value, $output);

		$result = array(
			 "cert_data" 	=> $cert	
			,"parse_data"	=> $output
		);

		echo json_encode($result);

		die();
	}

	public function certViewAPIAction()
	{
		
		$param = $this->info->arg1;
		$path = base64_decode($param);

		$cert_file = $path;

		$tmp_cert_file = explode("/", $cert_file);

		$cert = file_get_contents($cert_file);
		$cert = preg_replace('/(\W)(\d+)(\W)/', '\\1"\\2"\\3', $cert);
		$cert = json_decode($cert, true, 512, JSON_BIGINT_AS_STRING);

		$cert_value = @$cert["cert"];

		chdir("/workdir/srcs");
		exec("/workdir/srcs/cert_tool parse-cert ".$cert_value, $output);

		$result = array(
			 "cert_data" 	=> $cert	
			,"parse_data"	=> $output
		);

		echo json_encode($result);

		die();
	}

	public function certificateViewAction()
	{
		$cert_value = @$_POST["cert"];

		chdir("/workdir/srcs");
		exec("/workdir/srcs/cert_tool parse-cert ".$cert_value, $output);

		$result = array(
			"parse_data"	=> $output
		);

		echo json_encode($result);

		die();
	}

	public function certificateViewAPIAction()
	{
		$param = $this->info->arg1;
		$cert_data = $param;

		chdir("/workdir/srcs");
		exec("/workdir/srcs/cert_tool parse-cert ".$cert_data, $output);

		$result = array(
			"parse_data"	=> $output
		);

		echo json_encode($result);

		die();
	}

	public function downloadAction()
	{
		$dir = $_POST["dir"];
		$fullname = explode("/", $dir);
		$filename = $fullname[count($fullname)-2];

		if(substr($filename, 0, 6) == "batch_")
		{
			$path = "/downloads/pseudonym_".$fullname[count($fullname)-4].".zip";
		}
		elseif ($filename == "ident_batch.json")
		{
			$path = "/downloads/ident_".$fullname[count($fullname)-4].".zip";
		}
		else
		{
			$tmp = explode(".", $filename);
			$path = "/downloads/".$tmp[0]."_".$fullname[count($fullname)-3].".zip";
		}

		$result = array(
			"download_path" => $path	
		);
		echo json_encode($result);

		die();
	}

	private function dirToArray($dir) 
	{
   
		$result = array(); 

		$cdir = scandir($dir); 
		natsort($cdir);
		foreach ($cdir as $key => $value) { 
			if (!in_array($value,array(".",".."))) { 
				if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) { 
					$result[$value] = $this->dirToArray($dir . DIRECTORY_SEPARATOR . $value); 
				} else { 
					$result[] = $value; 
				} 
			} 
		} 

		return $result; 
	} 
}
?>
