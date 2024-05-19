jQuery(document).ready(function($) {

  // Preloader
  $(window).on('load', function () {
	$('#preloader').fadeOut(500);
  });
   
     
  $('a.next').text('→');
  $('a.prev').text('←');

  if (window.location.href.indexOf("home") > -1) {
    $('body').addClass('home_not_set');
  }

  // Mobile Menu Toggle
  $(".elementor-widget-navigation-menu").prepend('<div class="burger_menu navbar"><button type="button" class="burger" id="burger"><span class="burger-line"></span><span class="burger-line"></span><span class="burger-line"></span><span class="burger-line"></span></button></div>');

  $(".burger").click(function() {
    $(this).toggleClass("is-active");
    $(".elementor-widget-navigation-menu ul.menu").slideToggle();
    $(".elementor-widget-navigation-menu ul.menu").toggleClass("check"); 
  });

  if ($(window).width() < 1025){
    $('.elementor-widget-navigation-menu ul.menu li.menu-item-has-children > a').on('click',function(event){
      event.preventDefault();
      $(this).toggleClass('selected');
      $(this).parent().find('ul').first().toggle(0);
      $(this).parent().siblings().find('ul').hide(0);
    
      //Hide menu when clicked outside
      $(this).parent().find('ul').parent().mouseleave(function(){
        var thisUI = $(this);
        $('html').click(function(){
          thisUI.children("ul.menu > ul.sub-menu").hide();
          thisUI.children("a").removeClass('selected');
          $('html').unbind('click');
        });
      });		
    });
  }


});
