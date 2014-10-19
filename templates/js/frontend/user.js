
// post the form using ajax
$(document).ready(function(){
	$("#userRegister").submit(function(){
		var $form = $(this);
		$.post(
			window.location,
			$form.serialize(),
			function(result){
				if (typeof result === "object"){
					// an object was returned, so there were errors
					var errorText = "<ul>";
					for (var i in result.error){
						if (result.error.hasOwnProperty(i)){
							errorText += "<strong>" + i + "</strong>: " + result.error[i] +"<br/>";
						}
					}
					errorText += "</li></ul>";
					$("#msgError").html(errorText).show();
				}else{
					// the result isn't an object, so it's probably the contents of
					// user/account, we should redirect
					window.location = SITE_URL + '/user/account';
				}
			}
		)
		return false;
	})
	$.get( '../user/json-get-api-key' , function( data ) {
		$( "#apiKeyField" ).text(data.key);
		}, "json" );
	
	$( "#generateApiKeyButton" ).click(function() {
		// silly hack --
		// TODO: must find out how to declare SITE_URL
		$.get( '../user/json-generate-api-key' , function( data ) {
			if(data.result == 'ok')
			{
				$( "#apiKeyField" ).text(data.key);
			}
			else{
				alert(data.message);
			}
			}, "json" );
		alert( "API Key Generated" );
		});
});
