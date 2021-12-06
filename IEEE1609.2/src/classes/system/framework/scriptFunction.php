<?php
/**
 * 프레임웍 함수들
 *
 * - 프레임웍에 상속됨
 *
 * @author  daekyu.seo dkseo@pentasecurity.com
 * @copyright   2014 Penta Security
 *
 */

Namespace classes\system\framework;


class scriptFunction
{
    public function location( $path )
    {
        header("Location: $path");
        exit;
    }

    public function href( $path )
    {
        echo "<script>window.location.href = '" . $path . "'</script>";
        exit;
    }

    public function alert( $msg )
    {
        echo "<script>alert(' " . $msg . " ')</script>";
    }

    public function js_script( $q )
    {
        echo "<script>" . $q . "</script>";
    }
}