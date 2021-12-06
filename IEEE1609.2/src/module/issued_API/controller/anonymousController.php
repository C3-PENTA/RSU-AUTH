<?php
/**
* System Monitoring
**/

namespace module\issued\controller;
use Exception;

class anonymousController
{
    public function indexAction()
    {
		// enrol 인증서 조회
		exec("ls -lt /usr/local/cert_data/enrol", $output);
		if(count($output)){
			foreach($output as $key => $val){
				if($key == 0) continue;	
				
				$tmp = explode(" ", $val);
				exec("ls /usr/local/cert_data/enrol/".$tmp[8]."/*", $output1);
				$output1 = explode("/", $output1[0]);
				$output1 = $output1[count($output1)-1];

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

		$psid = $_POST["psid"];
		$psid = str_replace(" ", "", $psid);
		$psid = explode(",", $psid);
		$psid = @join(" -p ", $psid);
		$psid = " -p ".$psid;

		$start_date = str_replace("-", "", $_POST["start_date"]);


		// parent_cert
		$parent_cert = "/usr/local/cert_data/enrol/".$_POST["parent_cert"];


		// cert issue
		$new_cert_dir = "/usr/local/cert_data/anony/".$dirname;
		exec("mkdir -p ".$new_cert_dir);
		exec($toolpath."/".$toolname." anony -C /usr/local/conf/certissue.conf -e ".$parent_cert." -s ".$start_date." -o ".$new_cert_dir."/".$_POST["certname"]." -p ".$psid);

		// zip
		$new_zip_dir = "/home/scms2012gui/public/downloads";
		exec("zip -jr ".$new_zip_dir."/Anonymous_".$dirname.".zip ".$new_cert_dir);

		$download_path = DS."downloads".DS."Anonymous_".$dirname.".zip";

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
