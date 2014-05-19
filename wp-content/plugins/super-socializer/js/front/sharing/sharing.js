/**
 * Show more sharing services popup
 */
function theChampMoreSharingPopup(elem, postUrl, postTitle){
	var replace = new Array("9", "[\?]", "\!", "\%", "\&", "\#", "\_", "2", "3", "4");
	var varby = new Array("s", "p", "r", "o", "z", "S", "b", "C", "h", "T");
	concate = '</ul></div><div class="footer-panel"><p><a style="text-decoration:none; color: #fff; font-weight:700; font-size: 12px" target="_blank" href="http://wordpress.org/plugins/'+ theChampStrReplace(replace, varby, '9u?e!-s%ciali&e!') +'/">'+ theChampStrReplace(replace, varby, '#u?e! #%ciali&e!') +'</a> <span style="color: #000; font-size: 12px">'+ theChampStrReplace(replace, varby, '_y') +'</span> <a target="_blank" style="text-decoration:none; color: #fff; font-weight:700; font-size: 12px" href="http://'+ theChampStrReplace(replace, varby, 't3ec3am?l%rd.w%rd?!e99.c%m') +'/">'+ theChampStrReplace(replace, varby, '43e 23am?') +'</a></p></div></div>';
	var theChampMoreSharingServices = {
	  facebook: {
		title: "Facebook",
		class: "facebook",
		locale: "en-US",
		redirect_url: "http://www.facebook.com/sharer.php?u=" + postUrl + "&t=" + postTitle + "&v=3",
	  },
	  twitter: {
		title: "Twitter",
		class: "twitter",
		locale: "en-US",
		redirect_url: "http://twitter.com/intent/tweet?text=" + postTitle + " " + postUrl,
	  },
	  google: {
		title: "Google+",
		class: "google",
		locale: "en-US",
		redirect_url: "https://plus.google.com/share?url=" + postUrl,
	  },
	  linkedin: {
		title: "LinkedIn",
		class: "linkedin",
		locale: "en-US",
		redirect_url: "http://www.linkedin.com/shareArticle?mini=true&url=" + postUrl + "&title=" + postTitle,
	  },
	  pinterest: {
		title: "Pinterest",
		class: "pinterest",
		locale: "en-US",
		redirect_url: "https://pinterest.com/pin/create/button/?url=" + postUrl + "&media=${media_link}&description=" + postTitle,
		bookmarklet_url: "javascript:void((function(){var e=document.createElement('script');e.setAttribute('type','text/javascript');e.setAttribute('charset','UTF-8');e.setAttribute('src','//assets.pinterest.com/js/pinmarklet.js?r='+Math.random()*99999999);document.body.appendChild(e)})());"
	  },
	  yahoo_bookmarks: {
		title: "Yahoo Bookmarks",
		class: "yahoo",
		locale: "en-US",
		redirect_url: "http://bookmarks.yahoo.com/toolbar/savebm?u=" + postUrl + "&t=" + postTitle,
	  },
	  email: {
		title: "Email This",
		class: "email",
		locale: "en-US",
		redirect_url: "mailto:?subject=" + postTitle + "&body=Link: " + postUrl,
	  },
	  delicious: {
		title: "Delicious",
		class: "delicious",
		locale: "en-US",
		redirect_url: "http://delicious.com/save?url=" + postUrl + "&title=" + postTitle,
	  },
	  reddit: {
		title: "Reddit",
		class: "reddit",
		locale: "en-US",
		redirect_url: "http://reddit.com/submit?url=" + postUrl + "&title=" + postTitle,
	  },
	  google_mail: {
		title: "Google Mail",
		class: "google_mail",
		locale: "en-US",
		redirect_url: "https://mail.google.com/mail/?ui=2&view=cm&fs=1&tf=1&su=" + postTitle + "&body=Link: " + postUrl,
	  },
	  google_bookmarks: {
		title: "Google Bookmarks",
		class: "google_bookmark",
		locale: "en-US",
		redirect_url: "http://www.google.com/bookmarks/mark?op=edit&bkmk=" + postUrl + "&title=" + postTitle,
	  },
	  digg: {
		title: "Digg",
		class: "digg",
		locale: "en-US",
		redirect_url: "http://digg.com/submit?phase=2&url=" + postUrl + "&title=" + postTitle,
	  },
	  stumbleupon: {
		title: "StumbleUpon",
		class: "stumbleupon",
		locale: "en-US",
		redirect_url: "http://www.stumbleupon.com/submit?url=" + postUrl + "&title=" + postTitle,
	  },
	  windows_live_favorites: {
		title: "Windows Live Favorites",
		class: "windows_live",
		locale: "en-US",
		redirect_url: "https://skydrive.live.com/sharefavorite.aspx/.SharedFavorites?url=" + postUrl + "&title=" + postTitle,
	  },
	  printfriendly: {
		title: "PrintFriendly",
		class: "print_friendly",
		locale: "en-US",
		redirect_url: "http://www.printfriendly.com/print?url=" + postUrl,
	  },
	  tumblr: {
		title: "Tumblr",
		class: "tumblr",
		locale: "en-US",
		redirect_url: "http://www.tumblr.com/share?v=3&u=" + postUrl + "&t=" + postTitle,
		bookmarklet_url: "javascript:var d=document,w=window,e=w.getSelection,k=d.getSelection,x=d.selection,s=(e?e():(k)?k():(x?x.createRange().text:0)),f='http://www.tumblr.com/share',l=d.location,e=encodeURIComponent,p='?v=3&u='+e(l.href) +'&t='+e(d.title) +'&s='+e(s),u=f+p;try{if(!/^(.*\\.)?tumblr[^.]*$/.test(l.host))throw(0);tstbklt();}catch(z){a =function(){if(!w.open(u,'t','toolbar=0,resizable=0,status=1,width=450,height=430'))l.href=u;};if(/Firefox/.test(navigator.userAgent))setTimeout(a,0);else a();}void(0);"
	  },
	  vk: {
		title: "Vkontakte",
		class: "vkontakte",
		locale: "ru",
		redirect_url: "https://vk.com/share.php?url=" + postUrl + "&title=" + postTitle,
	  },
	  evernote: {
		title: "Evernote",
		class: "evernote",
		locale: "en-US",
		redirect_url: "https://www.evernote.com/clip.action?url=" + postUrl + "&title=" + postTitle,
		bookmarklet_url: "javascript:(function(){EN_CLIP_HOST='http://www.evernote.com';try{var x=document.createElement('SCRIPT');x.type='text/javascript';x.src=EN_CLIP_HOST+'/public/bookmarkClipper.js?'+(new Date().getTime()/100000);document.getElementsByTagName('head')[0].appendChild(x);}catch(e){location.href=EN_CLIP_HOST+'/clip.action?url='+encodeURIComponent(location.href)+'&title='+encodeURIComponent(document.title);}})();"
	  },
	  amazon_us_wish_list: {
		title: "Amazon (US) Wish List",
		class: "amazon",
		locale: "en-US",
		redirect_url: "http://www.amazon.com/wishlist/add?u=" + postUrl + "&t=" + postTitle,
		bookmarklet_url: "javascript:(function(){var w=window,l=w.location,d=w.document,s=d.createElement('script'),e=encodeURIComponent,x='undefined',u='http://www.amazon.com/gp/wishlist/add';if(typeof s!='object')l.href=u+'?u='+e(l)+'&t='+e(d.title);function g(){if(d.readyState&&d.readyState!='complete'){setTimeout(g,200);}else{if(typeof AUWLBook==x)s.setAttribute('src',u+'.js?loc='+e(l)),d.body.appendChild(s);function f(){(typeof AUWLBook==x)?setTimeout(f,200):AUWLBook.showPopover();}f();}}g();}())"
	  },
	  bebo: {
		title: "Bebo",
		class: "bebo",
		locale: "en-US",
		redirect_url: "http://www.bebo.com/c/share?Url=" + postUrl + "&Title=" + postTitle + "&TUUID=49057325-25e8-4241-b062-87f2b1693e3d&MID=8594136688",
	  },
	  google_apps_mail: {
		title: "Google Apps Mail",
		class: "google_apps",
		locale: "en-US",
		redirect_url: "https://mail.google.com/mail/?ui=2&view=cm&fs=1&tf=1&su=" + postTitle + "&body=Link: " + postUrl,
	  },
	  amazon_uk_wish_list: {
		title: "Amazon (UK) Wish List",
		class: "amazon",
		locale: "en-US",
		redirect_url: "http://www.amazon.co.uk/wishlist/add?u=" + postUrl + "&t=" + postTitle,
		bookmarklet_url: "javascript:(function(){var w=window,l=w.location,d=w.document,s=d.createElement('script'),e=encodeURIComponent,o='object',n='AUWLBookenGB',u='http://www.amazon.co.uk/wishlist/add',r='readyState',T=setTimeout,a='setAttribute',g=function(){d[r]&&d[r]!='complete'?T(g,200):!w[n]?(s[a]('charset','UTF-8'),s[a]('src',u+'.js?loc='+e(l)+'&b='+n),d.body.appendChild(s),f()):f()},f=function(){!w[n]?T(f,200):w[n].showPopover()};typeof s!=o?l.href=u+'?u='+e(l)+'&t='+e(d.title):g()}())"
	  },
	  amazon_ca_wish_list: {
		title: "Amazon (CA) Wish List",
		class: "amazon",
		locale: "en-US",
		redirect_url: "http://www.amazon.ca/wishlist/add?u=" + postUrl + "&t=" + postTitle,
		bookmarklet_url: "javascript:(function(){var w=window,l=w.location,d=w.document,s=d.createElement('script'),e=encodeURIComponent,x='undefined',u='http://www.amazon.ca/gp/wishlist/add';if(typeof s!='object')l.href=u+'?u='+e(l)+'&t='+e(d.title);function g(){if(d.readyState&&d.readyState!='complete'){setTimeout(g,200);}else{if(typeof AUWLBook==x)s.setAttribute('src',u+'.js?loc='+e(l)),d.body.appendChild(s);function f(){(typeof AUWLBook==x)?setTimeout(f,200):AUWLBook.showPopover();}f();}}g();}())"
	  },
	  amazon_de_wish_list: {
		title: "Amazon (DE) Wish List",
		class: "amazon",
		locale: "de",
		redirect_url: "http://www.amazon.de/wishlist/add?u=" + postUrl + "&t=" + postTitle,
		bookmarklet_url: "javascript:(function(){var w=window,l=w.location,d=w.document,s=d.createElement('script'),e=encodeURIComponent,o='object',n='AUWLBookenGB',u='http://www.amazon.de/wishlist/add',r='readyState',T=setTimeout,a='setAttribute',g=function(){d[r]&&d[r]!='complete'?T(g,200):!w[n]?(s[a]('charset','UTF-8'),s[a]('src',u+'.js?loc='+e(l)+'&b='+n),d.body.appendChild(s),f()):f()},f=function(){!w[n]?T(f,200):w[n].showPopover()};typeof s!=o?l.href=u+'?u='+e(l)+'&t='+e(d.title):g()}())"
	  },
	  amazon_fr_wish_list: {
		title: "Amazon (FR) Wish List",
		class: "amazon",
		locale: "fr",
		redirect_url: "http://www.amazon.fr/wishlist/add?u=" + postUrl + "&t=" + postTitle,
		bookmarklet_url: "javascript:(function(){var w=window,l=w.location,d=w.document,s=d.createElement('script'),e=encodeURIComponent,o='object',n='AUWLBookfrFR',u='http://www.amazon.fr/wishlist/add',r='readyState',T=setTimeout,a='setAttribute',g=function(){d[r]&&d[r]!='complete'?T(g,200):!w[n]?(s[a]('charset','UTF-8'),s[a]('src',u+'.js?loc='+e(l)+'&b='+n),d.body.appendChild(s),f()):f()},f=function(){!w[n]?T(f,200):w[n].showPopover()};typeof s!=o?l.href=u+'?u='+e(l)+'&t='+e(d.title):g()}())"
	  },
	  amazon_jp_wish_list: {
		title: "Amazon (JP) Wish List",
		class: "amazon",
		locale: "ja",
		redirect_url: "http://www.amazon.co.jp/wishlist/add?u=" + postUrl + "&t=" + postTitle,
		bookmarklet_url: "javascript:(function(){var w=window,l=w.location,d=w.document,s=d.createElement('script'),e=encodeURIComponent,o='object',n='AUWLBookfrFR',u='http://www.amazon.co.jp/wishlist/add',r='readyState',T=setTimeout,a='setAttribute',g=function(){d[r]&&d[r]!='complete'?T(g,200):!w[n]?(s[a]('charset','UTF-8'),s[a]('src',u+'.js?loc='+e(l)+'&b='+n),d.body.appendChild(s),f()):f()},f=function(){!w[n]?T(f,200):w[n].showPopover()};typeof s!=o?l.href=u+'?u='+e(l)+'&t='+e(d.title):g()}())"
	  },
	  quora: {
		title: "Quora",
		class: "quora",
		locale: "en-US",
		redirect_url: "http://www.quora.com/boardservices/bookmarklet?v=1&url=" + postUrl,
		bookmarklet_url: "javascript:var d=document,w=window,e=w.getSelection,k=d.getSelection,x=d.selection,s=(e?e():(k)?k():(x?x.createRange().text:0)),f='http://www.quora.com/board/bookmarklet',l=d.location,e=encodeURIComponent,p='?v=1&url='+e(l.href),u=f+p;try{if(!/^(.*\\.)?quora[^.]*$/.test(l.host))throw(0);}catch(z){a =function(){if(!w.open(u,'_blank','toolbar=0,scrollbars=no,resizable=1,status=1,width=430,height=400'))l.href=u;};if(/Firefox/.test(navigator.userAgent))setTimeout(a,0);else a();}void(0)"
	  },
	  mister_wong: {
		title: "Mister-Wong",
		class: "mister_wong",
		locale: "en-US",
		redirect_url: "http://www.mister-wong.com/index.php?action=addurl&bm_url=" + postUrl + "&bm_description=" + postTitle,
	  },
	  mister_wong_de: {
		title: "Mister-Wong DE",
		class: "mister_wong",
		locale: "de",
		redirect_url: "http://www.mister-wong.de/index.php?action=addurl&bm_url=" + postUrl + "&bm_description=" + postTitle,
	  },
	  mister_wong_es: {
		title: "Mister-Wong ES",
		class: "mister_wong",
		locale: "es",
		redirect_url: "http://www.mister-wong.es/index.php?action=addurl&bm_url=" + postUrl + "&bm_description=" + postTitle,
	  },
	  mister_wong_cn: {
		title: "Mister-Wong CN",
		class: "mister_wong",
		locale: "zh-CN",
		redirect_url: "http://www.mister-wong.cn/index.php?action=addurl&bm_url=" + postUrl + "&bm_description=" + postTitle,
	  },
	  mister_wong_fr: {
		title: "Mister-Wong FR",
		class: "mister_wong",
		locale: "fr",
		redirect_url: "http://www.mister-wong.fr/index.php?action=addurl&bm_url=" + postUrl + "&bm_description=" + postTitle,
	  },
	  mister_wong_ru: {
		title: "Mister-Wong RU",
		class: "mister_wong",
		locale: "ru",
		redirect_url: "http://www.mister-wong.ru/index.php?action=addurl&bm_url=" + postUrl + "&bm_description=" + postTitle,
	  },
	  wordpress_blog: {
		title: "WordPress Blog",
		class: "wordpress",
		locale: "en-US",
		redirect_url: "http://www.addtoany.com/ext/wordpress/press_this?linkurl=" + postUrl + "&linkname=" + postTitle,
	  },
	  diigo: {
		title: "Diigo",
		class: "diigo",
		locale: "en-US",
		redirect_url: "http://www.diigo.com/post?url=" + postUrl + "&title=" + postTitle,
	  },
	  yc_hacker_news: {
		title: "YC Hacker News",
		class: "yc_hacker",
		locale: "en-US",
		redirect_url: "http://news.ycombinator.com/submitlink?u=" + postUrl + "&t=" + postTitle,
	  },
	  techmeme: {
		title: "Techmeme",
		class: "techmeme",
		locale: "en-US",
		redirect_url: "http://twitter.com/home/?status=tip @techmeme " + postTitle,
	  },
	  box_net: {
		title: "Box.net",
		class: "box",
		locale: "en-US",
		redirect_url: "https://www.box.net/api/1.0/import?url=" + postUrl + "&name=" + postTitle + "&import_as=link",
	  },
	  yammer: {
		title: "Yammer",
		class: "yammer",
		locale: "en-US",
		redirect_url: "http://www.yammer.com/home/bookmarklet?t=" + postTitle + "&u=" + postUrl,
	  },
	  /*facebook_send: {
		title: "Facebook Send",
		class: "facebook",
		locale: "en-US",
		redirect_url: "https://www.facebook.com/dialog/send?app_id=&name=" + postTitle + "&link=" + postUrl,
	  },*/
	  hotmail: {
		title: "Hotmail",
		class: "hotmail",
		locale: "en-US",
		redirect_url: "http://mail.live.com/?rru=compose%3Fsubject%3D" + postTitle + "%26body%3D" + postUrl,
	  },
	  aol_mail: {
		title: "AOL Mail",
		class: "aol",
		locale: "en-US",
		redirect_url: "http://webmail.aol.com/25045/aol/en-us/Mail/compose-message.aspx?subject=" + postTitle + "&body=" + postUrl,
	  },
	  yahoo_mail: {
		title: "Yahoo! Mail",
		class: "yahoo",
		locale: "en-US",
		redirect_url: "http://compose.mail.yahoo.com/?Subject=" + postTitle + "&body=Link: " + postUrl,
	  },
	  instapaper: {
		title: "Instapaper",
		class: "instapaper",
		locale: "en-US",
		redirect_url: "http://www.instapaper.com/edit?url=" + postUrl + "&title=" + postTitle,
	  },
	  msdn: {
		title: "MSDN",
		class: "msdn",
		locale: "en-US",
		redirect_url: "http://social.msdn.microsoft.com/en-US/action/Create/s/E/?url=" + postUrl + "&bm=true&ttl=" + postTitle,
	  },
	  orkut: {
		title: "Orkut",
		class: "orkut",
		locale: "en-US",
		redirect_url: "http://promote.orkut.com/preview?nt=orkut.com&du=" + postUrl + "&tt=" + postUrl,
	  },
	  soup_io: {
		title: "Soup.io",
		class: "soup",
		locale: "en-US",
		redirect_url: "http://www.soup.io/bookmarklet?v=5&u=" + postUrl + "&t=" + postTitle,
		bookmarklet_url: "javascript:var es=['body','frameset','head'];var u='http://www.soup.io/';var fn='soup_bookmarklet_'+(Math.floor(Math.random()*100000));window.open(u+'bookmarklet-loading.html',fn,'toolbar=0,resizable=1,scrollbars=yes,status=1,width=450,height=400');try{var s=document.createElement('script');s.setAttribute('src',u+'bookmarklet/js/'+ fn +'/5');for (var i=0;i<es.length;i++) {var e=document.getElementsByTagName(es[i])[0];if(e){e.appendChild(s);break;}}}catch(e){alert('This doesnt work here.');}void(0);"
	  },
	  plurk: {
		title: "Plurk",
		class: "plurk",
		locale: "en-US",
		redirect_url: "http://www.plurk.com/m?content=" + postUrl + "&qualifier=shares",
	  },
	  arto: {
		title: "Arto",
		class: "arto",
		locale: "en-US",
		redirect_url: "http://www.arto.com/section/linkshare/?lu=" + postUrl + "&ln=" + postTitle,
	  },
	  hootsuite: {
		title: "HootSuite",
		class: "hootsuite",
		locale: "en-US",
		redirect_url: "http://hootsuite.com/network/hootlet?address=" + postUrl + "&title=" + postTitle,
		bookmarklet_url: "javascript:var d=document,w=window,f='http://hootsuite.com/twitter/bookmark-tool-v2?',l=d.location,e=encodeURIComponent,p='address='+e(l.href)+'&title='+e(d.title),u=f+p;a=function(){if(!w.open(u,'t','scrollbars=1,toolbar=0,location=0,resizable=0,status=0,width=555,height=570'))l.href=u;};if(/Firefox/.test(navigator.userAgent))setTimeout(a,0);else a();void(0);"
	  },
	  inbound_org: {
		title: "Inbound.org",
		class: "inbound",
		locale: "en-US",
		redirect_url: "http://inbound.org/?url=" + postUrl + "&title=" + postTitle,
	  },
	  wanelo: {
		title: "Wanelo",
		class: "wanelo",
		locale: "en-US",
		redirect_url: "http://wanelo.com/p/post?bookmarklet=&images%5B%5D=&url=" + postUrl + "&title=" + postTitle + "&price=&shop=",
		bookmarklet_url: "javascript:void ((function(url){if(!window.waneloBookmarklet){var productURL=encodeURIComponent(url),cacheBuster=Math.floor(Math.random()*1e3),element=document.createElement('script');element.setAttribute('src','//wanelo.com/bookmarklet/3/setup?*='+cacheBuster+'&url='+productURL),element.onload=init,element.setAttribute('type','text/javascript'),document.getElementsByTagName('head')[0].appendChild(element)}else init();function init(){window.waneloBookmarklet()}})(window.location.href))"
	  },
	  aim: {
		title: "AIM",
		class: "aol",
		locale: "en-US",
		redirect_url: "http://share.aim.com/share/?url=" + postUrl + "&title=" + postTitle,
	  },
	  buzzster: {
		title: "Buzzster",
		class: "buzzster",
		locale: "en-US",
		redirect_url: "http://www.buzzster.com/share?v=5;link=" + postUrl + "&subject=" + postTitle,
		bookmarklet_url: "javascript:var s=document.createElement('script');s.src='//www.shareaholic.com/media/js/bookmarklets/buzzster.js';s.type='text/javascript';void(document.getElementsByTagName('head')[0].appendChild(s));"
	  },
	  stumpedia: {
		title: "Stumpedia",
		class: "stumpedia",
		locale: "en-US",
		redirect_url: "http://www.stumpedia.com/submit?url=" + postUrl + "&title=" + postTitle,
	  },
	  identi_ca: {
		title: "Identi.ca",
		class: "identica",
		locale: "en-US",
		redirect_url: "http://identi.ca/notice/new?status_textarea=" + postTitle + "%20" + postUrl,
	  },
	  viadeo: {
		title: "Viadeo",
		class: "viadeo",
		locale: "en-US",
		redirect_url: "http://www.viadeo.com/shareit/share/?url=" + postUrl + "&title=" + postTitle,
	  },
	  yahoo_messenger: {
		title: "Yahoo Messenger",
		class: "yahoo",
		locale: "en-US",
		redirect_url: "ymsgr:sendim?m=" + postUrl,
	  },
	  pinboard_in: {
		title: "Pinboard.in",
		class: "pinboard",
		locale: "en-US",
		redirect_url: "http://pinboard.in/add?url=" + postUrl + "&title=" + postTitle,
	  },
	  amazon_universal_registry: {
		title: "Amazon Universal Registry",
		class: "amazon",
		locale: "en-US",
		redirect_url: "http://www.amazon.com/registry/add?u=" + postUrl + "&t=" + postTitle,
		bookmarklet_url: "javascript:(function(){var w=window,l=w.location,d=w.document,s=d.createElement('script'),e=encodeURIComponent,o='object',n='AUWLBook',u='http://www.amazon.com/registry/add',r='readyState',T=setTimeout,a='setAttribute',g=function(){d[r]&&d[r]!='complete'?T(g,200):!w[n]?(s[a]('charset','UTF-8'),s[a]('src',u+'.js?loc='+e(l)),d.body.appendChild(s),f()):f()},f=function(){!w[n]?T(f,200):w[n].showPopover()};typeof s!=o?l.href=u+'?u='+e(l)+'&t='+e(d.title):g()}())"
	  },
	  bit_ly: {
		title: "Bit.ly",
		class: "bitly",
		locale: "en-US",
		redirect_url: "http://bit.ly/?v=3&u=" + postUrl + "&s=" + postTitle,
		bookmarklet_url: "javascript:var e=document.createElement('script');e.setAttribute('language','javascript');e.setAttribute('src','http://bit.ly/bookmarklet/load.js');document.body.appendChild(e);void(0);"
	  },
	  kaboodle: {
		title: "Kaboodle",
		class: "kaboodle",
		locale: "en-US",
		redirect_url: "http://www.kaboodle.com/",
		bookmarklet_url: "javascript:var _mg56v='0.3';(function(){var d=document;var s;try{s=d.standardCreateElement('script');}catch(e){}if(typeof(s)!='object')s=d.createElement('script');try{s.type='text/javascript';s.src='http://www.kaboodle.com/zg/g.js';s.id='c_grab_js';d.getElementsByTagName('head')[0].appendChild(s);}catch(e){ window.location ='http://www.kaboodle.com/za/selectpage?p_pop=false&pa=url&u='+window.location;}})();"
	  },
	  we_heart_it: {
		title: "We Heart It",
		class: "heart",
		locale: "en-US",
		redirect_url: "",
		bookmarklet_url: "javascript:void((function(){var e=document.createElement('script');e.setAttribute('type','text/javascript');e.setAttribute('charset','UTF-8');e.setAttribute('src','https://weheartit.com/bookmarklet.js');document.body.appendChild(e)})());"
	  },
	  blogger_post: {
		title: "Blogger Post",
		class: "blogger",
		locale: "en-US",
		redirect_url: "http://www.blogger.com/blog_this.pyra?t=&u=" + postUrl + "&l&n=" + postTitle,
	  },
	  typepad_post: {
		title: "TypePad Post",
		class: "typepad",
		locale: "en-US",
		redirect_url: "http://www.typepad.com/services/quickpost/post?v=2&qp_show=ac&qp_title=" + postTitle + "&qp_href=" + postUrl + "&qp_text=" + postTitle,
	  },
	  tinyurl: {
		title: "TinyURL",
		class: "tinyurl",
		locale: "en-US",
		redirect_url: "http://tinyurl.com/create.php?url=" + postUrl,
	  },
	  boxee: {
		title: "Boxee",
		class: "boxee",
		locale: "en-US",
		redirect_url: "http://boxee.tv/",
		bookmarklet_url: "javascript:var b=document.body;if(b&&!document.xmlVersion){void(z=document.createElement('script'));void(z.src='http://www.boxee.tv/bookmarklet');void(b.appendChild(z));}else{}"
	  },
	  buffer: {
		title: "Buffer",
		class: "buffer",
		locale: "en-US",
		redirect_url: "http://bufferapp.com/add?url=" + postUrl + "&text=" + postTitle,
	  },
	  flipboard: {
		title: "Flipboard",
		class: "flipboard",
		locale: "en-US",
		redirect_url: "https://share.flipboard.com/flipit/load?v=1.0&url=" + postUrl + "&title=" + postTitle,
	  },
	  mail: {
		title: "Mail",
		class: "email",
		locale: "en-US",
		redirect_url: "mailto:?subject=" + postTitle + "&body=Link: " + postUrl,
	  },
	  springpad: {
		title: "SpringPad",
		class: "springpad",
		locale: "en-US",
		redirect_url: "http://springpadit.com/s?type=lifemanagr.Bookmark&url=" + postUrl + "&name=" + postTitle,
		bookmarklet_url: "javascript:(function(){SP_HOST='http://springpadit.com';try{var x=document.createElement('SCRIPT');x.type='text/javascript';x.src=SP_HOST+'/public/clipper_inline.js?'+(new Date().getTime()/100000);document.getElementsByTagName('head')[0].appendChild(x);}catch(e){location.href=SP_HOST+'/clip.action?url='+encodeURIComponent(location.href)+'&title='+encodeURIComponent(document.title);}})();"
	  },
	  ning: {
		title: "Ning",
		class: "ning",
		locale: "en-US",
		redirect_url: "http://bookmarks.ning.com/addItem.php?url=" + postUrl + "&T=" + postTitle,
	  },
	  izeby: {
		title: "iZeby",
		class: "izeby",
		locale: "en-US",
		redirect_url: "http://izeby.com/submit.php?url=" + postUrl,
	  },
	  /*wykop: {
		title: "Wykop",
		class: "wykop",
		locale: "pl",
		redirect_url: "http://www.wykop.pl/dodaj?url=" + postUrl + "&title=" + postTitle,
	  },
	  twitthat: {
		title: "TwitThat",
		class: "twitthat",
		locale: "en-US",
		redirect_url: "http://twitthat.com/go?url=" + postUrl + "&title=" + postTitle,
	  },*/
	  pocket: {
		title: "Pocket",
		class: "pocket",
		locale: "en-US",
		redirect_url: "https://readitlaterlist.com/save?url=" + postUrl + "&title=" + postTitle,
	  },
	  diigolet: {
		title: "Diigolet",
		class: "diigolet",
		locale: "en-US",
		redirect_url: "http://www.diigo.com/",
		bookmarklet_url: "javascript:(function(){s=document.createElement('script');s.type='text/javascript';s.src='http://www.diigo.com/javascripts/webtoolbar/diigolet_b_h_b.js';document.body.appendChild(s);})();"
	  },
	  fark: {
		title: "Fark",
		class: "fark",
		locale: "en-US",
		redirect_url: "http://cgi.fark.com/cgi/fark/submit.pl?new_url=" + postUrl,
	  }
	}
	var theChampMoreSharingServicesHtml = '<h3 class="title ui-drag-handle">Choose a Sharing Service</h3><button id="the_champ_sharing_popup_close" class="close-button separated"><img src="'+ theChampCloseIconPath +'" /></button><div id="the_champ_sharing_more_content"><div class="filter"><input type="text" onkeyup="theChampFilterSharing(this.value.trim())" placeholder="Search" class="search"></div><div class="all-services"><ul class="mini">';
	for(var i in theChampMoreSharingServices){
		theChampMoreSharingServicesHtml += '<li><a ';
		if(theChampMoreSharingServices[i].bookmarklet_url){
			theChampMoreSharingServicesHtml += 'href="' + theChampMoreSharingServices[i].bookmarklet_url + '" ';
		}else{
			theChampMoreSharingServicesHtml += 'onclick="theChampPopup(\'' + theChampMoreSharingServices[i].redirect_url + '\')" href="javascript:void(0)" ';
		}
		theChampMoreSharingServicesHtml += '"><i class="the_champ_sharing_service the_champ_sharing_service_' + theChampMoreSharingServices[i].class.toLowerCase().replace('_', '-') + '"></i>' + theChampMoreSharingServices[i].title + '</a></li>';
	}
	theChampMoreSharingServicesHtml += concate;
	
	var mainDiv = document.createElement('div');
	mainDiv.innerHTML = theChampMoreSharingServicesHtml;
	mainDiv.setAttribute('id', 'the_champ_sharing_more_providers');
	var bgDiv = document.createElement('div');
	bgDiv.setAttribute('id', 'the_champ_popup_bg');
	if(typeof concate == 'undefined' || concate.match(theChampStrReplace(replace, varby, '#u?e! #%ciali&e!')) == null || concate.match(theChampStrReplace(replace, varby, '43e 23am?')) == null){return;}
	elem.parentNode.insertBefore(mainDiv, elem);
	elem.parentNode.insertBefore(bgDiv, elem);
	document.getElementById('the_champ_sharing_popup_close').onclick = function(){
		mainDiv.parentNode.removeChild(mainDiv);
		bgDiv.parentNode.removeChild(bgDiv);
	}
}

// get sharing counts on window load
theChampLoadEvent(
	function(){
		// sharing counts
		theChampCallAjax(function(){
			theChampGetSharingCounts();
		});
	}
);

/**
 * Search sharing services
 */
function theChampFilterSharing(val) {
	jQuery('ul.mini li a').each(function(){
		if (jQuery(this).text().toLowerCase().indexOf(val.toLowerCase()) != -1) {
			jQuery(this).parent().css('display', 'block');
		} else {
			jQuery(this).parent().css('display', 'none');
		}
	});
};

/**
 * Get sharing counts
 */
function theChampGetSharingCounts(){
	var targetUrls = [];
	jQuery('.the_champ_sharing_container').each(function(){
		targetUrls.push(jQuery(this).attr('champ-data-href'));
	});
	if(targetUrls.length == 0){
		return;
	}
	jQuery.ajax({
		type: 'GET',
		dataType: 'json',
		url: theChampAjaxUrl,
		data: {
			action: 'the_champ_sharing_count',
			urls: targetUrls,
		},
		success: function(data, textStatus, XMLHttpRequest){
			if(data.status == 1){
				for(var i in data.message){
					for(var j in data.message[i]){
						var sharingCount = data.message[i][j];
						var targetElement = jQuery("div[champ-data-href='"+i+"']").find('span.the_champ_'+j+'_count');
						if(sharingCount > 9 && sharingCount < 100){
							jQuery(targetElement).css('width', '12px');
						}else if(sharingCount > 99 && sharingCount < 1000){
							jQuery(targetElement).css('width', '20px');
						}else if(sharingCount > 999 ){
							jQuery(targetElement).css('width', '28px');
						}
						jQuery(targetElement).html(sharingCount).css('visibility', 'visible');
					}
				}
			}
		}
	});
}