<?php
/**
* System Monitoring
**/

namespace module\issued\controller;
use Exception;

class rseApplicationController
{
    public function indexAction()
    {
		// 인증서 조회
		$dir_list_pca = array();
		exec("ls -R /workdir/srcs/output/*/*/pca/*/pca.cert", $output);
		if(count($output)){
			foreach($output as $key => $val){
				if(substr($val,0,1) != "/")
					continue;

				$tmp = str_replace(":", "", $val);
				$tmp = str_replace("/workdir/srcs/output/", "", $tmp);
				$tmp = str_replace("/pca.cert", "", $tmp);
				$dir_list_pca[] = $tmp;
			}	
		}

		unset($output);
		$dir_list_obe = array();
		exec("ls -R /workdir/srcs/output/*/*/eca/*/obeEnrol/*/*.cert", $output);
		if(count($output)){
			foreach($output as $key => $val){
				if(substr($val,0,1) != "/")
					continue;

				$tmp = str_replace(":", "", $val);
				$tmp = str_replace("/workdir/srcs/output/", "", $tmp);
				$dir_list_obe[] = $tmp;
			}	
		}

		return array(
			 "dir_list_pca"	=> $dir_list_pca
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
		$issuer_cert_obe = "/workdir/srcs/output/".$_POST["issuer_cert_obe"];

		$conf = array(
			 "cert_type"			=> "implicit"
			,"use_encryption_key"	=> $_POST["use_encryption_key"]
			,"hash_type"			=> $_POST["hash_type"]
			,"issuer_cert_path"		=> $issuer_cert
			,"output_file_path"		=> "/workdir/srcs/output/".$_POST["issuer_cert"]."/rseApplication/".$dirname."/rseApplication.cert"
			,"cert_id"				=> array(
				 "type"		=> "binaryid"
				,"enrol_cert_path"		=> $issuer_cert_obe
			)
			,"obe_id"				=> $_POST["obe_id"]
			,"craca_id"				=> $_POST["craca_id"]
			,"crl_series"			=> (int)$_POST["crl_series"]
			,"validity_period"		=> array(
				 "begin"	=> $_POST["validity_period"]."T00:00:00"
				,"duration"	=> array(
					 "unit"		=> "YEARS"
					,"value"	=> (int)$_POST["duration"]
				)
			)
			,"app_permissions"	=> $app_permissions
		);

		$conf = json_encode($conf, JSON_PRETTY_PRINT);

		// create conf file
		$fp = fopen("/var/www/scms2016gui/public/conf/rseApplication.json", "w");
		fwrite($fp, $conf);
		fclose($fp);

		// run
		chdir("/workdir/srcs");
		exec("mkdir -p /workdir/srcs/output/".$_POST["issuer_cert"]."/rseApplication/".$dirname);
		exec("/workdir/srcs/cert_tool create-cert /var/www/scms2016gui/public/conf/rseApplication.json 2>&1", $output);

		// error handle
		if(count($output)>1)
		{
			$result = array(
				 "result" => "error"
				,"message" => $output[count($output)-1]
			);	

			echo json_encode($result);
			die();
		}

		// compress
		exec("zip -jr /var/www/scms2016gui/public/downloads/rseApplication_".$dirname.".zip /workdir/srcs/output/".$_POST["issuer_cert"]."/rseApplication/".$dirname);

		$result = array(
			 "result" => "success"
			,"download_path"	=> "/downloads/rseApplication_".$dirname.".zip"
		);

		echo json_encode($result);
		die();
    }
}
?>
