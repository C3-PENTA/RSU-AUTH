<?php
/**
 * 모듈 로더
 *
 * /module 아래에 디렉토리를 참조하여 모듈리스트 생성
 *
 * @author  daekyu.seo dkseo@pentasecurity.com
 * @copyright   2014 Penta Security
 *
 */

Namespace classes\system\framework;

class moduleLoader
{

    // init
    public function __construct(  )
    {
        # 디렉토리 리스트 가져와 모듈로 등록하기.
        $dir_list = $this->getDirList( ROOT_PATH . "module" );
        $this->module_name = $this->setModuleList( $dir_list );
    }

    ## /module 내 디렉토리 리스트 가져오기
    private function getDirList ( $path )
    {
        $module = getDir( $path );
        return $module;
    }

    ## 모듈 등록
    private function setModuleList( $list )
    {
        return $list;
    }

}
