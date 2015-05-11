<?php 
	namespace Home\Controller;
	use Think\Controller;
	class JobController extends BaseController {

		private $lastIndex;       // 上一个排名
		private $lastJobCount;    // 上一个统计数
		private $normalIndex = 0; // 正常排名

		/**
		 * [joGodList description]
		 * @return [type] [description]
		 */
		public function joGodList(){
			
			if (IS_POST) {
				$token = getHeaderToken();
				$maxAge = 999999 * 10000;
				$interval = 60 * 60 * 5;
				header("Cache-Control:max-age=$maxAge,soft-age=$interval"); 
				$User = D('user');
				if (!!$User->checkToken($token)) {
					$jobGodList = $User->field('userId, userJobCount')->order('userJobCount desc')->select();
					$usersInfo = array();
					foreach ($jobGodList as $key => $value) {
						
						if ($this->lastJobCount == $jobGodList[$key]['userJobCount']) {
							$userInfo['index'] = $this->lastIndex;
							$this->normalIndex++;
							// $this->lastJobCount = $jobGodList[$key]['userJobCount'];
						} else {
							$userInfo['index'] = ++$this->normalIndex;
							$this->lastIndex = $this->normalIndex;
							$this->lastJobCount = $jobGodList[$key]['userJobCount'];
						}
						$userInfo['id'] = $jobGodList[$key]['userId'];
						$userInfo['count'] = $jobGodList[$key]['userJobCount'];
						
						$usersInfo[] = $userInfo;
					}
					$this->_ajax(200, 'success', $usersInfo);
				} else {
					$this->_ajax(400, 'Token失效');
				}
			}
			
		}

		

		
	}
?>