 <?php 
 	namespace Home\Model;
	use Think\Model;
	class BlogModel extends Model {

		
		public function getUserBlogs($userId, $loginUserId, $page){
			$BlogImg = D('blogimg');
			$Comment = D('comment');
			$Praise = D('praise');
			
			if (is_array($userId)) {
				$map = '';
	            foreach ($userId as $id) {
	            	$map .= "blog.userId = '$id' OR ";
	            }
	            $map = substr($map, 0, -4);
			} else {
				$map['blog.userId'] = $userId;
			}
			
			$page == 0 ? $offset = 0 : $offset = $page * C('pageSize');
			
			// 基本博文信息
			$blogs = $this
					 ->field("blogId, blog.userId, content, postDate, address, praiseCount, commentCount")
					 ->join("user ON blog.userId = user.userId")
					 ->where($map)
					 ->limit($offset, C('pageSize'))
					 ->order("postDate DESC")
					 ->select();
					 
			foreach ($blogs as $index => $blog) {
				$_map['blogId'] = $blog['blogId'];
				// 判断是否对此条说说点赞
				$_praiseMap['blogId'] = $blog['blogId'];
				$_praiseMap['userId'] = $loginUserId;
				$isPraised = $Praise->where($_praiseMap)->find();
				$isPraised ? $blogs[$index]['isPraised'] = 'true' : $blogs[$index]['isPraised'] = 'false';
				// 说说图片
				$img = $BlogImg->field("small, large")->where($_map)->select();
				$blogs[$index]['img'] = $img;
				// 说说评论
				$comments = $Comment
						    ->field("userName, comment.userId, blogId, content, commentDate")
						    ->join("user ON comment.userId = user.userId")
						    ->where($_map)
						    ->order("commentDate ASC")
						    ->select();
				$blogs[$index]['comments'] = $comments;
			}
			return $blogs;
			
		}
		
		
		
	}
 ?>