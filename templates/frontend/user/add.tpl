<script>
	var SITE_URL = '{SITE_URL}';
</script>
<script type="text/javascript" src="{TEMPLATES_URL}/js/frontend/user.js"></script>
<div class="message_error" style="display:none" id="msgError"></div>
<br/>
<form id="userRegister" action="" method="post">
	<ul class="form">
		<li class="clearfix">
			<label for="username">Student:</label>
			<input id="username" type="text" value="" name="student">
		</li>
		<!-- BEGIN grades -->
		<li class="clearfix">
			<label for="grade">Grade:</label>
			<input type="number" name="grade" value=""/>
		</li>
		<!-- END grades -->
		<!-- BEGIN absence -->
		<li class="clearfix">
			<label for="abcense">Absence:</label>
			<input type="date" name="absence" value=""/>
		</li>
		<!-- END absence -->
		<li class="clearfix">
			<label class="empty">&nbsp;</label>
			<input type="submit" class="button" value="Add">
		</li>
	</ul>
</form>