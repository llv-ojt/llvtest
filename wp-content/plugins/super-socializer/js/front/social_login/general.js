// get trim() working in IE
if(typeof String.prototype.trim !== 'function') {
  String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g, ''); 
  }
}

/**
 * Open popup window
 */
function theChampPopup(url){
	window.open(url,"popUpWindow","height=400,width=600,left=20,top=20,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no,status=yes")
}
function theChampStrReplace(replace, by, str) {
    for (var i=0; i<replace.length; i++) {
        str = str.replace(new RegExp(replace[i], "g"), by[i]);
    }
    return str;
}

/**
 * Call functions on window.onload
 */
function theChampLoadEvent(func){
	var oldOnLoad = window.onload;
	if(typeof window.onload != 'function'){
		window.onload = func;
	}else{            
		window.onload = function(){
			oldOnLoad();
			func();
		}
	}
}

/**
 * Call Ajax function after loading jQuery
 */
function theChampCallAjax(callback){
	// data to send
	if(typeof jQuery != 'undefined'){
		// ajax to authenticate user
		callback();
	}else{
		// load jQuery dynamically
		theChampGetScript('http://code.jquery.com/jquery-latest.min.js', callback);
	}
}

/**
 * Load jQuery dynamically
 */
function theChampGetScript(url, success) {
	var script = document.createElement('script');
	script.src = url;
	var head = document.getElementsByTagName('head')[0],
		done = false;
	// Attach handlers for all browsers
	script.onload = script.onreadystatechange = function() {
	  if (!done && (!this.readyState
		   || this.readyState == 'loaded'
		   || this.readyState == 'complete')) {
		done = true;
		success();
		script.onload = script.onreadystatechange = null;
		head.removeChild(script);
	  }
	};
	head.appendChild(script);
}