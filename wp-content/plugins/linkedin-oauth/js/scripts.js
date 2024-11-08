jQuery( document ).ready(function($) {
    var oldUrl = $('.linkedin-oauth-login-pop a').attr("href");
    if(oldUrl != undefined) {
    	var newUrl = oldUrl.replace("state=oiEWJOD82938ojdKK", "state="+menuitem.nonce);
    }
    $('.linkedin-oauth-login-pop a').attr("href", newUrl);
    if(menuitem.logged == 'on') {
        $('.linkedin-oauth-login-pop a').html(menuitem.text);
        $('.linkedin-oauth-login-pop a').attr("href", menuitem.lgurl);
    }
});