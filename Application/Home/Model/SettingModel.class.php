<?php 
	namespace Home\Model;
	use Think\Model;
	class SettingModel extends Model {
		
		protected $trueTableName = 'setting'; 
		
		/**
		 * [setUserAuth description]
		 * @param [type] $userId [description]
		 * @param [type] $_info  [description]
		 */
		public function setUserSetting($userId, $setting){
			$map['userId'] = $userId;
			if (!$this->where($map)->find()) {
				$setting['userId'] = $userId;
				$flag = $this->add($setting);
			} else {
				$flag = $this->where($map)->save($setting);
			}
			return $flag ? true : false;
		}
		
		
		/**
		 * [getUserSetting description]
		 * @return [type] [description]
		 */
		public function getUserSetting($userId){
			$map['userId'] = $userId;
			return $this->where($map)->find();
		}



	}
?>