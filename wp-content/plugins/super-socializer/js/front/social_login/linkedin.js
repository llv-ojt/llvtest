// LinkedIn login
IN.Event.on(IN, 'auth', function(){
	theChampLoadingIcon();
	IN.API.Profile("me").
	fields(["email-address", "id", "first-name", "last-name", "headline", "picture-url", "public-profile-url"]).
	result(function(result) {
		if(result.values[0].id && result.values[0].id != ''){
			theChampAjaxUserAuth(result.values[0], 'linkedin');
		}
	});
});

function theChampLinkedInOnLoad(){
	theChampDisplayLoginIcon(document, 'theChampLinkedinButton');
}