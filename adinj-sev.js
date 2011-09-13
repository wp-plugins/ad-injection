/*
Part of the Ad Injection plugin for WordPress
http://www.reviewmylife.co.uk/blog/
*/

function adinj_check_referrer(){
	if (adinj_blocked()){
		return;
	}
	adinj_searchenginevisitor();
}

// Based on code from 
// http://www.scratch99.com/2008/09/avoid-smart-pricing-show-adsense-only-to-search-engine-visitors/
function adinj_searchenginevisitor(){
	if (adinj_searchEngineCookieSet()){
		return true;
	}
	var referrers = adinj_allowed_referrers_setting();
	var length = referrers.length;
	for (var i=0; i<length; ++i) {
		if (document.referrer.indexOf(referrers[i])!==-1) {
			var expiry = new Date();
			expiry.setTime(expiry.getTime() + 1000*60*60); // 1 hour
			document.cookie = "adinj=1; expires=" + expiry.toGMTString() + "; path=/; ";
			return true;
		}
	}
	return false;
}

function adinj_allowed_referrers_setting(){
	if (typeof adinj_referrers != 'undefined'){
		return adinj_referrers;
	} else {
		document.write("<!--ADINJ DEBUG: couldn't find adinj_referrers value. Using defaults.-->");
		return new Array('.google.', '.bing.', '.yahoo.', '.ask.', 'search?', 'search.');
	}
}

function adinj_blocked(){
	if (adinj_searchEngineCookieSet()){
		return false;
	}
	if (adinj_blockedCookieSet()){
		return true;
	}
	var blocked = adinj_blocked_referrers_setting();
	var length = blocked.length;
	for (var i=0; i<length; ++i) {
		if (document.referrer.indexOf(blocked[i])!==-1){
			var expiry = new Date();
			expiry.setTime(expiry.getTime() + 1000*60*60*adinj_blocked_hours_setting());
			document.cookie = "adinjblocked=1; expires=" + expiry.toGMTString() + "; path=/; ";
			return true;
		}
	}
	return false;
}

function adinj_blocked_referrers_setting(){
	if (typeof adinj_blocked_referrers != 'undefined'){
		return adinj_blocked_referrers;
	} else {
		document.write("<!--ADINJ DEBUG: couldn't find adinj_blocked_referrers value. Using default empty array.-->");
		return new Array();
	}
}

function adinj_blocked_hours_setting(){
	if (typeof adinj_blocked_hours != 'undefined'){
		return adinj_blocked_hours;
	} else {
		document.write("<!--ADINJ DEBUG: couldn't find adinj_blocked_hours value. Using default value.-->");
		return 24; //hours
	}
}

function adinj_searchEngineCookieSet() {
	var results = document.cookie.match('(^|;) ?adinj=([^;]*)(;|$)');
	if(results){
		return true;
	}
	return false;
}

function adinj_blockedCookieSet() {
	var results = document.cookie.match('(^|;) ?adinjblocked=([^;]*)(;|$)');
	if(results){
		return true;
	}
	return false;
}
