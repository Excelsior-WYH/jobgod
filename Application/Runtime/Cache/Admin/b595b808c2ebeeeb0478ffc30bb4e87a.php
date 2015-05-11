<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="ThemeBucket">
		<link rel="shortcut icon" href="#" type="image/png">

		<title>Horizontal menu Page</title>

		<link href="/jobgod/Public/admin/css/style.css" rel="stylesheet">
		<link href="/jobgod/Public/admin/css/style-responsive.css" rel="stylesheet">

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script src="js/html5shiv.js"></script>
		<script src="js/respond.min.js"></script>
		<![endif]-->
		<style type="text/css">
			.wrapper {
				padding: 20px 200px;
			}	
			.dropdown-menu {
				min-width: 147px;
			}
			
		</style>
	</head>

	<body class="horizontal-menu-page">

		<section>

		    <nav class="navbar navbar-default" role="navigation">
		        <div class="container-fluid">
		            <!-- Brand and toggle get grouped for better mobile display -->
		            <div class="navbar-header">
		                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
		                    <span class="sr-only">Toggle navigation</span>
		                    <span class="icon-bar"></span>
		                    <span class="icon-bar"></span>
		                    <span class="icon-bar"></span>
		                </button>
		                <a class="navbar-brand" href="index.html">
		                    <img src="/jobgod/Public/admin/images/logo.png" alt="">
		                </a>
		            </div>

		            <!-- Collect the nav links, forms, and other content for toggling -->
		            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		                <ul class="nav navbar-nav">
		                    <li><a href="#">我的发布</a></li>
		                    <li><a href="#">发布兼职</a></li>
		                    <li><a href="#">工作申请</a></li>
		                    <li><a href="#">我的资料</a></li>
		                </ul>

		                <ul class="nav navbar-nav navbar-right">
		                    <li>
		                        <form class="navbar-form navbar-left" role="search">
		                            <div class="form-group">
		                                <input type="text" class="form-control" placeholder="Search">
		                            </div>
		                        </form>
		                    </li>
		                    <li class="dropdown">
		                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <img alt="" src="/jobgod/Public/admin/images/photos/user-avatar.png">
		                        <?php echo ($_SESSION['userInfo']['companyName']); ?>
		                        <b class="caret"></b></a>
		                        <ul class="dropdown-menu">
		                            <li><a href="#">退出登录</a></li>
		                        </ul>
		                    </li>
		                </ul>
		            </div><!-- /.navbar-collapse -->
		        </div><!-- /.container-fluid -->
		    </nav>


		    <!--body wrapper start-->
		    <div class="wrapper">
		        <div class="row">
		            <div class="col-sm-12">
		                <div class="timeline">
		                
			                <?php if(is_array($info)): $key = 0; $__LIST__ = $info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($key % 2 );++$key;?><article class="timeline-item alt">
			                        <div class="text-right">
			                            <div class="time-show first">
			                                <a href="#" class="btn btn-primary"><?php echo ($v["day"]); ?></a>
			                            </div>
			                        </div>
			                    </article>
			                    <?php if(is_array($v["data"])): $index = 0; $__LIST__ = $v["data"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($index % 2 );++$index;?><article class="timeline-item <?php if(($index % 2) == 0): ?>alt<?php endif; ?>">
				                        <div class="timeline-desk">
				                            <div class="panel">
				                                <div class="panel-body">
				                                    <span class="arrow-alt"></span>
				                                    <span class="timeline-icon" style="background: <?php echo ($vo["icon"]); ?>"></span>
				                                    <span class="timeline-date"><?php echo ($vo["applyDate"]); ?></span>
				                                    <h1 class="<?php echo ($vo["color"]); ?>"><?php echo ($vo["applyDate"]); ?></if></h1>
				                                    <p> 用户
				                                    	<a class="tooltips" href="/jobgod/index.php?s=/Admin/User/userInfo/id/<?php echo ($vo["userId"]); ?>" data-toggle="tooltip " data-placement="top" title="" data-original-title="点击查看用户详细信息">
				                                    		<strong><?php echo ($vo["userName"]); ?></strong>
				                                    	</a>
				                                    	在您发布的工作
				                                    	<a href="">
				                                    		<strong><?php echo ($vo["jobName"]); ?></strong>
				                                    	</a>
				                                    	提交了申请
				                                    </p>
				                                </div>
				                            </div>
				                        </div>
				                    </article><?php endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
		                    

		                </div>
		            </div>
		        </div>

		    </div>
		    <!--body wrapper end-->
		   
		    <!--footer section start-->
		    <footer class="sticky-footer">
		        Footer contents goes here
		    </footer>
		    <!--footer section end-->

		</section>

		<!-- Placed js at the end of the document so the pages load faster -->
		<script src="/jobgod/Public/admin/js/jquery-1.10.2.min.js"></script>
		<script src="/jobgod/Public/admin/js/jquery-ui-1.9.2.custom.min.js"></script>
		<script src="/jobgod/Public/admin/js/jquery-migrate-1.2.1.min.js"></script>
		<script src="/jobgod/Public/admin/js/bootstrap.min.js"></script>
		<script src="/jobgod/Public/admin/js/modernizr.min.js"></script>
		<script src="/jobgod/Public/admin/js/jquery.nicescroll.js"></script>
		
		<!--gritter script-->
		<script src="/jobgod/Public/admin/js/gritter/js/jquery.gritter.js"></script>
		<script src="/jobgod/Public/admin/js/gritter/js/gritter-init.js"></script>

		<!--common scripts for all pages-->
		<script src="/jobgod/Public/admin/js/scripts.js"></script>

	</body>
</html>