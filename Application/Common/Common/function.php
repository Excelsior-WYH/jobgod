<?php 
	
	if (!function_exists('getHeaderToken')) {
		function getHeaderToken() {
			foreach ($_SERVER as $name => $value) { 
	           if (substr($name, 0, 5) == 'HTTP_') { 
	               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value; 
	           } 
	       	} 
	       	
	       	foreach ($headers as $key => $value) {
	       		if ($key == 'Token') {
	       			$token = $headers[$key];
	       			break;
	       		}
	       	}
	       	return $token;
		}
	}
	
	/**
	 * [analyJson description]
	 * @param  [type] $json_str [description]
	 * @return [type]           [description]
	 */
	function analyJson($json_str) {
		$json_str = str_replace('＼＼', '', $json_str);
		$out_arr = array();
		preg_match('/{.*}/', $json_str, $out_arr);
		if (!empty($out_arr)) {
			$result = json_decode($out_arr[0], true);
		} else {
			return false;
		}
		return $result;
	}
	
	
	function bug($message){
		echo "<pre style=\"color:red; font-size: 16px\">";
		print_r($message);
		echo "</pre>";
	}


    function rad($d){
        return $d * 3.1415926535898 / 180.0;
    }

    function getDistance($lat1, $lng1, $lat2, $lng2){

        $EARTH_RADIUS = 6378.137;
        $radLat1 = rad($lat1);
        $radLat2 = rad($lat2);
        $a = $radLat1 - $radLat2;
        $b = rad($lng1) - rad($lng2);
        $s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)));
        $s = $s *$EARTH_RADIUS;
        $s = round($s * 10000) / 10000;
        return $s;
    }


    function arraySort($nearybyUsers, $sort, $field){
        $sort = array(
            'direction' => $sort, //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
            'field'     => $field,       //排序字段
        );
        $arrSort = array();
        foreach($nearybyUsers as $uniqid => $row){
            foreach($row as $key=>$value){
                $arrSort[$key][$uniqid] = $value;
            }
        }
        if($sort['direction']){
            array_multisort($arrSort[$sort['field']], constant($sort['direction']), $arrUsers);
        }
        return $nearybyUsers;
    }


    function array_sort($arr,$keys,$type='desc'){   
	    $keysvalue = $new_array = array();  
	    foreach ($arr as $k => $v){  
	        $keysvalue[$k] = $v[$keys];  
	    }  
	    if($type == 'asc'){  
	        asort($keysvalue);  
	    }else{  
	        arsort($keysvalue);  
	    }  
	    reset($keysvalue);  
	    foreach ($keysvalue as $k=> $v){  
	        $new_array[$k] = $arr[$k];  
	    }  
	    return $new_array;   
	}   
	
	function array2sort($a,$sort,$d) {
	    $num=count($a);
	    if(!$d){
	        for($i=0;$i<$num;$i++){
	            for($j=0;$j<$num-1;$j++){
	                if($a[$j][$sort] > $a[$j+1][$sort]){
	                    foreach ($a[$j] as $key=>$temp){
	                        $t=$a[$j+1][$key];
	                        $a[$j+1][$key]=$a[$j][$key];
	                        $a[$j][$key]=$t;
	                    }
	                }
	            }
	        }
	    } else {
	        for($i=0;$i<$num;$i++){
	            for($j=0;$j<$num-1;$j++){
	                if($a[$j][$sort] < $a[$j+1][$sort]){
	                    foreach ($a[$j] as $key=>$temp){
	                        $t=$a[$j+1][$key];
	                        $a[$j+1][$key]=$a[$j][$key];
	                        $a[$j][$key]=$t;
	                    }
	                }
	            }
	        }
	    }
	    return $a;
	}
	
	function blogsByPage($blogs, $page) {
		
		$pageSize = 10;
        $count = count($blogs);
        // 最多页数
        $_page = floor($count / $pageSize);
        // 如果传入页数大于实际页数 直接返回空数组
        if ($page > $_page) {
        	return [];
        }
        // 如果有一页以上,返回指定页数
        if ($count > $pageSize) {
        	$blogs = array_slice($blogs, $page == 0 ? 0 : $page * $pageSize, $pageSize);
        }
        // 数据没有一页,返回所有数据
        return $blogs;
		
	}
	
	

?>