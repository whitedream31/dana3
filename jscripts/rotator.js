//<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js'></script>\n" .
//  <!-- By Dylan Wagstaff, http://www.alohatechsupport.net -->\n" .
function theRotator() {
  //Set the opacity of all images to 0\n" .
  $('div.rotator ul li').css({opacity: 0.0});
  //Get the first image and display it (gets set to full opacity)\n" .
  $('div.rotator ul li:first').css({opacity: 1.0});
  //Call the rotator function to run the slideshow, 6000 = change to next image after 6 seconds\n" .
  setInterval('rotate()',6000);
}

function rotate() {
  //Get the first image\n" .
  var current = ($('div.rotator ul li.show')? $('div.rotator ul li.show') : $('div.rotator ul li:first'));
  if ( current.length == 0 ) current = $('div.rotator ul li:first');
  //Get next image, when it reaches the end, rotate it back to the first image\n" .
  var next = ((current.next().length)
    ? ((current.next().hasClass('show')) ? $('div.rotator ul li:first') :current.next()) : $('div.rotator ul li:first'));
  //Un-comment the 3 lines below to get the images in random order\n" .
  //var sibs = current.siblings();\n" .
  //var rndNum = Math.floor(Math.random() * sibs.length );\n" .
  //var next = $( sibs[ rndNum ] );\n" .
  //Set the fade in effect for the next image, the show class has higher z-index\n" .
  next.css({opacity: 0.0})
    .addClass('show')
    .animate({opacity: 1.0}, 1500);
  //Hide the current image\n" .
  current.animate({opacity: 0.0}, 1500)
    .removeClass('show');
};

//$(document).ready(function() {
  //Load the slideshow\n" .
  theRotator();
  $('div.rotator').fadeIn(1500);
  $('div.rotator ul li').fadeIn(1500); // tweek for IE\n".

  $('#gallery a, div.rotator ul li.show').lightBox({
    imageLoading: 'images/lightbox/lightbox-ico-loading.gif',
    imageBtnPrev: 'images/lightbox/lightbox-btn-prev.gif',
    imageBtnNext: 'images/lightbox/lightbox-btn-next.gif',
    imageBtnClose: 'images/lightbox/lightbox-btn-close.gif',
    imageBlank: 'images/lightbox/lightbox-blank.gif',
    fixedNavigation: false,
    containerResizeSpeed: 400,
    overlayBgColor: '#ffffff',
    overlayOpacity: 0.78,
    txtImage: 'picture',
    txtOf: 'of'
  });

//});
