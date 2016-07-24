$(document).ready(function(){
     $('*').each(function(){
          if ($(this).css('background-image').indexOf("imgRoot") >= 0) {
               var str = $(this).css('background-image');
               var res = str.replace('/imgRoot', imgRoot);
               $(this).css('background-image', res);  
          }
     });
});