<?php
/**
* System Monitoring
**/

namespace module\issued_API\controller;
use Exception;
use classes\system\framework\dkFrameWork;

class pcaController extends dkFrameWork
{

    public function submitAction()
    {
		$user_idx = $this->info->arg1;
		$cert_id = $this->info->arg2;	

		$app_permissions[] = array(
			 "psid"	=> (int)35
			,"ssp"	=> "830001"
		);
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

		$issuer_cert = "/workdir/srcs/output/ica/authentica/ica.cert";

		$conf = array(
			 "cert_type"			=> "explicit"
			,"use_encryption_key"	=> true
			,"issuer_cert_path"		=> $issuer_cert
			,"output_file_path"		=> "/workdir/srcs/output/cert/".$user_idx."/pca/".$cert_id."/pca.cert"
			,"cert_id"				=> array(
				 "type"		=> "hostname"
				,"value"	=> $cert_id
			)
			,"craca_id"				=> "65538e"
			,"crl_series"			=> (int)2
			,"validity_period"		=> array(
				 "begin"	=> date("Y-m-d", time())."T00:00:00"
				,"duration"	=> array(
					 "unit"		=> "YEARS"
					,"value"	=> (int)1
				)
			)
			,"app_permissions"	=> $app_permissions
			,"cert_issue_permissions" => $cert_issue_permissions
		);

		$conf = json_encode($conf, JSON_PRETTY_PRINT);

		// create conf file
		$fp = fopen("/var/www/scms2016gui/public/conf/pca.json", "w");
		fwrite($fp, $conf);
		fclose($fp);

		// run
		chdir("/workdir/srcs");
		exec("rm -rf /workdir/srcs/output/cert/".$user_idx."/pca/".$cert_id);
		exec("mkdir -p /workdir/srcs/output/cert/".$user_idx."/pca/".$cert_id);
		exec("/workdir/srcs/cert_tool create-cert /var/www/scms2016gui/public/conf/pca.json 2>&1", $output);

		exec("zip -jr /workdir/srcs/output/cert/".$user_idx."/pca/".$cert_id."/pca.zip /workdir/srcs/output/cert/".$user_idx."/pca/".$cert_id."/pca.cert");

		$result = array(
			 "result" => "success"
			,"cert_path"	=> "/workdir/srcs/output/cert/".$user_idx."/pca/".$cert_id."/pca.zip"
		);

		echo json_encode($result);
		die();
    }
}
?>
