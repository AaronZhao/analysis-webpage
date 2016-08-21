<?php 
namespace Admin\Controller;
use Think\Controller;
class CommonController extends Controller{
	public function _initialize(){
	    $this->assign('title',C('APPTITLE'));
		//验证登陆,没有登陆则跳转到登陆页面
		if(empty($_SESSION['username'])) $this->redirect('Admin/Login/index');
		
		//权限验证		
// 		if(!authCheck(MODULE_NAME."/".CONTROLLER_NAME."/".ACTION_NAME,session('uid'))){
// 			header('HTTP/1.1 404 Not Found');
// 			$return['message']='你没有权限';
// 			$return['status']=false;
// 			$this->ajaxReturn($return);			
// 		}
		
		$this->getTopType();
		$typeObj = M("type");
		$result = $typeObj->field("min(id) as id , name ")->where("pid = 1")->find();
		$tid = empty( $_REQUEST['tid']) === false ? (int)$_REQUEST['tid']:$result['id'];
		$tree = $this->getType($tid,0);
		$this->assign( 'tid', $tid );
		$this->assign( 'tree', $tree );
	}

    protected function _empty(){
        //$this->error('你请求的页面不存在!!');
        echo "<script>$.messager.alert('错误提示','你请求的页面不存在!!','error');</script>";
    }
    
    protected function getTopType(){
        $typeObj = M("type");
        $results = $typeObj->where("pid = 1")->select();
        $typeArr = array();
        if( false !== $results ){
            foreach( $results as $value ){
                $typeArr[] = array(
                    'id' => $value['id'],
                    'name' => $value['name'],
                );
            }
        }
        $this->assign('topTypeArr',$typeArr);
    }
    
    protected function getType($tid,$level = 0){
        $typeObj = M("type");
        $results = $typeObj->where("pid = {$tid}")->select();
        $str = '';
        //层级
        $level++;

        if($results){
            foreach($results as $val ){
                $str .= "{ id:{$val['id']}, pId:{$val['pid']}, name:\"{$val['name']}\", open:false, file:\"search\" },";
                if( $val['isparent'] == 1 ){
                    $str .= $this->getType($val['id'], $level);
                }
            }
        }
        return $str;
    }
}
?>