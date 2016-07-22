$(document).ready(function(){
     
     /*$('body').css("background", "url("+imgRoot+"bg-body.jpg) repeat");
     
     $('#background').css("background", "url("+imgRoot+"bg-page.jpg) no-repeat top center");
     
     $('#header').css("background", "url("+imgRoot+"bg-header2.png) repeat-x bottom center");*/
     
     alert($('body').css('background-image'));
    
     $('*').each(function(){
          if ($(this).css('background-image').indexOf("imgRoot") >= 0) {
               var str = $(this).css('background-image');
               var res = str.replace('/imgRoot', imgRoot);
               $(this).css('background-image', res);  
          }
     });

     
});