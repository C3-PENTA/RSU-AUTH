<?php
namespace module\_default\model;

class fileStructure
{

	public function __construct()
	{
		$list = $this->getLs(ROOT_PATH);
		debug($list);
	}

	// directory listing
	private function getLs($path){
		try
		{
			if(0){
				$list = scandir($path);	
			}else{
				throw new Exception('test');	
			}
		} catch(Exception $e) { dkException($e); }
			
	}
}
