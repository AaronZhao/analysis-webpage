<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class RegionController extends CommonController {
    
    private $table = 'region';
    private $prefix = 'region';
    
    public function index(){

        $this->assign('prefix',$this->prefix);
        $this->display();
    }
    
    public function getDatas()
    {
        $obj    = M($this->table);
        $name   = I('post.name','');
        $alias   = I('post.alias','');
        $where      = ' 1=1 ';
        
        if( $name ){
            $where .= " and name like '%".$name."%'";
        }
        
        if( $alias ){
            $where .= " and alias like '%".$alias."%'";
        }
        $count  = $obj->where( $where )->count();
        $res    = $obj->field(array('id','name','alias') )
                        ->where( $where )->select();
        if( $res ){
            echo json_encode( array( 'total'=>$count, 'rows'=>$res ) );
            exit;
        }
        echo json_encode( array( ) ) ;
        exit;
    }
    
    public function addRow()
    {
        $op       = I("get.op",'');
        if( $op == 'save' )
        {
            $name = I("post.".$this->prefix."_name",'');
            $alias = I("post.".$this->prefix."_alias",'');
            if( empty( $name ) || empty( $alias ) )
            {
                echo json_encode( array( 'statusCode' => '301', 'message'=>'用户名或密码不能为空!') );
                exit;
            }
            $obj = M($this->table);
            $data['name'] = $name;
            $data['alias'] = $alias;
            $res = $obj->add( $data );
            if( $res === false )
            {
                echo json_encode( array( 'statusCode' => '302', 'message'=>'数据添加失败!') );
                exit;
            }
            echo json_encode( array( 'statusCode' => '200', 'message'=>'添加成功!') );
            exit;
        }
        else 
        {
            $this->display();
        }
    }
    
    public function modRow()
    {
        $op = I( "get.op", '' );
        $id = I( "post.user_id", 0 );
        if( $op == 'save' )
        {
            $name   = I( "post.".$this->prefix."_name", '' );
            $alias  = I( "post.".$this->prefix."_alias", '' );
            if( empty( $name ) || empty( $alias ) )
            {
                echo json_encode( array( 'statusCode' => '301', 'message'=>'名称或别称不能为空!') );
                exit;
            }
            $obj = M($this->table);
            $data['name'] = $name;
            $data['alias'] = $alias;
            $res = $obj->where('id='.$id)->save( $data );
            if( $res === false )
            {
                echo json_encode( array( 'statusCode' => '302', 'message'=>'数据更新失败!') );
                exit;
            }
            echo json_encode( array( 'statusCode' => '200', 'message'=>'更新成功!') );
            exit;
        }
        else
        {
            if( $id == 0 )
            {
                echo json_encode( array( 'statusCode' => '301', 'message'=>'数据有误!') );
                exit;
            }
            $obj = M($this->table);
            $res = $obj->where( "id={$id}" )->find();
            if( !$res )
            {
                echo json_encode( array( 'statusCode' => '301', 'message'=>'数据不存在!') );
                exit;
            }
            $this->assign( "info", $res );
            $this->display('Region:addRegion');
        }
    }
    
    public function delRow()
    {
        $id = I( "post.id", 0 );
        if( 0 === $id )
        {
            echo json_encode( array( 'statusCode' => '301', 'message'=>'ID不能为空!') );
            exit;
        }
        $obj = M($this->table);
        $res = $obj->where('id='.$id)->delete();
        if( $res === false )
        {
            echo json_encode( array( 'statusCode' => '302', 'message'=>'数据删除失败!') );
            exit;
        }
        echo json_encode( array( 'statusCode' => '200', 'message'=>'删除成功!') );
        exit;
    }

}