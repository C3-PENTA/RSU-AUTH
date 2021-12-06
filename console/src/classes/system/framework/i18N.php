<?php
/* i18N 처럼 컨텐츠 관리 */
use classes\user\db\mlog;

function c2ms( $idx )
{
    $fw = new classes\system\framework\dkFrameWork;
    $db = new mlog;

    $module = $fw->info->module;
    $controller = $fw->info->controller;
    $action = $fw->info->action;

    if($controller == "u" || $controller == "c"){
	$controller = "index";
    }

    $query = "
        SELECT
            CONTENTS
        FROM
            C2MS_en
        WHERE
            MODULE = '".$module."' AND
            CONTROLLER = '".$controller."' AND
            ACTION = '".$action."' AND
            CONTENTS_IDX = '".$idx."'
    ";
    $content = $db->simple_query($query);
    $content = htmlspecialchars_decode($content);

    return $content;
}
