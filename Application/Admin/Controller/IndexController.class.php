<?php 
	namespace Admin\Controller;
	use Think\Controller;
	class IndexController extends Controller {
		
		public function index(){
			
			if (!session('userInfo')) {
				$this->redirect('index.php/Admin/Index/signin');
			}
			
			$_colors = ['red', 'green', 'blue', 'yellow'];
			$_icon = ['#EF6F66', '#39B6AE', '#56C9F5', '#fed65a'];
			$map['applyDate'] = ['lt', strtotime(date('Y-m-d'))];
			$map['applyDate'] = ['gt', strtotime(date('Y-m-d', strtotime('-1 day')))];
			$_lists = D('apply')
					  ->field("userName, jobName, applyDate, apply.userId, apply.jobId")
					  ->join("job ON job.jobId = apply.jobId")
					  ->join("user ON user.userId = apply.userId")
					  ->where($map)
					  ->order("applyDate DESC")
					  ->select();
			
			foreach ($_lists as $_index => $_one) {
				// 字体颜色
				$_lists[$_index]['color'] = $_colors[$_index % count($_colors)];
				// 圆点颜色
				$_lists[$_index]['icon'] = $_icon[$_index % count($_icon)];
				
				// 今天的信息
				if ($_one['applyDate'] > strtotime(date('Y-m-d'))) {
					$_lists[$_index]['applyDate'] = $this->time2string(time() - $_one['applyDate']).'之前';
					$today['day'] = 'Today  '.date('m-d', time());
					$today['data'][] = $_lists[$_index];
				} else {
					$_lists[$_index]['applyDate'] = date('H:i', $_lists[$_index]['applyDate']);
					$yesterday['day'] = 'Tomoroow  '.date("m-d",strtotime("-1 day"));
					$yesterday['data'][] = $_lists[$_index];
				}
			}
			$info = [$today, $yesterday];
			$this->assign(compact('info'));
			$this->display('index');
		}
		
		
		public function time2string($second){
			$day = floor($second/(3600*24));
			$second = $second%(3600*24);//除去整天之后剩余的时间
			$hour = floor($second/3600);
			$second = $second%3600;//除去整小时之后剩余的时间 
			$minute = floor($second/60);
			$second = $second%60;//除去整分钟之后剩余的时间 
			//返回字符串
			return $hour.'小时'.$minute.'分';
		}
		
		
		public function signUpView(){
			
			if (IS_GET) {
				$trades = D('trade')->select();
				$this->assign('trades', $trades);
				$this->display('signup');	
			} else if (IS_POST) {
				$_info = I('post.');
				$sellerId = D('seller')->add($_info);
				$map['sellerId'] = $sellerId;
				$flag = D('seller')->where($map)->setField("uId", 's'.$sellerId);
				if ($flag) {
					$this->success('注册成功!', 'index.php/Admin');
				}
			}
			
			
		}
		
		
		/**
		 * [login description]
		 * @return [type] [description]
		 */
		public function signIn(){
			
			if (IS_GET) {
				$this->display('signin');
			} else {
				$_info = I('post.');
				$map['email'] = $_info['email'];
				$map['pwd'] = $_info['pwd'];
				$userInfo = D('seller')->where($map)->find();
				if ($userInfo) {
					$HomeBase = A('Home/Base');
					$_userInfo = [
						'userId' => $userInfo['uId'],
						'userName' => $userInfo['companyName']
					];
					$response = json_decode($HomeBase->RCgetToken($_userInfo));
					if ($response->code === 200) {
						$map['uId'] = $response->userId;
						D('seller')->where($map)->setField('token', $response->token);
					}
					session('userInfo', $userInfo);
					$this->redirect("Index/index");
				}
			}
		}
		
		/**
		 * [postJob description]
		 * @return [type] [description]
		 */
		public function postJob(){
			
		}


		
	}
?>