<?php
namespace Admin\Controller;
use Think\Controller;
use Common\Org\Snoopy;
use Common\Org\ExtractContent;
class AnalysisController extends Controller{
    
    private $_extractObj = null;
    private $_snoopyObj  = null;
    private $_redisObj   = null;
   
    public function _initialize()
    {
        set_time_limit( 0 );
        ini_set( "memory_limit", 1024*1024*1000 );
        $this->_extractObj  = new ExtractContent();
        $this->_snoopyObj   = new Snoopy();
        $this->_redisObj    = new \Redis;
        $this->_redisObj->connect(C("REDIS_HOST"),C("REDIS_PORT"));
    }
    //登录页面
    public function index()
    {
        phpinfo();exit;
        $id     = I( 'get.id' );
        $m_obj = M("site");
        $res = $m_obj->limit(10)->select();
        foreach( $res as $siteinfo )
        {
            
            $links = $this->_getLinks( $siteinfo['url'] );
            if( $links )
            {
                foreach( $links as $url )
                {
                    if( !$this->_redisObj->hExists(C('URL_HASH'),md5( $url )) )
                    {
                        $this->_redisObj->hSet( C('URL_HASH'), md5($url), $url );
                        $content = $this->_getContent( $url );
                        if( $content )
                        {
                            if( !empty( $content['content'] ) )
                            {
                                $this->_addContentToQueue( json_encode( $content ) );
                            }
                        }
                        sleep( 1 );
                    }
                }
            }
        }
    }
    
    private function _getLinks( $url )
    {
        $links = $this->_snoopyObj->fetchlinks($url);
        if ( preg_match('/200/', $links->response_code) )
        {
            return $links->results;
        }
        else 
        {
            return false;
        }
    }
    
    private function _getContent( $url )
    {
        if( !preg_match( '/javascript/i', $url ) )
        {
            $content = file_get_contents( $url );
            if( gettype( $content ) == 'string' )
            {
                $this->_extractObj->extract( $content );
                $res = $this->_extractObj->asHtml();
                if( !preg_match('/[<li|<div]/', $res) )
                {
                    $title = $this->_extractObj->getTitle();
                    return ['title'=>$title,'content'=>$res ];
                }
            }
        }
        return false;
    }
    
    private function _addContentToQueue( $value )
    {
        if( $this->_redisObj->lPush( C("FILTER_QUEUE"), $value ) )
        {
            return true;
        } 
        else 
        {
            return false;
        }  
    }
}

?>  