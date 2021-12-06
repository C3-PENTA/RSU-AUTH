<?php
/**
 * URI 로더
 *
 * - 접속한 URI 구조 분석
 *
 * @author  daekyu.seo dkseo@pentasecurity.com
 * @copyright   2014 Penta Security
 *
 */

 Namespace classes\system\framework;

 class uriLoader
 {

    // init
    public function __construct()
    {
        // url
        $redirect_uri = (isset($_SERVER["REDIRECT_URL"]))?$_SERVER["REDIRECT_URL"] : "/";
        // query
        $redirect_query = ($redirect_uri == "/")? @$_SERVER["REDIRECT_QUERY_STRING"] : $_SERVER["QUERY_STRING"];

        $tmp_uri = array_filter( explode("/", $redirect_uri));
        foreach ( $tmp_uri as $key => $val ){
            $tmp_sort_uri[] = $val;
        }

        if ( isset($tmp_sort_uri) ) {
            // uri 트리를 배열로 변환
            $this->uri = $tmp_sort_uri;
            // 현재 uri
            $this->now_uri = $this->uri[0];

        }else{
            // uri 트리를 배열로 변환
            $this->uri = array("");
            // 현재 uri
            $this->now_uri = "_default";
        }
    }
 }