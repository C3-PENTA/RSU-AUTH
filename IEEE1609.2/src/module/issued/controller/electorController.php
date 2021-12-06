<?php
/**
* System Monitoring
**/

namespace module\issued\controller;
use Exception;

class electorController
{
    public function indexAction()
    {
		// 인증서 조회
		$dir_list = array();
		exec("ls -lt /workdir/srcs/output/", $output);
		$output[0] = null;
		$output = array_filter($output);
		if(count($output)){
			foreach($output as $key => $val){
				$tmp = explode(" ", $val);
				$dir_list[] = $tmp[8];
			}	
		}

		return array(
			"dir_list"	=> $dir_list
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

		$issuer_cert = "/workdir/srcs/output/".$_POST["issuer_cert"]."/rootca.cert";

		$conf = array(
			 "cert_type"			=> "explicit"
			,"use_encryption_key"	=> $_POST["use_encryption_key"]
			,"issuer_cert_path"		=> $issuer_cert
			,"output_file_path"		=> "/workdir/srcs/output/".$_POST["issuer_cert"]."/elector/".$dirname."/elector.cert"
			,"cert_id"				=> array(
				 "type"		=> $_POST["cert_id_type"]
				,"value"	=> $_POST["cert_id_value"]
			)
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
		$fp = fopen("/var/www/scms2016gui/public/conf/elector.json", "w");
		fwrite($fp, $conf);
		fclose($fp);

		// run
		chdir("/workdir/srcs");
		exec("mkdir -p /workdir/srcs/output/".$_POST["issuer_cert"]."/elector/".$dirname);
		exec("python /workdir/srcs/cert_tool.py create-cert /var/www/scms2016gui/public/conf/elector.json 2>&1", $output);

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
		exec("zip -jr /var/www/scms2016gui/public/downloads/elector_".$dirname.".zip /workdir/srcs/output/".$_POST["issuer_cert"]."/elector/".$dirname);

		$result = array(
			 "result" => "success"
			,"download_path"	=> "/downloads/elector_".$dirname.".zip"
		);

		echo json_encode($result);
		die();
    }
}
?>
