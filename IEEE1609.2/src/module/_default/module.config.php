<?php

return array(
    "routes" => array(
        /**
         * 현재 모듈 위치 알기 위한 참고용.
        */
        "route" => "/",

        /**
         * REQUEST URI에서 구분자는 "/" 이고, 구분자로 구분했을 때(explode) 배열의 순서대로 설정한다.
         * 배열의 갯수는 무한대이고, 순서도 마음대로 정의할 수 있다.
         * controller와 action을 정의하지 않을시 defaults의 내용을 그대로 따라 간다.
        */
        "constraints" => array(
            "controller",
            "action",
            "arg1",
            "arg2",
        ),

        /**
         * 컨트롤러와 액션의 기본 설정
         * constraints의 항목과 이름이 동일해야 함 (대소문자 구분)
         * System에서 controller 이름을 기준으로 파일과 클래스명을 찾음.
         * 그러므로 반드시 같은 파일명과 클래스명으로 선언해야 함.
         * 선언된 action명으로 controller내 함수명을 찾아가므로
         * 반드시 선언한 action명과 동일하게 함수를 선언해야 함.
        */
        "defaults" => array(
            "controller"    => "index",
            "action"        => "index",
        ),

        /**
         * 컨트롤러 mapping 설정
         * 일부 컨트롤러는 기존의 컨트롤러를 사용할 수 있도록 하는 설정
         * 컨트롤러 명 => "매핑할 컨트롤러 명"
        "mapping" => array(
            "mapping"   => "index",
            "mapping1"   => "index",
            "mapping2"   => "index",
        ),
        */

        /**
         * 기본 뷰 페이지임.
         * 사용자 정의 모듈에서 view_manager를 설정하지 않으면 기본으로 home의 view_manager를 따라감.
         * 모듈별 뷰를 따로 가져가고 싶으면 해당 모듈의 module.config.php에 별도 등록해야 함.
        */
        "view_manager" => array(
            "layout"    => __DIR__ . DS . "view" . DS . "layout" . DS . "layout.phtml",
            "html_path"   => __DIR__ . DS . "view" . DS . "html",
            "error"     => __DIR__ . DS . "view" . DS . "error" . DS . "error.phtml",
        ),

        /**
         * css, javascript include
         * _default에 적용된 css와 javascript는 모든 페이지에 적용되는 공통사항임.
         * 모듈별로 include를 설정하면 공통include에 추가하여 설정됨
         * meta_tag에서 default는 모든 페이지에 적용됨.
         * meta_tag에서 개별 페이지는 routing을 명시하면 됨..
         * meta_tag는 반드시 작은 따옴표로 묶어야... 내부에 또 따옴표 명시할때는 \"로 함.
		 * javascript는 html 최하단에 출력되도록 셋팅함 $include_js
        */
        "client_script" => array(
            "css"   => array(
				 "/assets/adminlte/bootstrap/css/bootstrap.min.css"
				,"/assets/adminlte/plugins/select2/select2.min.css"
				,"/assets/adminlte/font-awesome.min.css"
				,"/assets/adminlte/ionicons.min.css"
				,"/assets/adminlte/plugins/datatables/dataTables.bootstrap.css"
				,"/assets/adminlte/dist/css/AdminLTE.min.css"
				,"/assets/adminlte/dist/css/skins/_all-skins.min.css"
				,"/assets/css/common.css"
            ),
            "head_js"    => array(
				 "/assets/adminlte/plugins/jQuery/jQuery-2.2.0.min.js"
                ,"/assets/adminlte/plugins/datatables/jquery.dataTables.min.js"
                ,"/assets/adminlte/plugins/datatables/dataTables.bootstrap.min.js"
            ),
			"js"	=> array(
				 "/assets/adminlte/bootstrap/js/bootstrap.min.js"
				,"/assets/adminlte/plugins/slimScroll/jquery.slimscroll.min.js"
				,"/assets/adminlte/plugins/fastclick/fastclick.js"
				//,"/assets/adminlte/dist/js/app.min.js"
				,"/assets/adminlte/dist/js/app.js"
				,"/assets/adminlte/dist/js/demo.js"
				,"/assets/adminlte/plugins/select2/select2.min.js"
				,"/assets/js/common.js"
			),
            "title" => array(
                "default" => "SCMS2016 Tool",
                //"/index/index" => "",
                //"/c/index" => "",
            ), 
            "meta_tag" => array(
                "default" => array(
                    "charset='utf8'",
                    "http-equiv='X-UA-Compatible' content='IE=edge'",
					"content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'"
                ),

			/*
                "/index/index" => array(																
                    "name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no'",
                    "name='description' content='Cloudbric provides effortless, first-class website security for free. We can protect against hacking, DDos, identity theft, web spider and SQL injection.'",
					"name='keywords' content='website security, website protection, DDos protection, identity theft protection, cloudbric'",
                ),
                "/c/index" => array(																
                    "name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no'",
                    "name='description' content='Cloudbric provides effortless, first-class website security for free. We can protect against hacking, DDos, identity theft, web spider and SQL injection.'",
					"name='keywords' content='website security, website protection, DDos protection, identity theft protection, cloudbric'",
                ),
                "/u/index" => array(																
                    "name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no'",
                    "name='description' content='Cloudbric provides effortless, first-class website security for free. We can protect against hacking, DDos, identity theft, web spider and SQL injection.'",
					"name='keywords' content='website security, website protection, DDos protection, identity theft protection, cloudbric'",
                ),
            */
            ),
        ),
    ),
);
