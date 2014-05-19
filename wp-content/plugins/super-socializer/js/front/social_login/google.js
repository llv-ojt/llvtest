(function() {
   var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
   po.src = 'https://apis.google.com/js/client:plusone.js?onload=theChampGoogleOnLoad';
   var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
})();

function theChampGoogleOnLoad(){
	theChampDisplayLoginIcon(document, 'theChampGoogleButton');
}

function theChampInitializeGPLogin(){
	gapi.auth.signIn({ 
	  'callback': theChampGPSignInCallback, 
	  'clientid': theChampGoogleKey, 
	  'cookiepolicy': 'single_host_origin', 
	  'requestvisibleactions': 'http://schemas.google.com/AddActivity',
	  'scope': 'https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/userinfo.email'
	});
}

function theChampGPSignInCallback(authResult){
	if (authResult['status']['signed_in']) {	
		gapi.client.load('plus','v1', function(){
			if(authResult['access_token']) {
			  theChampGetProfile();
			} else if (authResult['error']) {
			  // There was an error, which means the user is not signed in.
			  // As an example, you can handle by writing to the console:
			  //console.log('There was an error: ' + authResult['error']);
			  
			}
		});
	}else{
		// handle error
		//console.log('Sign-in state: ' + authResult['error']);
	}
}

function theChampGetProfile(){
  theChampLoadingIcon();
  var request = gapi.client.plus.people.get( {'userId' : 'me'} );
  request.execute( function(profile) {
	if(profile.error){
		// handle error
		return;
	}else if(profile.id){
		theChampAjaxUserAuth(profile, 'google');
	}
  });
}