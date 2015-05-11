<?php 
	namespace Home\Model;
	use Think\Model;
	class FocusModel extends Model {


		/**
		 * [focusUser 关注好友]
		 * @param  [type] $fromUserId [description]
		 * @param  [type] $toUserId   [description]
		 * @return [type]             [description]
		 */
		public function focusUser($fromUserId, $toUserId){
			if (($fromUserId && $toUserId) != null) {
				$flag = $this->where("fromId = '$fromUserId' AND toId = '$toUserId'")->find();
				if ($flag) {
					return false;
				} else {
					$add = $this->add([
						'fromId' => $fromUserId,
						'toId' => $toUserId	
					]);
					return $add ? true : false;
				}
			} else {
				return false;
			}
		}
		
		
		/**
		 * [unfocusUser 取消关注]
		 * @param  [type] $fromUserId [description]
		 * @param  [type] $toUserId   [description]
		 * @return [type]             [description]
		 */
		public function unFocusUser($fromUserId, $toUserId){
			if (($fromUserId && $toUserId) != null) {
				$delete = $this->where("fromId = '$fromUserId' AND toId = '$toUserId'")->delete();
				return $delete ? true : false;
			} else {
				return false;
			}
		}
		
		
		/**
		 * [getUserFriends description]
		 * @param  [Number]  $userId [用户ID]
		 * @param  [Boolean] $flag   [True-关注我的/Flase-我关注的]
		 * @return [Array]           [好友ID数组]
		 */
		public function getUserFriends($userId, $flag){
			if (strcmp($flag, 'true') === 0) {
				$userFriends = $this->field("fromId as id")->where("toId = '$userId'")->select();
				return !!$userFriends == true ? $userFriends : $userFriends = array();
			} else {
				$userFriends = $this->field("toId as id")->where("fromId = '$userId'")->select();
				return !!$userFriends == true ? $userFriends : $userFriends = array();
			}
		}
		
		
		/**
		 * [getUserFriendsId description]
		 * @return [type] [description]
		 */
		public function getUserFriendsId($userId){
			$map['fromId'] = $userId;
			return $this->where($map)->getField("toId", true);
		}
		


		
	}
?>