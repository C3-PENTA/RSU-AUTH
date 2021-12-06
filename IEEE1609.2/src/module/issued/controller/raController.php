<?php
/**
* System Monitoring
**/

namespace module\issued\controller;
use Exception;

class raController
{
    public function indexAction()
    {
		// 인증서 조회
		$dir_list = array();
		exec("ls /workdir/srcs/output/*/ica/*", $output);
		if(count($output)){
			foreach($output as $key => $val){
				if(substr($val,0,1) != "/")
					continue;

				$tmp = str_replace(":", "", $val);
				$tmp = str_replace("/workdir/srcs/output/", "", $tmp);
				$dir_list[] = $tmp;
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

		foreach($_POST["cert_issue_permissions"] as $key => $val)
		{
			if($val[0] == "EXPLICIT")
			{
				$explicit = array( array(
					 "psid"			=> (int)$val[5]	
					,"ssp_range" 	=> array(
						 "range_type"	=> "OPAQUE"
						,"opaque"		=> $val[6]
					)
				));

				$cert_issue_permissions[] = array(
					 "subject_permissions" => array(
						 "permission_type"	=> $val[0]
						,"explicit"				=> $explicit
					)
					,"min_chain_depth"		=> (int)$val[1]
					,"chain_depth_range"	=> (int)$val[2]
					,"eetype"				=> array(
						 "app"		=> (bool)$val[3]	
						,"enrol"	=> (bool)$val[4]	
					)
				);
			}
			else if($val[0] == "ALL")
			{
				$cert_issue_permissions[] = array(
					 "subject_permissions" => array(
						"permission_type"	=> $val[0]
					)
					,"min_chain_depth"		=> (int)$val[1]
					,"chain_depth_range"	=> (int)$val[2]
					,"eetype"				=> array(
						 "app"		=> (bool)$val[3]	
						,"enrol"	=> (bool)$val[4]	
					)
				);
			}
		}

		$issuer_cert = "/workdir/srcs/output/".$_POST["issuer_cert"]."/ica.cert";

		$conf = array(
			 "cert_type"			=> "explicit"
			,"issuer_cert_path"		=> $issuer_cert
			,"output_file_path"		=> "/workdir/srcs/output/".$_POST["issuer_cert"]."/ra/".$dirname."/ra.cert"
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
			,"cert_issue_permissions" => $cert_issue_permissions
		);

		$conf = json_encode($conf, JSON_PRETTY_PRINT);

		// create conf file
		$fp = fopen("/var/www/scms2016gui/public/conf/ra.json", "w");
		fwrite($fp, $conf);
		fclose($fp);

		// run
		chdir("/workdir/srcs");
		exec("mkdir -p /workdir/srcs/output/".$_POST["issuer_cert"]."/ra/".$dirname);
		exec("python /workdir/srcs/cert_tool.py create-cert /var/www/scms2016gui/public/conf/ra.json 2>&1", $output);

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
		exec("zip -jr /var/www/scms2016gui/public/downloads/ra_".$dirname.".zip /workdir/srcs/output/".$_POST["issuer_cert"]."/ra/".$dirname);

		$result = array(
			 "result" => "success"
			,"download_path"	=> "/downloads/ra_".$dirname.".zip"
		);

		echo json_encode($result);
		die();
    }
}
?>
