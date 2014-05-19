if(theChampVerified){
	// show thickbox on window load
	theChampLoadEvent(function(){
		tb_show(theChampPopupTitle, theChampAjaxUrl);
	});
}
theChampLoadEvent(function(){
	if(typeof jQuery != 'undefined'){
		jQuery('.the_champ_login_container').each(function(){
			if(!jQuery(this).find('a').length){
				jQuery(this).remove();
			}
		});
	}
});
if(theChampEmailPopup){
	// show thickbox on window load
	theChampLoadEvent(function(){
		// override tb_remove
		tb_show(theChampEmailPopupTitle, theChampEmailAjaxUrl);
	});
	
	function theChampValidateEmail(email){
		var re =/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return re.test(email);
	}
	
	function the_champ_save_email(elem){
		var email = document.getElementById('the_champ_email').value.trim();
		// validate email
		if(elem.id == 'save' && !theChampValidateEmail(email)){
			document.getElementById('the_champ_error').innerHTML = theChampEmailPopupErrorMsg;
			return;
		}
		theChampCallAjax(function(){
			theChampSaveEmail(elem.id, email);
		});
	}
	
	function theChampSaveEmail(elementId, email){
		document.getElementById('the_champ_error').innerHTML = '<img src="'+theChampLoadingImgPath+'" />';
		jQuery.ajax({
		  type: 'POST',
		  dataType: 'json',
		  url: theChampAjaxUrl,
		  data: {
			action: 'the_champ_save_email',
			elemId: elementId,
			email: email,
			id: theChampEmailPopupUniqueId
		  },
		  success: function(data, textStatus, XMLHttpRequest){
			window.history.pushState({"html":'html',"pageTitle":'page title'},"", '?done=1');
			if(data.status == 1 && data.message.response && data.message.response == 'success'){
				location.href = data.message.url;
			}else if(data.status == 1 && data.message == 'success'){
				location.href = theChampRegRedirectionUrl;
			}else if(data.status == 1 && data.message == 'cancelled'){
				// close the popup
				tb_remove();
			}else if(data.status == 1 && data.message == 'verify'){
				document.getElementById('TB_ajaxContent').innerHTML = '<strong>'+theChampEmailPopupVerifyMessage+'</strong>';
			}else if(data.status == 0){
				document.getElementById('the_champ_error').innerHTML = data.message;
			}
		  }
		});
	}
}

/**
 * Display loading image in place of Social Login interface
 */
function theChampLoadingIcon(){
	jQuery('.the_champ_login_container').html('<img id="the_champ_loading_image" src="'+theChampLoadingImgPath+'" />');
}

/**
 * Call Ajax to authenticate user
 */
function theChampAjaxUserAuth(response, provider){
	theChampLoadingIcon();
	jQuery.ajax({
	  type: 'POST',
	  dataType: 'json',
	  url: theChampAjaxUrl,
	  data: {
		action: 'the_champ_user_auth',
		profileData: response,
		provider: provider
	  },
	  success: function(data, textStatus, XMLHttpRequest){
		var redirect = theChampSiteUrl;
		if(data.status == 1){
			if(data.message == 'register'){
				redirect = theChampRegRedirectionUrl+(theChampCommentFormLogin ? '/#commentform' : '');
			}else{
				redirect = theChampRedirectionUrl+(theChampCommentFormLogin ? '/#commentform' : '');
			}
		}else if(data.message.match(/ask/) !== null){
			var keyArr = data.message.split('|');
			redirect = theChampSiteUrl+'?theChampEmail=1&par=' + keyArr[1];
		}else if(data.message == 'unverified'){
			redirect = theChampSiteUrl+'?theChampUnverified=1';
		}
		location.href = redirect;
	  }
	});
}
var theChampCommentFormLogin = false;
/**
 * Initiate Social Login process according to provider
 */
function theChampInitiateLogin(icon){
	if(icon.title == 'Login with Facebook'){
		theChampAuthUserFB();
	}else if(icon.title == 'Login with Twitter'){
		theChampPopup(theChampSiteUrl+'?theChampAuth=Twitter&champ_redirect_to='+theChampTwitterRedirect);
	}else if(icon.title == 'Login with Linkedin'){
		IN.User.authorize();
		return false;
	}else if(icon.title == 'Login with Google'){
		theChampInitializeGPLogin();
	}else if(icon.title == 'Login with Vkontakte'){
		theChampInitializeVKLogin();
	}else if(icon.title == 'Login with Instagram'){
		theChampInitializeInstaLogin();
	}
}

/**
 * Get elements by class name without jQuery
 */
function theChampGetElementsByClass(node, classname) {
	if (node.getElementsByClassName) { // use native implementation if available
		return node.getElementsByClassName(classname);
	} else {
		return (function getElementsByClass(searchClass,node) {
			if ( node == null ) {
				node = document;
			}
			var classElements = [],
			els = node.getElementsByTagName("*"),
			elsLen = els.length,
			pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)"), i, j;

			for (i = 0, j = 0; i < elsLen; i++) {
				if ( pattern.test(els[i].className) ) {
					classElements[j] = els[i];
					j++;
				}
			}
			return classElements;
		})(classname, node);
	}
}

/**
 * Display login icons
 */
function theChampDisplayLoginIcon(node, className){
	if(typeof jQuery != 'undefined'){
		jQuery('.' + className).css('display', 'block');
	}else{
		var icons = theChampGetElementsByClass(node, className);
		for(var i = 0; i < icons.length; i++){
			icons[i].style.display = 'block';
		}
	}
}