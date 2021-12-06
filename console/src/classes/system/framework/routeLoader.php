<?php
/**
 * 라우트 로더
 *
 * /module 아래에 디렉토리를 참조하여 라우트 설정함
 * 디렉토리명이 _로 시작하면 라우팅에서 제외함.
 *
 * @author  daekyu.seo dkseo@pentasecurity.com
 * @copyright   2014 Penta Security
 *
 */

Namespace classes\system\framework;

class routeLoader
{

    // init
    public function __construct(  )
    {
        # 디렉토리 리스트 가져오기.
        $dir_list = getDir( ROOT_PATH . "module" );
        # 라우트 등록하기
        $this->route = $this->setRouting( $dir_list );
    }


    ## 라우트 등록하기
    private function setRouting( $dir_list )
    {
        // defult
        $route[] = "/";

        foreach ( $dir_list as $key => $val ){
            // '_'로 시작하면 제외
            if ( substr($val, 0, 1) == "_" ) {
                continue;
            }
            // ---
            else
            {
                $route[] = "/" . $val;
            }
        }

        return $route;
    }
}
