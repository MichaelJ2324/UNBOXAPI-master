<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		body,html{
			background-color: #cccccc;
			background-image: url(data:image/png;base64,<?php echo base64_encode(file_get_contents(DOCROOT."assets/css/whitey.png")) ?>);
			background-repeat: repeat;
			background-position:right top;
			height: 100%;
			padding: 0;
			margin: 0;
			box-sizing: border-box;
			font-family: Arial, Helvetica, sans-serif;
		}
		.header{
			height: 60px;
			padding: 10px 0px 10px 0px;
			font-size: 40px;
			background-color: #333;
			color: #fff;
			width: 100%;
		}
		.main-logo{
			display: block;
			margin-left: auto;
			margin-right: auto;
			height: 100%;
			width: 320px;
		}
		.main-logo a{
			height: 100%;
			display: inline-block;
			text-decoration: none;
			color: #fff;
			font-family: "Lucida Sans Unicode", "Lucida Grande", sans-serif;
			vertical-align: top;
		}
		.unbox-logo{
			height: 100%;
			display: inline-block;
			float: left;
		}
		.icon-logo{
			height: 100%;
			display: inline-block;
			float: left;
		}
		.icon-logo>img{
			height: 100%;
			padding: 0px 5px 0px 5px;
		}
		.api-logo{
			display: block;
			height: 100%;
			float: left;
			color:#985A55;
		}
		.container{
			margin-left: auto;
			margin-right: auto;
			width: 60%;
		}
	</style>
</head>
<body>
	<div class='container'>
		<div class='header'>
			<div class='main-logo'>
				<a href="http://unboxapi.dev/public/">
					<div class='unbox-logo'>
						UNBOX
					</div>
					<div class="icon-logo">
						<img src="http://unboxapi.dev/public/assets/img/logo_light_small.png?1436497438" alt="">
					</div>
					<div class='api-logo'>
						<b>API</b>
					</div>
				</a>
			</div>
		</div>
		<div class='body'>
			<h2>Setup your new account!</h2>
			<p>Thanks for registering on UNBOX API. Your seconds away from being able to easily test, manage, document, and share your APIs. </p>
			<p>
				Simply click on the link below to verify you new account and setup a password.
			</p>
			<a href="http://unboxapi.dev/public/verify?code=<?php echo $code; ?>"> Verify Account </a>
		</div>
	</div>
</body>
</html>
