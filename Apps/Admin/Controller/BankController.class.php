<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class BankController extends CommonController {
    public function index(){
               
        $this->display();
    }
    
    public function getBanks()
    {
        $bankObj    = M('bank');
        $name       = I('post.bankname','');
        $alias      = I('post.bankalias','');
        $where      = ' 1=1 ';
        
        if( $name ){
            $where .= " and name like '%".$name."%'";
        }
        
        if( $alias ){
            $where .= " and alias like '%".$alias."%'";
        }
        $count  = $bankObj->where( $where )->count();
        $res    = $bankObj->field(array( 'id','name','alias' ) )
                        ->where( $where )->select();
        if( $res ){
            echo json_encode( array( 'total'=>$count, 'rows'=>$res ) );
            exit;
        }
        echo json_encode( array( ) ) ;
        exit;
    }
    
    public function addBank()
    {
        $op = I("get.op",'');
        if( $op == 'save' )
        {
            $name = I("post.bankname",'');
            $alias = I("post.bankalias",'');
            if( empty( $name ) )
            {
                echo json_encode( array( 'statusCode' => '301', 'message'=>'银行名称不能为空!') );
                exit;
            }
            $bankObj = M('bank');
            $data['name'] = $name;
            $data['alias'] = $alias;
            $res = $bankObj->add( $data );
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
    
    public function editBank()
    {
        $op = I( "op", '' );
        $bankObj = M('bank');
        
        if( $op == 'save' )
        {
            $id = I("post.bankid");
            if( $id == 0 )
            {
                echo json_encode( array( 'statusCode' => '301', 'message'=>'信息有误!') );
                exit;
            }
            $name = I("post.bankname",'');
            $alias = I("post.bankalias",'');
            if( empty( $name ) )
            {
                echo json_encode( array( 'statusCode' => '301', 'message'=>'银行名称不能为空!') );
                exit;
            }
            
            $data['name'] = $name;
            $data['alias'] = $alias;
            $res = $bankObj->where( "id={$id}" )->save( $data );
            if( $res === false )
            {
                echo json_encode( array( 'statusCode' => '302', 'message'=>'数据添加失败!') );
                exit;
            }
            echo json_encode( array( 'statusCode' => '200', 'message'=>'保存成功!') );
            exit;
        }
        else
        {
            $id = I( "get.id", 0 );
            if( $id == 0 )
            {
                echo json_encode( array( 'statusCode' => '301', 'message'=>'请选择要编辑的内容!') );
                exit;
            }
            $res = $bankObj->where("id={$id}")->find();
            $this->assign( 'info', $res );
            $this->display();
        }
    }
    
    
    public function delBank()
    {
        $id = I( "post.id", 0 );
        if( 0 === $id )
        {
            echo json_encode( array( 'statusCode' => '301', 'message'=>'ID不能为空!') );
            exit;
        }
        $bankObj = M('bank');
        $res = $bankObj->where('id='.$id)->delete();
        if( $res === false )
        {
            echo json_encode( array( 'statusCode' => '302', 'message'=>'数据删除失败!') );
            exit;
        }
        echo json_encode( array( 'statusCode' => '200', 'message'=>'删除成功!') );
        exit;
    }

}