$(document).ready(function(){
     $('*').each(function(){
          if ($(this).css('background-image').indexOf("imgRoot") >= 0) {
               var str = $(this).css('background-image');
               var res = str.replace('/imgRoot', imgRoot);
               $(this).css('background-image', res);  
          }
     });
     
     $('*').each(function(){
          if ($(this).css('list-style-image').indexOf("imgRoot") >= 0) {
               var str = $(this).css('list-style-image');
               var res = str.replace('/imgRoot', imgRoot);
               $(this).css('list-style-image', res);  
          }
     });
});