<?php
/**
* System Monitoring
**/

namespace module\issued_API\controller;
use Exception;
use classes\system\framework\dkFrameWork;

class pseudonymBatchController extends dkFrameWork
{

    public function submitAction()
    {
		$param = $this->info->arg1;
		$param = base64_decode($param);
		$param = json_decode($param, true);

		$user_idx = $param["user_idx"];
		$domain_name = $param["domain_name"];
		$device_name = $param["device_name"];
		$cert_type = $param["cert_type"];
		$cert_id = $param["cert_id"];
		$zip_password = $param["zip_password"];

		foreach($param["app_permissions"] as $key => $val)
		{
			$app_permissions[] = array(
				 "psid"	=> (int)$val["psid"]
				,"ssp"	=> $val["ssp"]
			);
		}

		$issuer_cert = "/workdir/srcs/output/cert/".$user_idx."/pca/".$device_name."/pca.cert";

		$conf = array(
			 "use_encryption_key"	=> false
			,"issuer_cert_path"		=> $issuer_cert
			,"hash_type"			=> 2
			,"output_dir"			=> "/workdir/srcs/output/cert/".$user_idx."/pseudonym/".$device_name."/".$cert_id
			,"obe_id"				=> $cert_id
			,"j_count"				=> (int)20
			,"craca_id"				=> "65538e"
			,"crl_series"			=> (int)1
			,"batch_validity_period"		=> array(
				 "begin"	=> date("Y-m-d", time())."T00:00:00"
				,"duration"	=> array(
					 "unit"		=> "YEARS"
					,"value"	=> (int)1
				)
			)
			,"unit_cert_duration"	=> array(
				 "unit"		=> "WEEKS"
				,"value"	=> 1
				,"margin"	=> array(
					 "unit"		=> "HOURS"
					,"value"	=> 1
				)
			)
			,"app_permissions"	=> $app_permissions
		);

		$conf = json_encode($conf, JSON_PRETTY_PRINT);

		// create conf file
		$fp = fopen("/var/www/scms2016gui/public/conf/pseudonym_batch.json", "w");
		fwrite($fp, $conf);
		fclose($fp);

		// run
		chdir("/workdir/srcs");
		exec("rm -rf /workdir/srcs/output/cert/".$user_idx."/pseudonym/".$device_name."/".$cert_id);
		exec("mkdir -p /workdir/srcs/output/cert/".$user_idx."/pseudonym/".$device_name."/".$cert_id);
		exec("/workdir/srcs/cert_tool create_pseudonym_batch /var/www/scms2016gui/public/conf/pseudonym_batch.json 2>&1", $output);

		// compress
		exec("zip -jr -P ".$zip_password." /workdir/srcs/output/cert/".$user_idx."/pseudonym/".$device_name."/".$cert_id.".zip /workdir/srcs/output/cert/".$user_idx."/pseudonym/".$device_name."/".$cert_id);

		$result = array(
			 "result" => "success"
			,"download_path"	=> "/workdir/srcs/output/cert/".$user_idx."/pseudonym/".$device_name."/".$cert_id.".zip"
		);

		echo json_encode($result);
		die();
    }
}
?>
