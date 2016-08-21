<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class TypeController extends CommonController {
    public function index(){
        $this->display();
    }
    
    public function getDatas( $pid = 0)
    {
        $obj = M('type');
        $res = $obj->where( 'pid = '.$pid )->select();
        if( $res )
        {
            foreach( $res as &$val )
            {
//                 $f = $obj->where( "id = ".$val['pid'] )->find();
//                 $data['level'] = $f['level'] + 1;
//                 $obj->where( "id = ".$val['id'] )->save( $data );
                if( $val['isparent'] == 1 )
                {
                    $val['children'] = array();
                    $childRes = $this->getDatas( $val['id']);
                    if( $childRes )
                    {
                        $val['children'] = $childRes;
                    }
                }
                
            }
            return $res;
        }
        return array();
    }
    
    public function getTypes()
    {
        $res = $this->getDatas(0);
        if( $res )
        {
            echo json_encode( $res );
            exit;
        }
        echo json_encode( array() );
        exit;
    }
    
    public function addType()
    {
        $op = I('op','');
        $pid = I('pid',1);
        if( $op == 'save')
        {
            $data['pid']    = I("type_pid",0);
            $data['name']   = I("type_name",'');
            if( $data['pid'] == 0 || $data['name'] == '' )
            {
                echo json_encode( array( 'statusCode' => '301', 'message'=>'参数有误!') );
                exit;
            }
            $obj = M('type');
            $parent = $obj->where( "id={$data['pid']}")->find();
            if( $parent)
            {
                $data['level']      = $parent['level'] + 1;
                $data['isparent']   = 1;
            }
            else 
            {
                echo json_encode( array( 'statusCode' => '301', 'message'=>'获取父类信息失败!') );
                exit;
            }
            if( !$obj->add( $data ) )
            {
                echo json_encode( array( 'statusCode' => '301', 'message'=>'添加失败!') );
                exit;
            }
            echo json_encode( array( 'statusCode' => '200', 'message'=>'添加成功!') );
            exit;
        }
        else 
        {
            $this->assign('pid',$pid);
            $this->display();
        }
    }
    
    public function delType()
    {
        $id = I("id",0);
        if( $id == 0 )
        {
            echo json_encode( array( 'statusCode' => '301', 'message'=>'ID不能为空!') );
            exit;
        }
        $this->delDatas( $id );
        echo json_encode( array( 'statusCode' => '200', 'message'=>'删除成功!') );
        exit;
    }
    
    public function wordInfo()
    {
        $tid    = I("id",0);
        if( $tid != 0 )
        {
            $tObj   = M("type");
            $res_t = $tObj->where("id=".$tid)->find();
            if( $res_t )
            {
                $this->assign( "type", $res_t );
            }
            else
            {
                $this->assign("errorFlag",1);
                $this->assign("errMsg","获取类别信息失败");    
            }
            $this->assign("id",$tid);
            $this->assign( "errorFlag", 0 );
        }
        else 
        {
            $this->assign( 'errMsg', '类别id不能为空！' );
            $this->assign( 'errorFlag', 1 );
        }
        $this->display();
    }
    
    public function getWords()
    {
        $id = I("id",0);
        $obj    = M('typeandword');
        $where      = ' tid= '.$id;
        
        $count  = $obj->where( $where )->count();
        $res    = $obj->where( $where )->select();
        if( $res ){
            $mObj = M("word");
            $wordAry = array();
            foreach( $res as $v )
            {
                $wordres=$mObj->where("id=".$v['wid'])->find();
                if( $wordres )
                {
                    $wordAry[] = $wordres;
                }
            }
            echo json_encode( array( 'total'=>$count, 'rows'=>$wordAry ) );
            exit;
        }
        echo json_encode( array( ) ) ;
        exit;
    }
    
    public function delDatas( $pid = 0)
    {
        $obj = M('type');
        $res = $obj->where( 'pid = '.$pid )->select();
        if( $res )
        {
            foreach( $res as $val )
            {
                $childRes = $this->delDatas( $val['id']);
                if( $childRes )
                {
                    foreach( $childRes as $child )
                    {
                        $obj->where("id=".$child['id'])->delete();
                    }
                }
    
            }
        }
        $obj->where("id={$pid}")->delete();
    }
    
    public function addWord()
    {
        $word   = I("typeword_name",'');
        $tid    = I("typeword_tid",0);
        if( empty($word) || empty( $tid ) )
        {
            echo json_encode( array( 'statusCode' => '301', 'message'=>'参数有误!') );
            exit;
        }
        $typeObj = M("type");
        $wordObj = M("word");
        $res_type = $typeObj->where('id='.$tid)->find();
        if( !$res_type )
        {
            echo json_encode( array( 'statusCode' => '301', 'message'=>'获取类别信息失败!') );
            exit;
        }
        $res_word = $wordObj->where("word='".$word."'")->find();
        $typewordObj = M("typeandword");
        if( $res_word )
        {
            $data['wid'] = $res_word['id'];
            $res = $typewordObj->where("tid={$tid} and wid={$data['wid']}")->find();
            if( $res )
            {
                echo json_encode( array( 'statusCode' => '301', 'message'=>'此类别下已存在该关键词!') );
                exit;
            }
        }
        else 
        {
            $wdata['word'] = $word;
            $wdata['score'] = 0;
            $data['wid'] = $wordObj->add($wdata);
            if( !$data['wid'] )
            {
                echo json_encode( array( 'statusCode' => '301', 'message'=>'添加关键词失败!') );
                exit;
            }
        }
        
        $data['tid'] = $tid;
        if( !$typewordObj->add( $data ) )
        {
            echo json_encode( array( 'statusCode' => '301', 'message'=>'类别下添加关键词失败!') );
            exit;
        }
        echo json_encode( array( 'statusCode' => '200', 'message'=>'添加关键词成功!') );
        exit;
        
    }
    
    public function delWord()
    {
        $wid = I("wid",0);
        $tid = I("tid",0);
        if( empty($wid) || empty($tid) )
        {
            echo json_encode( array( 'statusCode' => '301', 'message'=>'参数有误!') );
            exit;
        }
        $typewordObj = M("typeandword");
        $res = $typewordObj->where("tid={$tid} and wid={$wid}")->delete();
        if( !$res )
        {
            echo json_encode( array( 'statusCode' => '301', 'message'=>'删除关键词失败!') );
            exit;
        }
        $res = $typewordObj->where("wid={$wid}")->count();
        if( !$res )
        {
            $wordObj = M('word');
            $wordObj->where("id={$wid}")->delete();
        }
        echo json_encode( array( 'statusCode' => '200', 'message'=>'删除成功!') );
        exit;
    }
}