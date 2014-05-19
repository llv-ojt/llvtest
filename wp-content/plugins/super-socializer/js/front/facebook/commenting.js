theChampLoadEvent(function(){
	var commentForm = document.getElementById('commentform');
	if(commentForm){
		theChampWPCommentingContent = commentForm.innerHTML;
		if(theChampFBCommentEnable){
			if(document.getElementById('reply-title')){
				document.getElementById('reply-title').innerHTML = theChampFBCommentTitle;
			}
		}
		theChampFBCommentingContent = '<div class="fb-comments" data-href="'+theChampFBCommentUrl+'"';
		if(theChampFBCommentColor != ''){
			theChampFBCommentingContent += ' data-colorscheme="'+theChampFBCommentColor+'"';
		}
		if(theChampFBCommentNumPosts != ''){
			theChampFBCommentingContent += ' data-numposts="'+theChampFBCommentNumPosts+'"';
		}
		if(theChampFBCommentWidth != ''){
			theChampFBCommentingContent += ' data-width="'+theChampFBCommentWidth+'"';
		}
		if(theChampFBCommentOrderby != ''){
			theChampFBCommentingContent += ' data-order-by="'+theChampFBCommentOrderby+'"';
		}
		if(theChampFBCommentMobile != ''){
			theChampFBCommentingContent += ' data-mobile="'+theChampFBCommentMobile+'"';
		}
		theChampFBCommentingContent += ' ></div>';
		if(!theChampForceFBComment){
			var toggleButton = '<input onclick = "theChampToggleCommenting(this)" type="button" value="Switch to WordPress Commenting" id="the_champ_comment_toggle" /><div style="clear:both"></div>';
		}else{
			var toggleButton = '';
		}
		commentForm.innerHTML = toggleButton + theChampFBCommentingContent;
	}
	theChampInitiateFBCommenting();
});

function theChampToggleCommenting(elem){
	if(elem.value == 'Switch to WordPress Commenting'){
		document.getElementById('commentform').innerHTML = '<input onclick = "theChampToggleCommenting(this)" type="button" value="Switch to Facebook Commenting" id="the_champ_comment_toggle" /><div style="clear:both"></div>' + theChampWPCommentingContent;
	}else{
		document.getElementById('commentform').innerHTML = '<input onclick = "theChampToggleCommenting(this)" type="button" value="Switch to WordPress Commenting" id="the_champ_comment_toggle" /><div style="clear:both"></div>' + theChampFBCommentingContent;
		theChampInitiateFBCommenting();
	}
}

function theChampInitiateFBCommenting(){
	FB.init({
		appId      : theChampFBAppID, // App ID
		channelUrl : '//'+theChampSiteUrl+'/channel.html', // Channel File
		status     : true, // check login status
		cookie     : true, // enable cookies to allow the server to access the session
		xfbml      : true  // parse XFBML
	});
}