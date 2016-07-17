$(document).ready(function(){

     $('#header a.logo img').mouseover(function(){
        $(this).attr("src", "/images/logo2-hover.png");  
     });
     
     $('#header a.logo img').mouseout(function(){
        $(this).attr("src", "/images/logo2.png");  
     });
     
     function mover(){
          $('#header a.logo img').trigger("mouseover");
     }
     
     function mout(){
          $('#header a.logo img').trigger("mouseout");
     }
     function logoLoop(){
          if( $('#header a.logo img').attr("src") != "/images/logo2-hover.png" ){
               setTimeout(mover, 500);
               setTimeout(mout, 700);
               setTimeout(mover, 900);
               setTimeout(mout, 1000);
          }
          var waitSec = ((Math.floor((Math.random() * 10) + 1)) * 1000) - 400;
          setTimeout(logoLoop, (waitSec));
     }
     
     logoLoop();
});

$(window).on("load", function() {
	$('div[class=loaderBox]').fadeOut(1000);
});

function setErrMsg(elementId, msg){
     clearErrMsg(elementId);
     $('#err_'+elementId).text(msg);
}

function clearErrMsg(elementId){
     $('#err_'+elementId).text("");
}

function clearAllErrMsg(){
     $('span[id^=err_]').each(function(){
          $(this).text("");
     });
}

$.fn.toObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

function validateEmail(str) {
     var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
     return regex.test(str);
}

function validateAccount(str){
     var regex = /^[A-Za-z][A-Za-z0-9]{3,15}$/;
     return regex.test(str);
}

function validatePassword(str){
     var regex = /^[A-Za-z][A-Za-z0-9]{5,15}$/;
     return regex.test(str);
}

function validateNickname(str){
     var regex = /^[A-Za-z0-9\u4e00-\u9fa5]{3,12}$/;
     return regex.test(str);
}
