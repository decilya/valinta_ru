window.onload = function()
{

snow(1);
}
function snow(id)
{
 var pos_x = Math.random()*(99 - 1)+1;
 pos_x = Math.floor(pos_x);
 if(pos_x >= 1 & pos_x <= 10){var q = -10;var png_sh = 1;}
 if(pos_x > 10 & pos_x <= 20){var q = 10;var png_sh = 3;}
 if(pos_x > 20 & pos_x <= 30){var q = -10;var png_sh = 0;}
 if(pos_x > 30 & pos_x <= 40){var q = 10;var png_sh = 2;}
 if(pos_x > 40 & pos_x <= 50){var q = -10;var png_sh = 1;}
 if(pos_x > 50 & pos_x <= 60){var q = 10;var png_sh = 3;}
 if(pos_x > 60 & pos_x <= 70){var q = -10;var png_sh = 0;}
 if(pos_x > 70 & pos_x <= 80){var q = 10;var png_sh = 2;}
 if(pos_x > 80 & pos_x <= 90){var q = -10;var png_sh = 1;}
 if(pos_x > 90 & pos_x <= 99){var q = 10;var png_sh = 3;}
 var end_x = pos_x+q;
    var img="<img id='snow_"+id+"' style='left:"+pos_x+"%;position:fixed;z-index:9999999' src='/libs/snow/new/snow_"+png_sh+".png'/>";
 //var img="<img id='snow_"+id+"' style='left:"+pos_x+"%; top:-10%; position:fixed;z-index:9999999' src='/templates/newipap/js/snow/show_"+png_sh+".png'/>";
 //   var img="<img id='snow_"+id+"' style='left:"+pos_x+"%; top:-10%; position:fixed;z-index:9999999' src='/templates/newipap/js/snow/snow"+png_sh+".png'/>";
  jQuery("#snow").append(img);
    var sh = document.body.scrollHeight;
   // console.log('Scroll Height', sh);

 move_show(id,end_x, sh);
 id++;
 setTimeout("snow("+id+");", 700);
}


function move_show(id,end_x, sh)
{
    var s = sh;
  jQuery("#snow_"+id).animate({top: s},20000,function()
 {

    // console.log(jQuery("#snow_"+id).position().top);


            jQuery("#snow_"+id).empty().remove();




 });
}