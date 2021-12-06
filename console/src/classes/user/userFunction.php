<?php

/**
* convert domain_type
**/
function convert_domain_type($domain_type)
{
	switch($domain_type)
	{
		case "car":		$c_domain_type = "Smart Car"; break;
		case "iot":		$c_domain_type = "Smart Home"; break;
		case "factory":	$c_domain_type = "Smart Factory"; break;
		case "ssl":		$c_domain_type = "SSL/TLS"; break;
		default: 		$c_domain_type = "None"; break;
	}

	return $c_domain_type;
}

/**
* convert Lot number
**/
function convert_lot_number($data)
{
	$d = strtoupper("00".dechex($data));

	return chunk_split($d, $len=2, $end=" ");

}

function convert_bin2hex($data)
{
	$d = strtoupper(bin2hex(base64_decode($data)));
	return chunk_split($d, $len=2, $end=" ");
}


/**
* show error modal
**/
function show_system_error($code)
{
	$template = file_get_contents(ROOT_PATH . "public/modals/system_error.phtml");	
	echo $template;
}


/**
* history.back
**/
function go_back($val)
{
	echo "
		<script>window.history.back(".$val.");</script>
	";
}
