<?php
/**
 * default 컨트롤러
 *
 * @author  daekyu.seo
 * @copyright   dkseo
 *
 */

namespace module\_default\controller;

class indexController
{
    public function indexAction()
    {


		return array(
			"foo"	=> "bar"
		);
    }

	public function getCracaIdAction()
	{
		$root_ca_path = "/workdir/srcs/rootca.cert";
		$root_cert = file_get_contents($root_ca_path);
		$root_cert = preg_replace('/(\W)(\d+)(\W)/', '\\1"\\2"\\3', $root_cert);
		$root_cert = json_decode($root_cert, true);

		$cert_hash_id3 = $root_cert["cert_hash_id3"];

		$result = array(
			"cert_hash_id3" => $cert_hash_id3	
		);	

		echo json_encode($result);

		die("");
	}

	public function getCertificateIdAction()
	{
		$obe_enrol_path = "/workdir/srcs/output/".$_POST["path"];
		$obe_cert = file_get_contents($obe_enrol_path);
		$obe_cert = preg_replace('/(\W)(\d+)(\W)/', '\\1"\\2"\\3', $obe_cert);
		$obe_cert = json_decode($obe_cert, true);

		$cert_hash_id8 = substr($obe_cert["cert_hash_id"], -16);

		$result = array(
			"cert_hash_id8" => $cert_hash_id8	
		);	

		echo json_encode($result);

		die("");
	}
}
?>
