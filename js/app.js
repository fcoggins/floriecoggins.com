var $overlay = $('<div id="overlay"></div>');
var $image = $("<img>");
var $caption = $("<p></p>");

if ( $(window).width() > 480){

    $overlay.append($image);
    $overlay.append($caption);
    $('body').append($overlay);

    $("#gallery a").click(function(event){
      event.preventDefault();
      var imageLocation = $(this).attr("href");
      $image.attr('src', imageLocation);
      $overlay.show();
      var captionText = $(this).children('img').attr('alt');
      $caption.text(captionText);
    });

    $overlay.click(function(){
      $overlay.hide();
      });
} else {
    console.log("dummy code");
    $("#gallery a").click(function(event){
        event.preventDefault();
    });
}