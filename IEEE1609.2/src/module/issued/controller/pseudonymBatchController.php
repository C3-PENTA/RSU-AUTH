<?php
/**
* System Monitoring
**/

namespace module\issued\controller;
use Exception;

class pseudonymBatchController
{
    public function indexAction()
    {
		// pca 인증서 조회
		$dir_list = array();
		exec("ls -R /workdir/srcs/output/*/*/pca/*/pca.cert", $output);
		if(count($output)){
			foreach($output as $key => $val){
				if(substr($val,0,1) != "/")
					continue;

				$tmp = str_replace(":", "", $val);
				$tmp = str_replace("/workdir/srcs/output/", "", $tmp);
				$tmp = str_replace("/pca.cert", "", $tmp);
				$dir_list[] = $tmp;
			}	
		}

		// obe enrol 인증서 조회
		$dir_list_obe = array();
		exec("ls -R /workdir/srcs/output/*/*/eca/*/obeEnrol/*/*.cert", $output1);
		if(count($output1)){
			foreach($output1 as $key => $val){
				if(substr($val,0,1) != "/")
					continue;

				$tmp = str_replace(":", "", $val);
				$tmp = str_replace("/workdir/srcs/output/", "", $tmp);
				$tmp = explode("/", $tmp);
				array_shift($tmp);
				array_shift($tmp);
				array_shift($tmp);
				array_shift($tmp);
				array_shift($tmp);
				$tmp = join("/", $tmp);
				$dir_list_obe[] = $tmp;
			}	
		}

		return array(
			 "dir_list"		=> $dir_list
			,"dir_list_obe"	=> $dir_list_obe
		);

    }

    public function submitAction()
    {

		$dirname = date("Ymd")."T".date("His");

		foreach($_POST["app_permissions"] as $key => $val)
		{
			$app_permissions[] = array(
				 "psid"	=> (int)$val[0]	
				,"ssp"	=> $val[1]	
			);
		}

		$issuer_cert = "/workdir/srcs/output/".$_POST["issuer_cert"]."/pca.cert";

		$obe_id = str_replace("/", "_", $_POST["obe_id"]);

		$conf = array(
			 "use_encryption_key"	=> $_POST["use_encryption_key"]
			,"issuer_cert_path"		=> $issuer_cert
			,"hash_type"			=> $_POST["hash_type"]
			,"output_dir"		=> "/workdir/srcs/output/".$_POST["issuer_cert"]."/pseudonym/".$dirname
			,"obe_id"				=> $obe_id
			,"j_count"				=> (int)$_POST["j_count"]
			,"craca_id"				=> "d21590"
			,"crl_series"			=> (int)1
			,"batch_validity_period"		=> array(
				 "begin"	=> $_POST["validity_period"]."T00:00:00"
				,"duration"	=> array(
					 "unit"		=> "YEARS"
					,"value"	=> (int)$_POST["duration"]
				)
			)
			,"unit_cert_duration"	=> array(
				 "unit"		=> "WEEKS"
				,"value"	=> (int)$_POST["unit_value"]
				,"margin"	=> array(
					 "unit"		=> "HOURS"
					,"value"	=> (int)$_POST["unit_margin_value"]
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
		exec("mkdir -p /workdir/srcs/output/".$_POST["issuer_cert"]."/pseudonym/".$dirname);
		exec("/workdir/srcs/cert_tool create_pseudonym_batch /var/www/scms2016gui/public/conf/pseudonym_batch.json 2>&1", $output);

		// error handle
		if(count($output)>1)
		{
			if(!strpos($output[count($output)-1], "cert batch generation time"))
			{
				$result = array(
					 "result" => "error"
					,"message" => $output[count($output)-1]
				);	

				echo json_encode($result);
				die();
			}
		}

		// compress
		exec("zip -jr /var/www/scms2016gui/public/downloads/pseudonym_".$dirname.".zip /workdir/srcs/output/".$_POST["issuer_cert"]."/pseudonym/".$dirname);

		$result = array(
			 "result" => "success"
			,"download_path"	=> "/downloads/pseudonym_".$dirname.".zip"
		);

		echo json_encode($result);
		die();
    }
}
?>
