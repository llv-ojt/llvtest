window.vkAsyncInit = function() {
    VK.init({
        apiId: theChampVkKey
    });
	// callback
	theChampDisplayLoginIcon(document, 'theChampVkontakteButton');
};

setTimeout(function() {
	var el = document.createElement("script");
	el.type = "text/javascript";
	el.src = "http://vk.com/js/api/openapi.js";
	el.async = true;
	document.getElementById("vk_api_transport").appendChild(el);
}, 0);

function theChampInitializeVKLogin(){
	VK.Auth.login(function(response){
		if (response.session.mid) {
			VK.Api.call('getProfiles', {uids: response.session.mid, fields: 'uid, first_name, last_name, nickname, photo'}, function(profile) {
				if(profile.response[0].uid){
					theChampCallAjax(function(){
						theChampAjaxUserAuth(profile.response[0], 'vkontakte');
					});
				}
			});
		}else {
			// error handling
		}
	});
}