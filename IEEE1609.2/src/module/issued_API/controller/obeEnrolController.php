<?php
/**
* System Monitoring
**/

namespace module\issued_API\controller;
use Exception;
use classes\system\framework\dkFrameWork;

class obeEnrolController extends dkFrameWork
{
    public function submitAction()
    {
		$user_idx = $this->info->arg1;
		$cert_id = $this->info->arg2;	

		$cert_issue_permissions[] = array(
			 "subject_permissions" => array(
				"permission_type"	=> "ALL"
			)
			,"min_chain_depth"		=> (int)2
			,"chain_depth_range"	=> (int)0
			,"eetype"				=> array(
				 "app"		=> (bool)1
				,"enrol"	=> (bool)1
			)
		);

		$issuer_cert = "/workdir/srcs/output/cert/".$user_idx."/eca/".$cert_id."/eca.cert";

		$conf = array(
			 "cert_type"			=> "implicit"
			,"use_encryption_key"	=> true
			,"hash_type"			=> 2
			,"issuer_cert_path"		=> $issuer_cert
			,"output_file_path"		=> "/workdir/srcs/output/cert/".$user_idx."/obeEnrol/".$cert_id."/obeEnrol.cert"
			,"cert_id"				=> array(
				 "type"		=> "hostname"
				,"value"	=> $cert_id
			)
			,"obe_id"				=> $cert_id
			,"craca_id"				=> "65538e"
			,"crl_series"			=> (int)4
			,"validity_period"		=> array(
				 "begin"	=> date("Y-m-d", time())."T00:00:00"
				,"duration"	=> array(
					 "unit"		=> "YEARS"
					,"value"	=> (int)1
				)
			)
			,"cert_issue_permissions" => $cert_issue_permissions
		);

		$conf = json_encode($conf, JSON_PRETTY_PRINT);

		// create conf file
		$fp = fopen("/var/www/scms2016gui/public/conf/obeEnrol.json", "w");
		fwrite($fp, $conf);
		fclose($fp);

		// run
		chdir("/workdir/srcs");
		exec("rm -rf /workdir/srcs/output/cert/".$user_idx."/obeEnrol/".$cert_id);
		exec("mkdir -p /workdir/srcs/output/cert/".$user_idx."/obeEnrol/".$cert_id);
		exec("/workdir/srcs/cert_tool create-cert /var/www/scms2016gui/public/conf/obeEnrol.json 2>&1", $output);

		// compress
		exec("zip -jr /workdir/srcs/output/cert/".$user_idx."/obeEnrol/".$cert_id.".zip /workdir/srcs/output/cert/".$user_idx."/obeEnrol/".$cert_id.".cert");

		$result = array(
			 "result" => "success"
			,"download_path"	=> "/workdir/srcs/output/cert/".$user_idx."/obeEnrol/".$cert_id.".zip"
		);

		echo json_encode($result);
		die();
    }
}
?>
