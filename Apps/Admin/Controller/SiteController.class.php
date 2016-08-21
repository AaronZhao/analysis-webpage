<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
use Think\Cache\Driver\Redis;
class SiteController extends CommonController {
    public function index(){
               
        $this->display();
    }
    
    public function getSites()
    {
        $siteObj    = M('site');
        $site   = I('post.site','');
        $where      = ' 1=1 ';
        
        if( $site ){
            $where .= " and site like '%".$site."%'";
        }
        
        $count  = $siteObj->where( $where )->count();
        $res    = $siteObj->field(array('id','url','note' ) )
                        ->where( $where )->select();
        if( $res ){
            echo json_encode( array( 'total'=>$count, 'rows'=>$res ) );
            exit;
        }
        echo json_encode( array( ) ) ;
        exit;
    }
    
    public function addSite()
    {
        $op       = I("get.op",'');
        if( $op == 'save' )
        {
            $site = I("post.site_site",'');
            $note = I("post.site_note",'');
            if( empty( $site ) )
            {
                echo json_encode( array( 'statusCode' => '301', 'message'=>'URL不能为空!') );
                exit;
            }
            
            $siteObj = M('site');
            $data['url'] = $site;
            $data['token'] = md5( $site );
            $data['note'] = $note;
            $res = $siteObj->add( $data );
            if( $res === false )
            {
                echo json_encode( array( 'statusCode' => '302', 'message'=>'数据添加失败!') );
                exit;
            }
            $lastId = $siteObj->getLastInsID();
            $key = C('URL_SAVE_PREFIX').$lastId % C('PROCESSNUM');
            $redisObj = new Redis();
            $redisObj-> hset( $key, $data['token'], $data['url'] );
            echo json_encode( array( 'statusCode' => '200', 'message'=>'添加成功!') );
            exit;
        }
        else 
        {
            $this->display();
        }
    }
    
    public function delSite()
    {
        $id = I( "post.id", 0 );
        if( 0 === $id )
        {
            echo json_encode( array( 'statusCode' => '301', 'message'=>'ID不能为空!') );
            exit;
        }
        $userObj = M('site');
        $res = $userObj->where('id='.$id)->delete();
        if( $res === false )
        {
            echo json_encode( array( 'statusCode' => '302', 'message'=>'数据删除失败!') );
            exit;
        }
        echo json_encode( array( 'statusCode' => '200', 'message'=>'删除成功!') );
        exit;
    }

}