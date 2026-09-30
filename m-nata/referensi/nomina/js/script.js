var zx=jQuery.noConflict();

zx('#lightSlider').lightSlider({
    gallery: true,
    item: 1,
    loop:true,
    slideMargin: 0,
    thumbItem: 5,
  auto: true,
  loop: true,
  pause: 6900
});

zx("#lightSliderMobile").lightSlider({
    auto: true,
    item: 1,
    loop: true,
    slideMargin: 0,
    pause: 5500,
    keyPress: true,
    controls: true,
    prevHtml: "",
    nextHtml: "",
    pager: false,
    adaptiveHeight: true,
    enableTouch: false,
    enableDrag: false,
    freeMove: false
}); 

zx(document).ready(function(){var o=zx(".scrollTop");zx(window).scroll(function(){100<zx(this).scrollTop()?zx(o).css("opacity","0.5"):zx(o).css("opacity","0")}),zx(o).click(function(){return zx("html, body").animate({scrollTop:0},800),!1});var l=zx("#h1").position(),t=zx("#h2").position(),i=zx("#h3").position();zx(".link1").click(function(){return zx("html, body").animate({scrollTop:l.top},500),!1}),zx(".link2").click(function(){return zx("html, body").animate({scrollTop:t.top},500),!1}),zx(".link3").click(function(){return zx("html, body").animate({scrollTop:i.top},500),!1})});

zx(window).load(function() {
  zx('.flexslider').flexslider({
    animation: "fade",
    controlNav: "thumbnails",
    slideshow: true, 
    animationLoop: true,  
    slideshowSpeed: 3000, 
    directionNav: false,  
    touch: false,  
  });
});


zx(window).load(function() {
  zx('.flexslider-mobile').flexslider({
    animation: "slide",
    slideshow: true, 
    animationLoop: true,  
    slideshowSpeed: 3000, 
    touch: false,  
    controlNav: false,
    directionNav: true
  });
});

/* zx(window).load(function() {
  zx('.pilihan').flexslider({
    animation: "slide",
    animationLoop: true,
    itemWidth: 210,
    itemMargin: 3,
    minItems: 3,
    maxItems: 3
  });
});  */

  zx(".owl-carousel").owlCarousel({
    loop:true,
    items:3,
    margin:10,
    stagePadding: 0,
    nav:true,
    dots: false,
    dotsEach: false,
        autoplay:false,
    autoplayTimeout:5000,
    autoplayHoverPause:true,
       responsiveClass:true,
       responsive:{
        0:{
            items:2,
        },
       750:{
            items:2,
        },
       1000:{
            items:3,
        },
     }
  });

  zx("#berita-rekomendasi .owl-carousel").owlCarousel({
    loop:true,
    items:3,
    margin:10,
    stagePadding: 0,
    nav:true,
    dots: false,
    dotsEach: false,
        autoplay:false,
    autoplayTimeout:4000,
    autoplayHoverPause:true,
       responsiveClass:true,
  });

zx(".hamburger-button").click(function() {
    zx(".mobile-menu-kiri-wrap").fadeToggle();
    zx('body').css('overflow', 'hidden');
}),zx(".close-button-hamburger").click(function() {
    zx(".mobile-menu-kiri-wrap").hide();
  zx('body').css('overflow', 'visible');
})









if (zx(window).width() <= 460) {
    zx(".mobile-menu-kiri > li.menu-item-has-children > a").click(function(){
        zx(this).find('ul.sub-menu').toggle();
     zx(this).siblings().find("ul.sub-menu").hide();
    });
};

zx( "ul.mobile-menu-kiri li.menu-item-has-children:has(ul)" ).click(function(){ // When a li that has a ul is clicked ...
  zx(this).toggleClass('active'); // then toggle (add/remove) the class 'active' on it. 
});


zx(".close-button").click(function () {
        zx("#sidebar-banner-mobile-top-header-parallax").slideUp("slow");
   });

/* zx(".menu-utama .menu-item-has-children").click(function(){
  zx(".menu-utama .sub-menu").toggle();
}); */

zx(".close-button-sidebar-bawah").click(function () {
        zx("#sidebar-banner-bawah").hide();
   });

//add alt to headline image thumbnail
zx(".flex-control-thumbs img").attr('alt', 'Alternative text');

// click to copy to clipboard
var $temp = zx("<input>");
var $url = zx(location).attr('href');

zx('.clipboard').on('click', function() {
  zx("body").append($temp);
  $temp.val($url).select();
  document.execCommand("copy");
  $temp.remove();
  zx(".copied").fadeIn();
    setTimeout(function() {
      zx('.copied').fadeOut();
    }, 700); 
})

zx('.clipboard-mobile').on('click', function() {
  zx("body").append($temp);
  $temp.val($url).select();
  document.execCommand("copy");
  $temp.remove();
  zx(".copied-mobile").fadeIn();
    setTimeout(function() {
      zx('.copied-mobile').fadeOut();
    }, 700); 
})

// add container before youtube iframe

zx('iframe[src*="youtube"]').wrap("<div class='video-container'></div>");

zx('div.video-container').wrap("<div class='video-wrap'></div>");

zx('iframe.my-youtube-video').after("<button class='close-youtube'>x</button>");

zx("iframe[src^='https://www.youtube.com']").addClass("my-youtube-video");


/* zx(window).scroll(function(){
  var sticky = zx('header'),
      scroll = zx(window).scrollTop();

  if (scroll >= 480) sticky.addClass('fixed');
  else sticky.removeClass('fixed');
}); */


if (document.querySelector('.single-format-video')) {
  // youtube scroll to bottom

 var $window = zx(window);
var $videoWrap = zx('.video-wrap');
/* var $videoWrapSpan = zx('.video-wrap span');*/
var $video = zx('.video-container');
var $button = zx('.video-wrap button');
var videoHeight = $video.outerHeight();


$window.on('scroll',  function() {
  var windowScrollTop = $window.scrollTop();
  var videoBottom = videoHeight + $videoWrap.offset().top;
  
  if (windowScrollTop > videoBottom) {
    $videoWrap.height(videoHeight);
    $video.addClass('stuck');
/*  $videoWrapSpan.addClass('tes'); */
  $button.css("display", "block");
  } else {
    $videoWrap.height('auto');
    $video.removeClass('stuck');
  $button.css("display", "none");
  $videoWrap.css("display", "block");
    zx(".close-youtube").click(function () {
        zx(".video-wrap").hide();
   });
  }
}); 
 
}







  var swiper = new Swiper(".mySwiper", {
        slidesPerView: 4,
        freeMode: true,
     autoplay: {
          delay: 5000,
          disableOnInteraction: false,
        },
     spaceBetween: 3,
        watchSlidesProgress: true,
      });
      var swiper2 = new Swiper(".mySwiper2", {
        spaceBetween: 2,
       autoplay: {
          delay: 5000,
          disableOnInteraction: false,
        },
        navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev",
        },
        thumbs: {
          swiper: swiper,
        },
      });



zx(".caption-info").click(function() {
    zx(".caption-photo-buka-tutup").toggle()
});


// start marquee
function handleMarquee(){
  const marquee = document.querySelectorAll('.marquee-baru');
  let speed = 1;
  let lastScrollPos = 0;
  let timer;

  marquee.forEach(function(el){
    // stop animation on mouseenter
    mouseEntered = false;
    document.querySelector('.inner').addEventListener('mouseenter', function() {
      mouseEntered = true;
    })
    document.querySelector('.inner').addEventListener('mouseleave', function() {
      mouseEntered = false
    })

    const container = el.querySelector('.inner');
    const content = el.querySelector('.inner > *');
    //Get total width
    const  elWidth = content.offsetWidth;
    
    //Duplicate content
    let clone = content.cloneNode(true);
    container.appendChild(clone);
    
    let progress = 1;
    function loop(){
      if (mouseEntered === false) {progress = progress-speed;} 
      if (progress <= elWidth*-1) {progress=0;}
      container.style.transform = 'translateX(' + progress + 'px)';
      window.requestAnimationFrame(loop);
    }
    loop();
  });
  
  function handleSpeedClear(){
    speed = 4;
  }
};

handleMarquee();