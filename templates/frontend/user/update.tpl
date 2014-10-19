<script src="{SITE_URL}/templates/js/frontend/user.js" type="text/javascript"></script>
<form action="{SITE_URL}/user/account/" method="post">
<input type="hidden" name="userToken" value="{USERTOKEN}">
	<ul class="form">
		<li class="clearfix">
			<label>Email:</label>
			<strong>{EMAIL}</strong>
		</li>
		<li class="clearfix">
			<label for="password">Password:</label>
			<input type="password" name="password" value="{PASSWORD}" id="password" />
		</li>
		<li class="clearfix">
			<label for="password2">Re-type Password:</label>
			<input type="password" name="password2" value="{PASSWORD}" id="password2" />
		</li>
		<li class="clearfix">
			<label for="firstName">First Name:</label>
			<strong>{FIRSTNAME}</strong>
		</li>
		<li class="clearfix">
			<label for="lastName">Last Name:</label>
			<strong>{LASTNAME}</strong>
		</li>
		<li class="clearfix">
			<label for="phoneNumber">Phone Number:</label>
			<strong>{PHONENUMBER}</strong>
		</li>
		<li class="clearfix">
			<label for="homeAddress">Home Address:</label>
			<strong>{HOMEADDRESS}</strong>
		</li>
		<!-- API JQUERY 1 -->
		<li class="clearfix">
			<label for="lastName">API Key:</label>
			<span id="apiKeyField"> </span>
		</li>
		<!-- API JQUERY 1 END -->
		<li class="clearfix">
			<label class="empty">&nbsp;</label>
			<input type="submit" class="button" value="Update" />
			<!-- API JQUERY 2 -->
			<a class="button" id="generateApiKeyButton"  > Generate Api Key </a>
			<!-- API JQUERY 2 END -->
		</li>
	</ul>
</form>