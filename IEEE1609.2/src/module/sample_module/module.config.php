<?php
return array(
    "routes" => array(
        "route" => "/sample_module",

        "constraints" => array(
            "module",
            "controller",
            "action",
            "arg1",
            "arg2",
            "arg3",
        ),

        "defaults" => array(
            "controller"    => "index",
            "action"        => "index",
        ),


        "view_manager" => array(
            //"layout"    	=> __DIR__ . DS . "view" . DS . "layout" . DS . "layout.phtml",
            "html_path"   	=> __DIR__ . DS . "view" . DS . "html",
            //"error"     => __DIR__ . DS . "view" . DS . "error" . DS . "error.phtml",
        ),

        "client_script" => array(
            "css"   => array(
				 "/assets/adminlte/plugins/daterangepicker/daterangepicker-bs3.css"
            ),
            "head_js"   => array(
				 "/assets/adminlte/plugins/daterangepicker/moment.js"
				,"/assets/adminlte/plugins/daterangepicker/daterangepicker.js"
				,"/assets/adminlte/plugins/datepicker/bootstrap-datepicker.js"
            ),
            "title" => array(
            ), 
            "meta_tag" => array(
            ),
        ),
    ),
);
