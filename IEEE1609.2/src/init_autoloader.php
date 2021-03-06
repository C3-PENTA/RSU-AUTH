<?php

# session start
session_start();
# enc type
header("Content-Type: text/html; charset=UTF-8");

#force setting timestemp
ini_set("date.timezone", "Asia/Seoul");


### Define Variable
define ('DS', DIRECTORY_SEPARATOR);
define ('ROOT_PATH', __DIR__ . DS);
//---
define ('CLASS_PATH', ROOT_PATH . "classes" . DS);
define ('SYSTEM_CLASS_PATH', ROOT_PATH . "classes" . DS . "system" . DS);
//---
define ('MODULE_PATH', ROOT_PATH . "module" . DS);


use classes\system\framework\dkFrameWork;

## framework 내장함수 선언
include SYSTEM_CLASS_PATH . "framework" . DS . "dkFunction.php";

error_reporting(E_ALL);
ini_set("display_errors", 0); 

## 실서비스에 에러 화면을 안찍으려면 아래 두줄 주석하기
## apache2 설정에서 error file을 찍게 하면 됨.
set_error_handler("dkErrorHandler");
register_shutdown_function("dkErrorCapture");


## 사용자 정의 함수 선언
try
{
	if(is_file(CLASS_PATH . "user" . DS . "userFunction.php")){
		include CLASS_PATH . "user" . DS . "userFunction.php";
	}else{
		throw new Exception('
			사용자 정의 함수가 없는데 괜찮아요? 
			~/classes/user/userFunction.php를 확인하세요.
		');	
	}
}
catch(Exception $e) { dkException($e); }


## 프레임워크 구동
$fw = new dkFrameWork;

## Run
$fw->RunningFW();
