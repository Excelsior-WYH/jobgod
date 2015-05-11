<?php 
	namespace Home\Controller;
	use Think\Controller;
    use Home\Model\SettingModel as Setting;
	class UserController extends BaseController {


		private $token;
		private $Blog;

		public function _initialize(){
			$this->token = getHeaderToken();
			$this->Blog = D('blog');
		}

        /**
         *
         */
		public function register(){
            $User = D('user');
            if (I('post.userCode') == NULL) {
                $userTel = I('post.userTel');
                $flag = $User->checkTel($userTel);
                $flag ? $this->_ajax(100, '该手机号已被注册!') : $this->_ajax(200, '可以注册');
            }else {
                $userTel = I('post.userTel');
                $userPass = I('post.userPass');
                $userCode =  I('post.userCode');
                $response = json_decode($this->mobValidate($userTel, $userCode), true);
                if ($response['status'] === 200) {
                    $flag = $User->addOneUser($_POST);
                    $flag ? $this->_ajax(200, '验证码正确，注册成功!') : $this->_ajax(0, '注册失败');
                }else {
                    $this->_ajax(0, '验证码错误!');
                }
            }
		}
		
		

		public function modifyPass(){
			
			if (IS_POST && !empty($_POST)) {
				$User = D('user');
				if (I('post.userCode') == NULL) {
					$flag = $User->checkTel(I('post.userTel'));
					$flag ? $this->_ajax(200, '可以修改密码!') : $this->_ajax(0, '此用户不存在!');
				}else {
					$response = json_decode($this->mobValidate(I('post.userTel'), I('post.userCode')));
					if ((int)$response->status === 200) {
						$flag = $User->modifyPass(I('post.userTel'), I('post.userPass'));
						$flag ? $this->_ajax(200, '修改密码成功!') : $this->_ajax(0, '修改密码失败');
					}else {
						$this->_ajax(0, '验证码错误!');
					}
				}
			}
			
		}



		public function modifySign() {
			
			if (IS_POST) {
				$token = getHeaderToken();
				if (is_null($token)) {
					$this->_ajax(400, '参数有误,请重新登录!');
				}
				$User = D('user');
				if (!$User->checkToken($token)) {
					$this->_ajax(400, 'Token失效,请重新登录');
				}
				$flag = $User->setUserInfo($token, 'userSign', I('post.userSign'));
				$flag ? $this->_ajax(200, '更新成功!') : $this->_ajax(0, '更新失败!');
			}
			
		}
		
		

		public function modifyName() {
			
			if (IS_POST) {
				$token = getHeaderToken();
				if (is_null($token)) {
					$this->_ajax(400, '参数有误,请重新登录!');
				} else {
					$User = D('user');
					if (!$User->checkToken($token)) {
						$this->_ajax(400, 'Token失效,请重新登录');
					} else {
						$userName = I('post.userName');
						$flag = $User->setUserInfo($token, 'userName', $userName);
						if ($flag) {
							$response = json_decode($this->RCrefreshName($User->getIDByToken($token), $userName));
							$response->code === 200 ? $this->_ajax(200, 'success') : $this->_ajax(0, 'failed');
						}
					}
				}
			}
			
		}
		


		public function modifyFace() {
			$User = D('user');
			if ($User->checkToken($this->token) === 2) {
				$flag = $User->setUserInfo($this->token, 'userFace', I('post.userFace'));
				if ($flag) {
					$response = json_decode($this->RCrefreshFace($User->getIDByToken($this->token), $userFace));
					if ($response->code === 200) {
						$this->_ajax(200, 'success');
					} else {
						$this->_ajax(0, 'failed');	
					}
				}
			} else {
				$this->_ajax(400, '不要瞎搞!');
			}
			 
		}
		
		

		private function _uploadUserFace($userFace, $token) {
			
			// 上传文件基本配置
            $config = C('uploadConfig'); 
            $config['savePath'] = 'upload/user/face/';
            $config['saveName'] = D('user')->getTelByoken($token)._.time();    // 手机号+时间戳作为头像名

            // 上传的原始图片信息
            $Upload = new \Think\Upload($config);
            $uploadInfo = $Upload->uploadOne($userFace);
            $uploadInfo['userFace'] = 'Public/'.$uploadInfo['savepath'].$uploadInfo['savename']; 
            
            // 返回缩略图信息
            return $this->_createThumbUserFace($uploadInfo);
            
		}	
		

		private function _createThumbUserFace($uploadInfo){
            $Image = new \Think\Image();
            $Image->open($uploadInfo['userFace']);    // 打开原始图片
            $Image->thumb(200, 200, \Think\Image::IMAGE_THUMB_FIXED);    // 生成缩略图
            $thumbFaceInfo = 'Public/'.$uploadInfo['savepath'].'thumb_'.$uploadInfo['savename'];
            if ($Image->save($thumbFaceInfo)) {
                return C('completeDomain').$thumbFaceInfo;
            }
        }
		
		

		public function setUserBg(){
			$User = D('user');
			$UserBg = D('userbg');
			if ($User->checkToken($this->token) === 2) {
				
				$userId = $User->getIDByToken($this->token);
				$map['userId'] = $userId;
				$bgInfo = array(
					'userId' => $userId,         // 用户ID
					'bgSrc' => I('post.bgSrc')   // 阿里云图片地址
				);
				
				// 首次上传, 新增记录
				if (!$UserBg->where($map)->find()) {
					$flag = $UserBg->add($bgInfo);
				} else { // 更新
					$flag = $UserBg->where($map)->save($bgInfo);
				}
				$flag ? $this->_ajax(200, 'success') : $this->_ajax(0, 'failed');
			} else {
				$this->_ajax(400, '不要瞎搞!');
			}			
		}
		
		
		/**
		 * [getUserBg description]
		 * @return [type] [description]
		 */
		public function getUserBg(){
			// $maxAge = 999999 * 10000;
			// $interval = 60 * 60 * 5;
			// header("Cache-Control:max-age=$maxAge,soft-age=$interval"); 
			$User = D('user');
			if ($User->checkToken($this->token)) {
				$map['userId'] = I('post.userId');
				$bgInfo = D('userbg')->where($map)->getField("bgSrc");
				$bgInfo ? $this->_ajax(200, 'success', $bgInfo) : $this->_ajax(0, 'failed');
			} else {
				$this->_ajax(400, '不要瞎搞!');
			}
		}
		
		
		/**
		 * [_uploadUserBg description]
		 * @param  [type] $userBg [description]
		 * @return [type]         [description]
		 */
		private function _uploadUserBg($userBg, $token){
			// 上传文件基本配置
            $config = C('uploadConfig'); 
            $config['savePath'] = 'upload/user/bg/';
            $config['saveName'] = D('user')->getTelByoken($token)._.time();    // 手机号+时间戳作为头像名

            // 上传的原始图片信息
            $Upload = new \Think\Upload($config);
            $uploadInfo = $Upload->uploadOne($userBg);
            return C('completeDomain').'Public/'.$uploadInfo['savepath'].$uploadInfo['savename']; 
            
		}
		
		


		/**
		 * [login 用户登录]
		 * @return [JSON] [提示信息]
		 */
		public function login(){

			$User = D('user');
			$userTel = I('post.userTel');
			$userInfo = $User->userLogin($userTel, I('post.userPass'));
			if ($userInfo) {
				$response = json_decode($this->RCgetToken($userInfo));
				if ($response->code === 200) {
					$flag = $User->setUserToken($userTel, $response->token);
					if ($flag) {
						$map['userId'] = $userInfo['userId'];
						// 用户Token
						$basicInfo = array(
							"token" => $response->token,
							"userId" => $userInfo['userId']
						);
						$this->_ajax(200, '登录成功!', $basicInfo);
					} else {
						$this->_ajax(0, '有误!');
					}
				}else {
					$this->_ajax(0, '系统错误!');
				}
			}else {
				$this->_ajax(0, '用户名或密码错误');
			}

		}
		
		/**
		 * [getPersonBrief 获取个人信息]
		 * @param  [type] $id [description]
		 * @return [type]     [description]
		 */
		public function getPersonBrief() {
            $User = D('user');
            $token = getHeaderToken();
			if ($User->checkToken($token)) {
                $personBrief = $User->getPersonBrief(I('post.userId'));
                if ($personBrief) {
                    $this->_ajax(200, '这是用户信息!', $personBrief);
                } else {
                    $this->_ajax(0, '无此用户!');
                }
			} else {
				$this->_ajax(400, '不要瞎搞!');
			}
		}


        /**
         *
         */
        public function getUserDetail(){
            $User = D('user');
            $token = getHeaderToken();
            if ($User->checkToken($token) === 2) {
                I('post.userId') ? $userId = I('post.userId') : $userId = $User->getIDByToken($token);
                $userDetail = $User->getUserDetail($userId);
                $userDetail ? $this->_ajax(200, 'success', $userDetail) : $this->_ajax(0, 'failed');
            }
        }
		
		/**
		 * [getUsersInfo 获取用户相关联用户信息]
		 * @param  [Array] [$id]  [用户ID数组]
		 * @return [JSON]         [返回信息]
		 */
		public function getPersonsBrief() {
			
			if (IS_POST) {
				$token = getHeaderToken();
				$User = D('user');
				if ($User->checkToken($token)) {
					$ids = I('post.userId');
					$ids = explode(",", $ids);
					foreach ($ids as $id) {
						$personBrief = $User->getPersonBrief($id);
						if (!!$personBrief) { $usersInfo[] = $personBrief; }
					}
					$this->_ajax(200, 'success', $usersInfo);
				} else {
					$this->_ajax(400, '不要瞎搞!');
				}
			}
			
		}
		

		/**
		 * [updatePersonsBreif description]
		 * @return [type] [description]
		 */
		public function updatePersonsBrief() {
			
			if (IS_POST) {
                $User = D('user');
				$token = getHeaderToken();
				// $maxAge = 999999 * 10000;
				// $interval = 60 * 60 * 5;
				// header("Cache-Control:max-age=$maxAge,soft-age=$interval"); 
				if ($User->checkToken($token)) {
					$ids = I('post.userId');
					if (!$ids) {
						$this->_ajax(200, '为空', array());
					} else {
                        $ids = explode(",", $ids);
                        $timeStamp = I('post.time'); //    上次同步时间戳
                        $personsBrief = $User->updatePersonsBrief($ids, $timeStamp); //    取得数据有更新的用户数据
                        if (!$personsBrief) {
                            $personsBrief = array();
                        }
                        $this->_ajax(200, 'success', $personsBrief);
                    }
				} else {
					$this->_ajax(400, '不要瞎搞!', null);
				}
			}
			
		}
			
		
		/**
		 * [getTempToken 获取临时Token]
		 * @return [type] [description]
		 */
		public function getTempToken(){
			$tempToken = md5(time().sha1('jobgod'));
			$tempTokenInfo = array('ttToken'=>$tempToken, 'ttDate'=>time());
			$flag = D('temptoken')->add($tempTokenInfo);
			if (!!$flag) {
				$this->_ajax(200, 'success', $tempToken);
			} else {
				$this->_ajax(0, 'failed');
			}
			
		}
		
		/**
		 * [getUserFriends 获取用户关注列表]
		 * @return [type] [description]
		 */
		public function getUserFriends(){
			
			if (IS_POST) {
				$token = getHeaderToken();
				$User = D('user');
				if ($User->checkToken($token) === 2) {
					$userId = $User->getIDByToken($token);
					$userFriends = D('focus')->getUserFriends($userId, I('post.flag'));
					if ($userFriends) {
						foreach ($userFriends as $key => $value) {
							$ids[] = $userFriends[$key]['id'];
						}
					}
					$this->_ajax(200, 'success', $ids);
				} else {
					$this->_ajax(400, 'Token失效');
				}
			}
			
		}
		
		
		/**
		 * [focusUser 关注好友]
		 * @return [type] [description]
		 */
		public function focusUser(){
			if (IS_POST) {
				$User = D('user');
				$token = getHeaderToken();
				if ($User->checkToken($token) === 2) {
					$flag = D('focus')->focusUser($User->getIDByToken($token), I('post.id'));
					$flag ? $this->_ajax(200, 'success') : $this->_ajax(0, 'failed');
				} else {
					$this->_ajax(400, 'Token失效,请重新登录!');
				}
			}
		}
		
		
		/**
		 * [unfocusUser 取消关注]
		 * @return [type] [description]
		 */
		public function unfocusUser(){
			if (IS_POST) {
				$User = D('user');
				$token = getHeaderToken();
				if ($User->checkToken($token) === 2) {
					$flag = D('focus')->unFocusUser($User->getIDByToken($token), I('post.id'));
					$flag ? $this->_ajax(200, 'success') : $this->_ajax(0, 'failed');
				} else {
					$this->_ajax(400, 'Token失效,请重新登录!');
				}
			}
		}
		
		
		/**
		 * [userResume 用户简历]
		 * @return [type] [description]
		 */
		public function setUserResume(){
			
			/** Data Init **/
			$User = D('user');
			$Resume = D('resume');
			$Setting = D('setting');
			/** Data End **/
			
			
			if ($User->checkToken($this->token) === 2) {
				$info = I('post.');
				$addressAuth = $info['publicAddress'];
				unset($info['publicAddress']);
				$userId = $User->getIDByToken($this->token);
				$map['userId'] = (int)$userId;
				
				// 更新简历
				if ($Resume->where($map)->find()) {
					$Resume->where($map)->save($info);
				} else { // 添加简历
                    $info['userId'] = $userId;
					$Resume->add($info);
				}
				// 设置地址是否公开
				$Setting->where($map)->setField('addressPublicAble', $addressAuth);
				$this->_ajax(200, 'success');
			} else {
				$this->_ajax(400, 'Token失效,请重新登录!');
			}
			
		}


        /**
         *
         */
		public function getUserResume(){
			
			
            /**   Data Init  **/
            $User = D('user');
            $Setting = D('setting');
            $Resume = D('resume');
            /**   Data End  **/
            
            if ($User->checkToken($this->token) === 2) {
            	
            	$userId = I('post.userId');
            	// 查看别人的简历前判断是否公开
            	if ($userId) {
            		$resumeAuth = $Setting->where("userId = '$userId'")->getField("resumePublicAble");
                    if ($resumeAuth == 'false') {
                        $this->_ajax(0, '该用户简历未公开!');
                    }
            	} else {
            		$userId = $User->getIDByToken($this->token);
            	}
            	
            	$map['userId'] = $userId;
                
                // 简历基本信息
                $resumeInfo = $Resume->where($map)->find();
                // 判断地址信息是否公开
                $addressPublicAble = $Setting->where($map)->getField('addressPublicAble');
                $address = $addressPublicAble == 'true' ? $User->where($map)->getField('userAddress') : '暂未公开!';
                
                $resumeInfo['userAddress'] = $address;
                $resumeInfo['publicAddress'] = $addressPublicAble;
                $resumeInfo['userSign'] = $User->where($map)->getField('userSign');
                
                if (!$resumeInfo) {
                    $resumeInfo = null;
                }
                $this->_ajax(200, 'success', $resumeInfo);
                
            } else {
            	$this->_ajax(400, '不要瞎搞!');
            }

		}
		
		
		/**
		 * [setUserSetting 个人用户设置]
		 */
		public function setUserSetting(){
			if (IS_POST) {
				$User = D('user');
				$token = getHeaderToken();
				if ($User->checkToken($token) === 2) {
					$userId = $User->getIDByToken($token);
					$_setting = I('post.');
					$flag = D('setting')->setUserSetting($userId, $_setting);
					$flag ? $this->_ajax(200, '设置成功!', $_setting) : $this->_ajax(0, '设置失败!', $_setting);
				}
			}
		}
		
		/**
		 * [setUserSetting 个人用户设置]
		 */
		public function getUserSetting(){
			if (IS_POST) {
				$User = D('user');
				$token = getHeaderToken();
				if ($User->checkToken($token) === 2) {
					$setting = D('setting')->getUserSetting($User->getIDByToken($token));
					unset($setting['userId']);
					$setting ? $this->_ajax(200, 'success', $setting) : $this->_ajax(0, 'failed');
				} else {
                    $this->_ajax(400, '请重新登录!', null);
                }
			}
		}


        /**
         *
         */
        public function syncAddress(){
            $User = D('user');
            $token = getHeaderToken();
            if ($User->checkToken($token) === 2) {
                $map['userId'] = $User->getIDByToken($token);
                $_info = I('post.');
                $_info['userSyncDate'] = time();
                $_update = $User->where($map)->save($_info);
                $this->_ajax(200, '同步成功!');
            } else {
                $this->_ajax(400, '请重新登录!');
            }
        }



        public function getNearbyUser(){
            $User = D('user');
            $page = I('post.page');
            $token = getHeaderToken();
            if ($User->checkToken($token) === 2) {
                $nearbyUser = $User->getNearbyUser($User->getIDByToken($token), $page);
                $nearbyUser ? $nearbyUser = $nearbyUser : $nearbyUser = array();
                $this->_ajax(200, 'success', $nearbyUser);
            }
        }
		
		
		
		public function test(){
            $result = '';
            foreach ($test as $value) {
            	$result .= 'userId = '.$value.'OR ';
            }
            echo substr($result, 0, -3);
		}
		


	}
?>