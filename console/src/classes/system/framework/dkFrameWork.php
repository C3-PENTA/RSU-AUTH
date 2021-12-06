<?php
/**
 * 프레임웍 구동시 필요한 부속들
 *
 * - 모듈로드
 * - 라우트로드
 * - URI 설정
 *
 * @author  daekyu.seo dkseo@pentasecurity.com
 * @copyright   2014 Penta Security
 *
 */

Namespace classes\system\framework;
use classes\system\framework\moduleLoader;
use classes\system\framework\routeLoader;
use classes\system\framework\uriLoader;
use classes\system\framework\scriptFunction;
use \Exception;


class dkFrameWork
{

    public $module;
    public $route;

    ## init
    public function __construct()
    {
        // 모듈 로드
        $this->LoadModule();
        // 라우트 로드
        $this->LoadRoute();
        // URI 셋팅
        $this->SettingURI();

        // default 모듈의 config파일 load
        $this->LoadDefaultModuleConfig();

        // 변수 만들기
        $this->BuildValue();

        // 현재 접속한 URI의 환경 설정 Load
        // 모듈, 컨트롤러, 액션, 뷰 등
        $this->LoadUriModuleConfig();
    }


    #################################
    ## 모듈 로드
    #################################
    private function LoadModule()
    {
        $module = new moduleLoader;

        // 모듈명 등록
        $this->setModuleName( $module );
        // 모듈명으로 object 등록
        $this->createModuleObject();
    }

    // 모듈명 등록
    private function setModuleName( $module )
    {
	$this->module = new \stdClass();
        $this->module->name = $module->module_name;
    }

    // 모듈명으로 object 등록
    private function createModuleObject()
    {
        foreach ( $this->module->name as $key => $val ){
            $this->module->$val = new \stdClass();
            $this->module->$val->path = MODULE_PATH . $val;
        };
    }


    #################################
    ## 라우트 로드
    #################################
    private function LoadRoute()
    {
        $route = new routeLoader;
        $this->route = new \stdClass();
        $this->route->name = $route->route;
    }


    #################################
    ## URI 설정
    #################################
    private function SettingURI()
    {
        $uri = new uriLoader;
        $this->uri = new \stdClass();
        $this->uri->list = $uri->uri;
        $this->uri->now_uri = $uri->now_uri;
    }


    #################################
    ## default 모듈의 config 파일 Load
    #################################
    private function LoadDefaultModuleConfig()
    {
        $this->default_module_config = require $this->module->_default->path . DS . "module.config.php";
    }


    #################################
    ## 현재 URI의 모듈 설정 Load
    #################################
    private function LoadUriModuleConfig()
    {
        ### 모듈 Load
        // 현재 uri가 모듈로 등록되어 있으면 모듈 config 파일 로드
        if ( in_array( $this->uri->now_uri, $this->module->name ) ) {
            $now_uri = $this->uri->now_uri;
            $module_config = require $this->module->$now_uri->path . DS . "module.config.php";
            $this->info = new \stdClass();
            $this->info->module = $this->uri->now_uri;
        }
        // 그렇지 않으면 default 설정 로드
        else {
            $module_config = require $this->module->_default->path . DS . "module.config.php";
            $this->info = new \stdClass();
            $this->info->module = "_default";
        }

        ### 컨트롤러, 액션, arg... Load
        foreach ( $module_config["routes"]["constraints"] as $key => $val ){
            $this->info->$val = @$this->uri->list[$key];
        }

        ### 선언 안한 항목은 config에서 미리 선언한 default값으로 셋팅
        foreach ( $this->info as $key => $val ){
            if ( !$val ) {
                $this->info->$key = @$module_config["routes"]["defaults"][$key];
            }
        }

        ### view 설정
        ### default view 설정 후, 현재 모듈 view로 덮어씌움
        $this->info->view = $this->default_module_config["routes"]["view_manager"];
        $this->info->view["layout"] = ( isset($module_config["routes"]["view_manager"]["layout"]) )?$module_config["routes"]["view_manager"]["layout"] : $this->info->view["layout"];
        $this->info->view["html_path"] = ( isset($module_config["routes"]["view_manager"]["html_path"]) )?$module_config["routes"]["view_manager"]["html_path"] : $this->info->view["html_path"];
        $this->info->view["error"] = ( isset($module_config["routes"]["view_manager"]["error"]) )?$module_config["routes"]["view_manager"]["error"] : $this->info->view["error"];

        ### css, js, meta_tag, title include
        $this->info->css = (@is_array($this->default_module_config["routes"]["client_script"]["css"]))?$this->default_module_config["routes"]["client_script"]["css"] : array();
        $this->info->head_js = (@is_array($this->default_module_config["routes"]["client_script"]["head_js"]))?$this->default_module_config["routes"]["client_script"]["head_js"] : array();
        $this->info->js = (@is_array($this->default_module_config["routes"]["client_script"]["js"]))?$this->default_module_config["routes"]["client_script"]["js"] : array();
        $this->info->title = (@is_array($this->default_module_config["routes"]["client_script"]["title"]))?$this->default_module_config["routes"]["client_script"]["title"] : array();
        $this->info->meta_tag = (@is_array($this->default_module_config["routes"]["client_script"]["meta_tag"]))?$this->default_module_config["routes"]["client_script"]["meta_tag"] : array();

        ### default 설정 후, 현재 모듈 추가
        $this->info->css = ($this->info->module != '_default' && @is_array($module_config["routes"]["client_script"]["css"]))? array_merge($this->info->css, $module_config["routes"]["client_script"]["css"]) : $this->info->css;
        $this->info->head_js = ($this->info->module != '_default' && @is_array($module_config["routes"]["client_script"]["head_js"]))? array_merge($this->info->head_js, $module_config["routes"]["client_script"]["head_js"]) : $this->info->head_js;
        $this->info->js = ($this->info->module != '_default' && @is_array($module_config["routes"]["client_script"]["js"]))? array_merge($this->info->js, $module_config["routes"]["client_script"]["js"]) : $this->info->js;
        $this->info->title = ($this->info->module != '_default' && @is_array($module_config["routes"]["client_script"]["title"]))? array_merge($this->info->title, $module_config["routes"]["client_script"]["title"]) : $this->info->title;
        $this->info->meta_tag = ($this->info->module != '_default' && @is_array($module_config["routes"]["client_script"]["meta_tag"]))? array_merge($this->info->meta_tag, $module_config["routes"]["client_script"]["meta_tag"]) : $this->info->meta_tag;

        // view 없음
        if($module_config["routes"]["view_manager"] == "none")
            $this->info->view = "none";
    }


    #################################
    ## 변수 만들기
    #################################
    public function BuildValue()
    {
        /**
         * Function Load
         */
        $this->script = new scriptFunction;

        /**
         * $_SERVER
         */
        $this->value = new \stdClass;
        $this->value->server = new \stdClass;
        $this->value->server->protocal = ($_SERVER["SERVER_PORT"] != "443")?"http://":"https://";
        $this->value->server->host = $this->value->server->protocal . $_SERVER["HTTP_HOST"];
        $this->value->server->referer = (isset($_SERVER["HTTP_REFERER"]))?str_replace( $this->value->server->host, "", $_SERVER["HTTP_REFERER"]) : NULL;
        $this->value->server->remote_addr = $_SERVER["REMOTE_ADDR"];



        //debug($_SERVER);

        /**
         * $_POST
         */
        $this->value->post = new \stdClass();

        if(count($_POST)){
            foreach ( $_POST as $key => $val ){
                $this->value->post->$key = $val;
            }
        }

    }


    #################################
    ## 프레임워크 시작
    #################################
    public function RunningFW()
    {
        if ( $this->info->controller != "favicon.ico" ) {
            // 컨트롤러 로드
            $tmpController = "\module\\" . $this->info->module . "\controller\\" . $this->info->controller . "Controller";
			try
			{
				if(class_exists($tmpController)){
            		$controller = new $tmpController;
				}else{
					throw new Exception("Object : ".$tmpController."가(이) 존재하지 않아요.");	
				}
			} catch (Exception $e) {dkException($e); exit;} 

            // action 시작
            $tmpAction = $this->info->action . "Action";
			try
			{
				if(method_exists($controller, $tmpAction))	
            		$action = $controller->$tmpAction();
				else
					throw new Exception("Method : ".$tmpController."->".$tmpAction."이(가) 존재하지 않아요.");
			} catch (Exception $e) { dkException($e); exit; }

            // action 에서 리턴한 배열들 변수화
            if( is_array($action) ){
                foreach ( $action as $key => $val ){
                    ${$key} = $val;
                }
            }
        }

        // view 로드
        if ( $this->info->view != "none" ) {
            // css 로드
            if( is_array($this->info->css) ){
                foreach ( $this->info->css as $key => $val ){
                    $css_list[] = "<link href='" . $val . "' media='screen' rel='stylesheet' type='text/css'>";
                }
            }
            $css_list = @join("\n", $css_list);

            // head_js 로드
            if( is_array($this->info->head_js) ){
                foreach ( $this->info->head_js as $key => $val ){
                    $head_js_list[] = "<script type='text/javascript' src='" . $val . "'></script>";
                }
            }
            $head_js_list = @join("\n", $head_js_list);

            // js 로드
            if( is_array($this->info->js) ){
                foreach ( $this->info->js as $key => $val ){
                    $js_list[] = "<script type='text/javascript' src='" . $val . "'></script>";
                }
            }
            $js_list = @join("\n", $js_list);

            // title 로드
            if( is_array($this->info->title) ){
                foreach ( $this->info->title as $key => $val ){
                    $title_tag_for_module = ( $this->info->module == "_default" )? "": "/" . $this->info->module;
                    $title_tag_each_page = $title_tag_for_module."/".$this->info->controller."/".$this->info->action;

                    if ( $key == "default" ){
                        $title_tag[] = $val;
                    }else if( $key == $title_tag_each_page ){
                        $title_tag[] = $val;
                    }
                }


            }
            $title_tag_list = "<title>" . @join("", $title_tag) . "</title>";

            // meta tag 로드
            if( is_array($this->info->meta_tag) ){
                foreach ( $this->info->meta_tag as $key => $val ){
                    $meta_tag_for_module = ( $this->info->module == "_default" )? "": "/" . $this->info->module;
                    $meta_tag_each_page = $meta_tag_for_module."/".$this->info->controller."/".$this->info->action;

                    if ( $key == "default" ){
                        for ($i=0; $i<count($val); $i++){ 
                            $meta_tag[] = "<meta ".($val[$i])." />";
                        }
                    }else if( $key == $meta_tag_each_page ){
                        for ($i=0; $i<count($val); $i++){ 
                            $meta_tag[] = "<meta ".($val[$i])." />";
                        }
                    }
                }
            }
            $meta_tag_list = @join("\n", $meta_tag);

            ob_start();
            echo "\n" . $title_tag_list . "\n" . $meta_tag_list . "\n\n" . $css_list. "\n\n" . $head_js_list . "\n\n";
            $include_script = ob_get_clean();
			if(ob_get_length() > 0) ob_end_clean();

            ob_start();
            echo "\n". $js_list . "\n";
            $include_js = ob_get_clean();
			if(ob_get_length() > 0) ob_end_clean();


            // contents 로드
            $contents_path = @$this->info->view["html_path"] . DS . $this->info->controller;
            $contents_path .= DS . $this->info->action . ".phtml";


            // error catch
			try
			{
				if ( !is_file($contents_path) ){
					throw new Exception($contents_path."에 view 파일이 존재하지 않아요.");	
	//                echo "<br>error : view 파일이 없음. ($contents_path)<br><br>";
					$contents = "";
				}else{
					ob_start();
					include( $contents_path );
					$contents = ob_get_clean();
					if(ob_get_length() > 0) ob_end_clean();
				}
			} catch (Exception $e) { dkException($e); exit; }


            // layout Load
            $layout = @$this->info->view["layout"];
            // error catch
			try
			{
				if ( !is_file($layout) ){
					echo "<br>error : layout 파일이 없음. ($layout)<br><br>";
				}else{
					$layout = require_once $layout;
				}
			} catch (Exception $e) { dkException($e); exit; }
        }
    }
}
