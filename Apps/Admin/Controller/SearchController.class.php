<?php
namespace Admin\Controller;
use Think\Controller;
// use Common\Org\Snoopy;
use Common\Org\ExtractContent;
class SearchController extends Controller{
    //登录页面
    public function index(){
        $url = I('url','');
        
        if( !empty( $url ) )
        {
            $extractObj = new ExtractContent();
            $extractObj->extract( file_get_contents($url));
    //         echo $extractObj->getTitle();
            echo $extractObj->asHtml();
        }
        $this->display();
    }
  
    public function getDatas()
    {
        
    }
}

?>