<?php
/**
 * dkFramework 구동을 위한 내장 함수 선언
 *	- 일부 함수를 변경하면 framework 동작이 안 할 수 있으므로 신중하세요.
 *
 * @author  daekyu.seo dkseo@pentasecurity.com
 * @copyright   2014 Penta Security
 *
 */

// Class Loader
// new를 통해 클래서 선언시 디렉토리 구조와 같은 namespace를 유지해야 함.
function __autoload( $className )
{
    $class_path = str_replace("\\", DS, $className . ".php");

	$only_className = explode("\\", $className);
	$only_className = array_pop($only_className);

	try
	{
		if( !is_file(ROOT_PATH.$class_path) ){
			if(!class_exists($only_className)){
				throw new Exception("File : ". $class_path."이 없어요.");
			}else{
				//spl_autoload_register("new \\".$only_className);
				throw new Exception($only_className."은 내장객체인것 같아요. \"\\$only_className\"(으)로 사용하세요.");
			}
		} else {
			require_once ROOT_PATH . $class_path;
		}
	} catch (Exception $e) { dkException($e); }

}

// 디렉토리명 가져오기
function getDir( $path )
{
    $dir_list = scandir( $path );
    foreach ( $dir_list as $key => $val ){
        //echo $val;
        if ( $val == "." || $val == '..' || !is_dir( MODULE_PATH . $val ) ){
            continue;
        }
        else{
            $module[] = $val;
        }
    }   
    return $module;
}


// debug
function debug( $array )
{
    echo "<xmp>";
    print_r($array);
    echo "</xmp>";
}


// throw exception
function dkException($msg){

	$code 		= $msg->getCode();
	$message 	= $msg->getMessage();
	$file 		= $msg->getFile();
	$line 		= $msg->getLine();
	$trace 		= $msg->getTraceAsString();

	$error_code = array(
		 "0" 	=> "User defined throw Exception"
		,"2"	=> "E_WARNING"
		,"8"	=> "E_NOTICE"
		,"256" 	=> "E_USER_ERROR"
		,"512" 	=> "E_USER_WARNING"
		,"1024" => "E_USER_NOTICE"
		,"4096" => "E_RECOVERABLE_ERROR"
		,"8191" => "E_ALL"
	);

	echo "<div style='padding:10px; background-color:#e8e8e8'>";
	echo "<h3 style='color:red'>".$message."</h3>";
	echo "<h4>type : ".$error_code[$code]."</h4>";
	echo "<h4>file : ".$file." (line. ".$line.")</h4>";
	echo "<hr />";
    $count = 0;
	$rtn = "";
    foreach ($msg->getTrace() as $frame) {
        $args = "";
        if (isset($frame['args'])) {
            $args = array();
            foreach ($frame['args'] as $arg) {
                if (is_string($arg)) {
                    $args[] = "'" . $arg . "'";
                } elseif (is_array($arg)) {
                    $args[] = "Array";
                } elseif (is_null($arg)) {
                    $args[] = 'NULL';
                } elseif (is_bool($arg)) {
                    $args[] = ($arg) ? "true" : "false";
                } elseif (is_object($arg)) {
                    $args[] = get_class($arg);
                } elseif (is_resource($arg)) {
                    $args[] = get_resource_type($arg);
                } else {
                    $args[] = $arg;
                }   
            }   
            $args = join(", ", $args);
        }
        $rtn .= sprintf( "#%s %s (%s): %s(%s)\n",
                                 $count,
                                 @$frame['file'],
                                 "line. ".@$frame['line'],
                                 @$frame['function'],
                                 $args );
        $count++;
    }

	debug($rtn);

	echo "<hr />";
	echo "</div>";
}

function dkErrorHandler($errno, $errstr, $errfile, $errline, $errcontext)
{
	try
	{
		if (!(error_reporting() & $errno)) {
			// This error code is not included in error_reporting
		}else {
			throw new Exception($errstr, $errno);
		}

	} catch (Exception $e) { dkException($e); }


}

function dkErrorCapture()
{
	$lasterror = error_get_last();

	if(!$lasterror) return ;

	switch ($lasterror['type'])
	{
		case E_ERROR:				$error_type = "E_ERROR"; break;
		case E_CORE_ERROR:			$error_type = "E_CORE_ERROR"; break; 
		case E_COMPILE_ERROR:		$error_type = "E_COMPILE_ERROR"; break; 
		case E_USER_ERROR:			$error_type = "E_USER_ERROR"; break; 
		case E_RECOVERABLE_ERROR:	$error_type = "E_RECOVERABLE_ERROR"; break; 
		case E_CORE_WARNING:		$error_type = "E_CORE_WARNING"; break; 
		case E_COMPILE_WARNING:		$error_type = "E_COMPILE_WARNING"; break; 
		case E_PARSE:				$error_type = "E_PARSE"; break; 
	}

	$tmp = explode("Stack trace:", $lasterror["message"]);
	$lasterror["message"] = $tmp[0];
	$lasterror["trace"] = @$tmp[1];
	
	if(isset($lasterror) && $lasterror != ""){
		echo "<div style='padding:10px; background-color:#e8e8e8'>";
		echo "<h3 style='color:red'>".$lasterror["message"]."</h3>";
		echo "<h4>type : ".@$error_type."</h4>";
		echo "<h4>file : ".@$lasterror["file"]." (line. ".@$lasterror["line"].")</h4>";
		echo "<pre>".@nl2br($lasterror["trace"])."</pre>";
		echo "</div>";
	}
}


function getExceptionTraceAsString($exception) {
    $rtn = "";
    $count = 0;
    foreach ($exception->getTrace() as $frame) {
        $args = "";
        if (isset($frame['args'])) {
            $args = array();
            foreach ($frame['args'] as $arg) {
                if (is_string($arg)) {
                    $args[] = "'" . $arg . "'";
                } elseif (is_array($arg)) {
                    $args[] = "Array";
                } elseif (is_null($arg)) {
                    $args[] = 'NULL';
                } elseif (is_bool($arg)) {
                    $args[] = ($arg) ? "true" : "false";
                } elseif (is_object($arg)) {
                    $args[] = get_class($arg);
                } elseif (is_resource($arg)) {
                    $args[] = get_resource_type($arg);
                } else {
                    $args[] = $arg;
                }   
            }   
            $args = join(", ", $args);
        }
        $rtn .= sprintf( "#%s %s(%s): %s(%s)\n",
                                 $count,
                                 $frame['file'],
                                 $frame['line'],
                                 $frame['function'],
                                 $args );
        $count++;
    }
    return $rtn;
}

// loading only contents
function only_contents( $var = null )
{
    $uri = $_SERVER["REQUEST_URI"];
    $route = explode("/", $uri);
    
    $module     = $route[1];
    $controller = $route[2];
    $view       = $route[3];

    $path = ROOT_PATH . "module/".$module."/view/html/".$controller."/".$view.".phtml";

    // 변수화
    if( is_array($var) ){
        foreach ( $var as $key => $val ){
            ${$key} = $val;
        }
    }   


    ob_start();
    include( $path );
    $contents = ob_get_clean();

    return $contents;
}

