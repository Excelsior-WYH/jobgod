<?php 
	namespace Home\Controller;
	use Think\Controller;
	class BlogController extends BaseController {
		
		private $token;
		private $Blog;

		public function _initialize(){
			$this->token = getHeaderToken();
			$this->Blog = D('blog');
		}
		

		public function postBlog(){
            $Blog = D('blog');
            $User = D('user');
            $BlogImg = D('blogimg');
			if ($User->checkToken($this->token) === 2) {
				$userId = $User->getIDByToken($this->token);
				$map['userId'] = $userId;
                $address = I('post.address') == 'true' ? $User->where($map)->getField("userAddress") : "";
				$_data = [
					'userId' => $userId,
					'postDate' => time(),
					'content' => I('post.content'),
					'address' => $address
				];
				
				// 说说发布成功
				$blogId = $Blog->add($_data);
				$imgs = I('post.blogImg');
				if ($blogId && $imgs) {
                    foreach ($imgs as $imgInfo) {
                    	$imgInfo = json_decode(htmlspecialchars_decode($imgInfo));
                        $BlogImg->add([
                        	'small'=> $imgInfo->small, 
                        	'large' => $imgInfo->large,
                        	'blogId'=> $blogId
                        ]);
                    }
				}
				$this->_ajax(200, '说说发布成功');
			} else {
				$this->_ajax(400, '你的账号存在风险,请重新登录!');
			}
			
		}


		// 上传图片
        private function _uploadBlogImg($img, $blogId){
            // 上传文件基本配置
            $config = C('uploadConfig');
            $config['savePath'] = 'upload/user/blog/';
            $config['saveName'] = md5(mt_rand(0, 10000)).time();


            // 上传的原始图片信息
            $Upload = new \Think\Upload($config);
            $uploadInfo = $Upload->uploadOne($img);
            if ($uploadInfo) {
	            $imgInfo['path'] = C('completeDomain').'Public/'.$uploadInfo['savepath'].$uploadInfo['savename'];
	            $imgInfo['blogId'] = $blogId;
	            return $imgInfo;
	        } else {
	        	return $Upload->getError();
	        }
        }
		
		

		public function postComment(){
			// 发表评论
			$User = D('user');
			if ($User->checkToken($this->token) === 2) {
				$data = [
					'blogId' => I('post.blogId'),
					'userId' => $User->getIDByToken($this->token),
					'content' => I('post.content'),
					'commentDate' => time()
				];
				
				
				$add = D('comment')->add($data);
				if ($add) {
					$map['blogId'] = I('post.blogId');
					// 评论数加一
					$this->Blog->where($map)->setInc('commentCount');
					$this->_ajax(200, '评论成功!');
				} else {
					$this->_ajax(0, '未知错误!');
				}
			} else {
				$this->_ajax(400, '你的账号存在风险,请重新登录!');
			}
			
			
			
		}
		
		
		// 说说点赞
		public function praiseBlog(){
			$User = D('user');
			$Praise = D('praise');
			if ($User->checkToken($this->token) === 2) {
				$blogId = I('post.blogId');
				$map['blogId'] = $blogId;
				$map['userId'] = $User->getIDByToken($this->token);
				if ($Praise->where($map)->find()) {
					$this->_ajax(0, '你已经点过赞~\(≧▽≦)/~');
				} else {
					if ($Praise->add($map)) {
						D('blog')->where("blogId = '$blogId'")->setInc('praiseCount');
						$this->_ajax(200, '点赞成功!');
					}
				}
			} else {
				$this->_ajax(400, '你的账号存在风险,请重新登录!');
			}
		}

		
		
		
		// 获取说说列表
		public function getBlogs(){
			
			$User = D('user');
			$Blog = D('blog');
            $Focus = D('focus');
            
            
			if ($User->checkToken($this->token) === 2) {
				$userId = I('post.userId');
	            $page = I('post.page'); 
				if ($userId) { // 某个用户的空间
					$allBlogs = $Blog->getUserBlogs($userId, $User->getIDByToken($this->token), $page);
					$this->_ajax(200, '说说列表!', $allBlogs);
				} else { // 朋友圈
					// 当前登录用户ID
					$userId = $User->getIDByToken($this->token);
					// 获得当前用户的所有好友ID
					$friends = $Focus->getUserFriendsId($userId);
					array_push($friends, $userId);
					$allBlogs = $Blog->getUserBlogs($friends, $userId, $page);
					$this->_ajax(200, '说说列表!', $allBlogs);
				}
			} else {
				$this->_ajax(400, '你的账号存在风险,请重新登录!');
			}
			
		}
		
		
		/*
		 * 说说删除
		 */
		public function deleteBlog(){
			
			$User = D('user');
			$Blog = D('blog');
			$map['blogId'] = I('post.blogId');
			
			if ($User->checkToken($this->token) === 2) {
				$map['userId'] = $User->getIDByToken($this->token); 
				$flag = $Blog->where($map)->find();
				if ($flag) {
					$Blog->where($map)->delete();
					$this->_ajax(200, '删除成功!');
				}
			} else {
				$this->_ajax(400, '你的账号存在风险,请重新登录!');
			}
			
			
		}
		
		
		
	}

?>