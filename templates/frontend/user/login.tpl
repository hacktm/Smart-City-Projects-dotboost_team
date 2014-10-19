



<style>
	.wrapper #logo {
		display: none;
	}
	.wrapper .content h1{
		display: none;
	}
	.footer {
		border-top: 0px;
	}
	.wrapper .content .container {
		padding-top: 300px;
	}
</style>
	<div class="wrapper login_form">
<div class="login_box clearfix">
	<h2>{SITE_NAME}</h2>
	<form action="{SITE_URL}/user/authorize" method="post" class="login clearfix">
		<div class="text_imputs">
			<input type="text" name="username" class="medium" placeholder="Username...">
			<input type="password" name="password" class="medium"  placeholder="Password...">
			<select name="userType" class="medium" >
				<option value="teacher">teacher</option>
				<option value="tutor">tutor</option>
				<option value="student">student</option>
			</select>
		</div>
		<input type="submit" class="button" id="login_button" value="Log In" style="height: 129px;
	padding: 42px 20px 0px 20px; ">
	</form>
</div>
</div>