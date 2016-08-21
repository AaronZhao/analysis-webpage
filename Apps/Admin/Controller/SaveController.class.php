<?php
namespace Admin\Controller;
use Think\Controller;
// use Common\Org\Snoopy;
use Common\Org\ExtractContent;
class SaveController extends Controller{
    //登录页面
    public function index(){
        $m_obj = M("site");
        $res = $m_obj->select();
        var_dump( $res);exit;
        
//         $snoopy = new Snoopy();
//         $res = $snoopy->fetchlinks("http://zhuanlan.sina.com.cn/");
        if( !empty( $url ) )
        {
            $extractObj = new ExtractContent();
            $extractObj->extract( file_get_contents($url));
    //         echo $extractObj->getTitle();
            echo $extractObj->asHtml();
        }
    }
}

?>