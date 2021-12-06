<?php

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
