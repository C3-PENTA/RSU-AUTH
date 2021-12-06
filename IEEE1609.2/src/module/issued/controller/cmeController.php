<?php
/**
* System Monitoring
**/

namespace module\issued\controller;
use Exception;

class cmeController
{
    public function indexAction()
    {
		// root 인증서 조회
		exec("ls -lt /usr/local/cert_data/root", $output);
		if(count($output)){
			foreach($output as $key => $val){
				if($key == 0) continue;	
				
				$tmp = explode(" ", $val);
				exec("ls /usr/local/cert_data/root/".$tmp[8]."/*.crt", $output1);
				$output1 = explode("/", $output1[0]);
				$output1 = $output1[count($output1)-1];
				$output1 = str_replace(".crt", "", $output1);


				//$dir_list[] = $tmp[8]."/".$output1[0];
				$dir_list[] = $tmp[8]."/".$output1;
				
			}	
		}

		return array(
			"dir_list" => @$dir_list
		);
	
    }

    public function submitAction()
    {
		// get CertToolPath
		$conf = file_get_contents(ROOT_PATH."conf.json");
		$conf = json_decode($conf);

		$toolpath = $conf->CertTool->toolpath;
		$toolname = $conf->CertTool->toolname;
		$datadir = $conf->CertTool->datadir;

		$dirname = date("YmdHis");

		// parent_cert
		$parent_cert = "/usr/local/cert_data/root/".$_POST["parent_cert"];

		// cert issue
		$new_cert_dir = "/usr/local/cert_data/cme/".$dirname;
		exec("mkdir -p ".$new_cert_dir);
		exec($toolpath."/".$toolname." cme -C /usr/local/conf/certissue.conf -y ".$_POST["y"]." -c ".$parent_cert." -n ".$_POST["cert_id"]." -o ".$new_cert_dir."/".$_POST["certname"]." -t ".$_POST["cert_type"]);

		// zip
		$new_zip_dir = "/home/scms2012gui/public/downloads";
		exec("zip -jr ".$new_zip_dir."/CME_".$dirname.".zip ".$new_cert_dir);

		$download_path = DS."downloads".DS."CME_".$dirname.".zip";

		$result_cmd = "tree ".$new_cert_dir;
		exec($result_cmd, $output);

		$output = @join("<br />", $output);

		$result = array(
			 "result"	=> "success"
			,"message"	=> $output
			,"download_path"	=> $download_path
		);
		echo json_encode($result);

		die();
    }
}
?>
