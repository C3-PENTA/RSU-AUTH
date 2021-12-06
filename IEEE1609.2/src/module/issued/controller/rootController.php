<?php
/**
* System Monitoring
**/

namespace module\issued\controller;
use Exception;

class rootController
{
    public function indexAction()
    {

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

		$conf = array(
			 "cert_type"			=> "explicit"
			,"output_file_path"		=> "/workdir/srcs/output/".$dirname."/rootca.cert"
			,"cert_id"				=> array(
				 "type"		=> $_POST["cert_id_type"]
				,"value"	=> $_POST["cert_id_value"]
			)
			,"craca_id"				=> "000000"
			,"crl_series"			=> (int)0
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
		$fp = fopen("/var/www/scms2016gui/public/conf/rootca.json", "w");
		fwrite($fp, $conf);
		fclose($fp);

		// run
		chdir("/workdir/srcs");
		mkdir("/workdir/srcs/output/".$dirname);
		exec("python /workdir/srcs/cert_tool.py create-cert /var/www/scms2016gui/public/conf/rootca.json 2>&1", $output);

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
		exec("zip -jr /var/www/scms2016gui/public/downloads/rootca_".$dirname.".zip /workdir/srcs/output/".$dirname."/rootca.cert");

		$result = array(
			 "result" => "success"
			,"download_path"	=> "/downloads/rootca_".$dirname.".zip"
		);

		echo json_encode($result);
		die();
    }
}
?>
