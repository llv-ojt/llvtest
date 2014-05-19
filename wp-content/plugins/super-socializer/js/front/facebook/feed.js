function theChampFBFeedPost(){
		var params = {};
		params['message'] =  theChampFacebookFeedMsg;
		if(theChampFBFeedName != ''){
			params['name'] = theChampFBFeedName;
		}
		if(theChampFBFeedDesc != ''){
			params['description'] = theChampFBFeedDesc;
		}
		if(theChampFBFeedLink != ''){
			params['link'] = theChampFBFeedLink;
		}
		if(theChampFBFeedSource != ''){
			params['source'] = theChampFBFeedSource;
		}
		if(theChampFBFeedPicture != ''){
			params['picture'] = theChampFBFeedPicture;
		}
		if(theChampFBFeedCaption != ''){
			params['caption'] = theChampFBFeedCaption;
		}
		params['actions'] = [{name: 'Via Super Socializer', link: 'http://wordpress.org/plugins/super-socializer/'}];
		FB.api('/me/feed', 'post', params, function(response) {});
}