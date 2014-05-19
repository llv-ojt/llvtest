function theChampInitializeInstaLogin(){
	theChampPopup('https://instagram.com/oauth/authorize/?client_id='+theChampInstaId+'&redirect_uri='+theChampSiteUrl+'&response_type=token');
}

function theChampGetHashValue(key){
    if (typeof key !== 'string') {
        key = '';
    } else {
        key = key.toLowerCase();
    }
    var keyAndHash = location.hash.toLowerCase().match(new RegExp(key + '=([^&]*)'));
    var value = '';
    if (keyAndHash) {
        value = keyAndHash[1];
    }
    return value;
}
var theChampInstagramHash = theChampGetHashValue('access_token');
if(theChampInstagramHash != ''){
	window.opener.location.href = theChampSiteUrl + '?theChampInstaToken=' + theChampInstagramHash + '&champ_redirect_to=' + theChampTwitterRedirect;
	window.close();
}