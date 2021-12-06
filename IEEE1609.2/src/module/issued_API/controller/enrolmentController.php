<?php
/**
* System Monitoring
**/

namespace module\issued\controller;
use Exception;

class enrolmentController
{
    public function indexAction()
    {

    }

    public function submitAction()
    {
		// get CertToolPath
		$conf = file_get_contents(ROOT_PATH."conf.json");
		$conf = json_decode($conf);

		$toolpath = $conf->CertTool->toolpath;
		$toolname = $conf->CertTool->toolname;
		$datadir = $conf->CertTool->datadir;

		$date_range = $_POST["date_range"];
		$date_tmp = explode(" ~ ", $date_range);
		$start_date = $date_tmp[0]." 00:00:00";
		$start_date_s = str_replace("-", "", $date_tmp[0]);
		$start_date_d = strtotime($date_tmp[0]." 00:00:00");
		$end_date = $date_tmp[1]." 11:59:59";
		$end_date_d = strtotime($date_tmp[1]." 11:59:59");

		$due_date = ceil(($end_date_d - $start_date_d)/86400);


		$psid = $_POST["psid"];
		$psid = str_replace(" ", "", $psid);
		$psid = explode(",", $psid);
		$psid = @join(" -p ", $psid);
		$psid = " -p ".$psid;

		$dirname = date("YmdHis");

		// cert issue
		$new_cert_dir = "/usr/local/cert_data/enrol/".$dirname;
		exec("mkdir -p ".$new_cert_dir);
		exec("mkdir sqlite");
		exec($toolpath."/".$toolname." enrol -C /usr/local/conf/certissue.conf -s ".$start_date_s." -d ".$due_date." -n ".$_POST["cert_id"].$psid." -o ".$new_cert_dir."/".$_POST["certname"]." -t ".$_POST["cert_type"]);

		// zip
		$new_zip_dir = "/home/scms2012gui/public/downloads";
		exec("zip -jr ".$new_zip_dir."/Enrolment_".$dirname.".zip ".$new_cert_dir);

		$download_path = DS."downloads".DS."Enrolment_".$dirname.".zip";

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
