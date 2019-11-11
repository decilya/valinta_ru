$(document).ready(function () {
  $('.b-wrap__attestat-img').on('mouseover', function(){
    $('.b-show-info').fadeIn(800);
    $('.b-wrap__attestat-img').on('mouseout', function(){
      $('.b-show-info').fadeOut(900);
    });
  });
});
