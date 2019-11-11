
$(document).ready(function(){

  var location = window.location.pathname;
  var cur_url = location;

  var middleMenu = $('.menu2 li');
  var bottomMenu = $('.b-bottom-nav .menu a').not(':first-child');

  middleMenu.each(function () {
    var link = $(this).find('a').attr('href');

    if (cur_url == link)
    {
      $(this).addClass('current');
    }
  });

  bottomMenu.each(function () {
    var link = $(this).attr('href');

    if (cur_url == link)
    {
      $(this).addClass('current2');
    }
  });

  $('#adminPages .navbar-brand').css({
    cursor: 'default',
    color: '#9d9d9d'
  });

  $('#adminPages .navbar-brand').on('click', function( e ){
    e.preventDefault();

  });


});