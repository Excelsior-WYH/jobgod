<?php 
	namespace Home\Model;
	use Think\Model;
	class UserModel extends Model {
		protected $trueTableName = 'user'; 

		/**
		 * [checkTel description]
		 * @param  [type] $userTel [description]
		 * @return [type]          [description]
		 */
		public function checkTel($userTel){
			$flag = $this->where("userTel = '$userTel'")->find();
			return $flag == true ? true : false;
		}

		
		/**
		 * [checkToken 验证Token]
		 * @param  [String] $token [用户Token]
		 * @return [Boolean]       [Boolean]
		 */
		public function checkToken($token) {
			if (D('temptoken')->where("ttToken = '$token'")->find()) {
				return 1; // 临时Token 一级接口
			} else if ($this->where("userToken = '$token'")->find()) {
				return 2; // 融云Token 二级接口
			} else {
				return false;
			}
		}

		
		/**
		 * [getUserToken 更新用户Token]
		 * @param  [Number] $userTel   [用户手机号]
		 * @param  [String] $userToken [最新Token]
		 * @return [Boolean]           [Boolean]
		 */
		public function setUserToken($userTel, $userToken) {
			$flag = $this->where("userTel = '$userTel'")->setField('userToken', $userToken);
			return $flag == true ? true : false;
		}
		
		
		/**
		 * [getTelByoken 根据Token获取用户电话]
		 * @param  [String] $token [融云Token]
		 * @return [Number]        [用户电话号]
		 */
		public function getTelByoken($token) {
			$userTel = $this->where("userToken = '$token'")->getField('userTel');
			return $userTel == true ? $userTel : false;
		}
		
		
		/**
		 * [getIDByToken description]
		 * @param  [type] $token [description]
		 * @return [type]        [description]
		 */
		public function getIDByToken($token) {
			$userId = $this->where("userToken = '$token'")->getField("userId");
			return $userId == true ? $userId : false;
		}

		/**
		 * [addOneUser description]
		 * @param  [Array] $userInfo [用户数据]
		 * @return [Boolean]        [Boolean]
		 */
		public function addOneUser($userInfo){
			$userData = array(
				'userTel' => $userInfo['userTel'],
				'userRegDate' => time(),
				'userPass' => md5($userInfo['userPass']),
			);
			$userId = $this->add($userData);
			if ($userId) {
				$_userBgInfo = [
					'userId' => $userId,        // 用户ID
					'slide' => 'true', // 滑动
					'blur' => 'true',    // 模糊
					'bgSrc' => 'http://www.noyours.com/jobGod/Public/upload/user/bg/default.jpg'
				];
				$add = D('userbg')->add($_userBgInfo);
			}
			return $add ? true : false;
		}

		/**
		 * [modifyPass 修改用户密码]
		 * @param  [Number] $userTel     [用户手机号]
		 * @param  [String] $userNewPass [新密码]
		 * @return [Boolean]             [Boolean]
		 */
		public function modifyPass($userTel, $userNewPass) {
			if (($userTel && $userNewPass) != NULL) {
				if (md5($userNewPass) == $this->where("userTel = '$userTel'")->getField('userPass')) {
					return true;
				}else {
					$flag = $this->where("userTel = '$userTel'")->setField('userPass', md5($userNewPass));
					return $flag ? true : false;
				}
			}
		}
		
		
		/**
		 * [userLogin 用户登录]
		 * @return [Boolean] [Boolean]
		 */
		public function userLogin($userTel, $userPass){
			
			if ($userTel != Null && $userPass != NULL) {
				$_userPass = $this->where("userTel = '$userTel'")->getField('userPass');
				if ($_userPass) {
					if (strcmp($_userPass, md5($userPass)) === 0) {
						$map['userTel'] = $userTel;
						return $this->where($map)->find();
					} else {
						return false;
					}
				}else {
					return false;
				}
			}else {
				return false;
			}
			
		}
		
		
		/**
		 * [setUserSign 更新用户数据]
		 * @param  [String] $userToken [用户Token]
		 * @param  [String] $field     [更新字段]
		 * @param  [String] $fieldData [字段数据]
		 * @return [Boolean]           [Boolean]
		 */
		public function setUserInfo($userToken, $field, $fieldData) {
			$flag = $this->where("userToken = '$userToken'")->setField($field, $fieldData);
			$this->where("userToken = '$userToken'")->setField("userModifyDate", time());
			return $flag == true ? true : false;
		}
		
		
		/**
		 * [getPersonBrief 获取用户基本信息]
		 * @param  [Number] $userTel [电话号]
		 * @return [Array]           [用户基本信息]
		 */
		public function getPersonBrief($field) {
			mb_strlen($field) <= 8 ? $where = "userId = ".$field : $where = "userTel = ".$field;
			$personBrief = $this->field('userId, userName, userFace, userSign')
			                    ->where($where)
			                    ->find();
			return $personBrief == true ? $personBrief : false;
		}


        /**
         * @param $userId
         * @return mixed
         */
        public function getUserDetail($userId){
            $map['userId'] = $userId;
            $userSetting = D('setting')->where($map)->find();
            $userBasic = $this->field("userId, userIntegral")->where($map)->find();
            // 位置权限
            if ($userSetting['addressPublicAble'] == 'true') {
                $userBasic['userAddress'] = $this->where($map)->getField("userAddress");
            } else {
                $userBasic['userAddress'] = '用户未公开';
            }
            // 简历权限
            if (!D('resume')->where($map)->find()) {
                $userBasic['resumePublicAble'] = 'false';
            } else {
                $userBasic['resumePublicAble'] = $userSetting['resumePublicAble'];
            }

            $userBasic['experiencePublicAble'] = $userSetting['experiencePublicAble'];
            return $userBasic;
        }


        /**
         * @param $ids
         * @param $timeStamp
         * @return array
         */
		public function updatePersonsBrief($ids, $timeStamp) {
			$personsBrief = array();
			foreach ($ids as $id) {
                $map['userId'] = $id;
                $modifyDate = $this->where($map)->getField('userModifyDate');
				$personBrief = $this->getPersonBrief($id);
				if ((int)$modifyDate > (int)$timeStamp) { //    用户实际更新时间>客户端更新时间 则返回最新数据
					$personsBrief[] = $personBrief;
                    $map = null;
				}
			}
			return $personsBrief;
		}


		/**
		 * @descrp 
		 * @param  $userId 
		 * @return array
		 */
        public function getNearbyUser($userId, $page){
            $nearbyUsers = [];
            $map['userId'] = $userId;
            // 当前登录用户的经纬度
            $userLng = $this->where($map)->getField("userLng");
            $userLat = $this->where($map)->getField("userLat");
            
            // 所有用户
            $allUsers = $this
                        ->field("userId, userLng, userLat, userSyncDate")
            			->where("userLng != '' AND userId != '$userId'")
            			->select();
            foreach ($allUsers as $one) {
                $distance = getDistance($one['userLat'], $one['userLng'], $userLat, $userLng);
                if (round($distance) < 10) {
                    $one['distance'] = floor($distance * 1000);
                    unset($one['userLng']);
                    unset($one['userLat']);
                    $nearbyUsers[] = $one;
                }
            }
            $nearbyUsers = array2sort($nearbyUsers, 'distance');
            $pageSize = 10;
            $count = count($nearbyUsers);
            // 最多页数
            $_page = floor($count / $pageSize);
            // 如果传入页数大于实际页数 直接返回空数组
            if ($page > $_page) {
            	return [];
            }
            // 如果有一页以上,返回指定页数
            if ($count > $pageSize) {
            	$nearbyUsers = array_slice($nearbyUsers, $page == 0 ? 0 : $page * $pageSize, $pageSize);
            }
            // 数据没有一页,返回所有数据
            return $nearbyUsers;
        }
		
		
	}
?>