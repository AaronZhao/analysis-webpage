<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class UserController extends CommonController {
    public function index(){
               
        $this->display();
    }
    
    public function getUsers()
    {
        $userObj    = M('user');
        $username   = I('post.username','');
        $realname   = I('post.realname','');
        $where      = ' 1=1 ';
        
        if( $username ){
            $where .= " and username like '%".$username."%'";
        }
        
        if( $realname ){
            $where .= " and realname like '%".$realname."%'";
        }
        $count  = $userObj->where( $where )->count();
        $res    = $userObj->field(array('id','username','realname','lastlogintime','lastloginip') )
                        ->where( $where )->select();
        if( $res ){
            echo json_encode( array( 'total'=>$count, 'rows'=>$res ) );
            exit;
        }
        echo json_encode( array( ) ) ;
        exit;
    }
    
    public function addUser()
    {
        $op       = I("get.op",'');
        if( $op == 'save' )
        {
            $username = I("post.user_username",'');
            $password = I("post.user_password",'');
            $realname = I("post.user_realname",'');
            if( empty( $username ) || empty( $password ) )
            {
                echo json_encode( array( 'statusCode' => '301', 'message'=>'用户名或密码不能为空!') );
                exit;
            }
            $userObj = M('user');
            $data['username'] = $username;
            $data['password'] = substr( md5( $password ), 0, 28 );
            $data['realname'] = $realname;
            $res = $userObj->add( $data );
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
    
    public function delUser()
    {
        $id = I( "post.id", 0 );
        if( 0 === $id )
        {
            echo json_encode( array( 'statusCode' => '301', 'message'=>'ID不能为空!') );
            exit;
        }
        $userObj = M('user');
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