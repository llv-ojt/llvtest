window.fbAsyncInit = function() {
	// init the FB JS SDK
	FB.init({
		appId      : theChampFBKey, // App ID
		channelUrl : theChampSiteUrl + '/channel.html', // Channel File
		status     : true, // check login status
		cookie     : true, // enable cookies to allow the server to access the session
		xfbml      : true  // parse XFBMLw
	});
	// Additional initialization code such as adding Event Listeners goes here
	if ( typeof theChampDisplayLoginIcon == 'function' ) {
		theChampDisplayLoginIcon(document, 'theChampFacebookButton');
	}
};
// Load the SDK Asynchronously
(function(d){
	var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
	if (d.getElementById(id)) {return;}
	js = d.createElement('script'); js.id = id; js.async = true;
	js.src = '//connect.facebook.net/' + theChampFBLang + '/all.js';
	ref.parentNode.insertBefore(js, ref);
}(document));