<?php
/**
* System Monitoring
**/

namespace module\issued\controller;
use Exception;

class namedController
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

		$date_range = $_POST["date_range"];
		$date_tmp = explode(" ~ ", $date_range);
		$start_date = $date_tmp[0]." 00:00:00";
		$start_date_s = str_replace("-", "", $date_tmp[0]);
		$start_date_d = strtotime($date_tmp[0]." 00:00:00");
		$end_date = $date_tmp[1]." 11:59:59";
		$end_date_d = strtotime($date_tmp[1]." 11:59:59");

		$due_date = ceil(($end_date_d - $start_date_d)/86400);

		// parent_cert
		$parent_cert = "/usr/local/cert_data/enrol/".$_POST["parent_cert"];

		switch($_POST["region_type"]){
			case "circle" 		:	$r_value = "-a ".$_POST["circle_radius"]; break;
			case "rectangle" 	:	$r_value = "-l ".$_POST["rectangle_count"]; break;
			case "polygon" 		:	$r_value = "-l ".$_POST["polygon_count"]; break;
		}

		// cert issue
		$new_cert_dir = "/usr/local/cert_data/named/".$dirname;
		exec("mkdir -p ".$new_cert_dir);

		//$output["cmd"] = $toolpath."/".$toolname." named -C /usr/local/conf/certissue.conf -e ".$parent_cert." -s ".$start_date." -d ".$due_date." -m ".$_POST["psid_order"]." -n ".$_POST["cert_id"]." -t ".$_POST["cert_type"]." -f ".$_POST["key_type"]." -r ".$_POST["region_type"]." ".$r_value." -i ".$_POST["lat"]." -g ".$_POST["lng"]." -o ".$new_cert_dir."/".$_POST["certname"]." -p ".$psid;

		exec($toolpath."/".$toolname." named -C /usr/local/conf/certissue.conf -e ".$parent_cert." -s ".$start_date_s." -d ".$due_date." -m ".$_POST["psid_order"]." -n ".$_POST["cert_id"]." -t ".$_POST["cert_type"]." -f ".$_POST["key_type"]." -r ".$_POST["region_type"]." ".$r_value." -i ".$_POST["lat"]." -g ".$_POST["lng"]." -o ".$new_cert_dir."/".$_POST["certname"]." -p ".$psid);

		// zip
		$new_zip_dir = "/home/scms2012gui/public/downloads";
		exec("zip -jr ".$new_zip_dir."/Named_".$dirname.".zip ".$new_cert_dir);

		$download_path = DS."downloads".DS."Named_".$dirname.".zip";

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
