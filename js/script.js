var choco = null;

function main(obj, friends){

    $("#photos").height($(window).height() - 120);

    var currentnb = 0;
    $(".gallerie").click(function(){
      if (choco !== null) {
        $("#photos").empty();
        choco.close();
      }

      $("#thumbs").empty();
      $("#serieTitle").html($(this).html());
      var id = $(this).attr('id');
      var scrollId = string_to_slug(id + "_scroller");
      var photos = obj[id];
      var loadedPhotos = 0;
      currentnb = 0;
      var cpt = 0;
      var links = [];
      var allImages = [];
      for(var key in photos){
        cpt++;
        var photo = $(this).attr('href') + "/" + photos[key];
        var caption = "";
        if(obj["captions"] !== undefined && obj["captions"][photos[key]] !== undefined) {
          caption = obj["captions"][photos[key]];
        }
        allImages.push({ src : photo, title: caption});

		    thumb = [];
        thumb[cpt] = new Image();
        $(thumb[cpt]).attr("src", $(this).attr('href') + "/thumb/" + photos[key]);
        $(thumb[cpt]).attr("nb", key);

        $(thumb[cpt]).load( function() {
          var count = $(this).attr("nb");
          var link = $('<a href="#"></a>');
          link.append(this);
          links[count] = link;
          loadedPhotos++;
          if (loadedPhotos == count) {
            $("#thumbs").append(link);
          } else if (loadedPhotos > count) {
            for (var i = count; i <= loadedPhotos; i++) {
              $("#thumbs").append(links[i]);
            }
          }

          if(loadedPhotos == photos.length) {
            displayThumbs(scrollId);
            $("#thumbs img").on("click", function(){
              choco.goto($(this).attr("nb"));
              $("#thumbs img.selected").removeClass("selected");
              $(this).addClass("selected");
            });
          }
        });
      }

      choco = $('#photos').Chocolat({
        container : '#photos',
        closeImg : "",
        separator1: "",
        images : allImages,
        preventClose : true
      }).data('api-chocolat');

      choco.open();
      return false;
    });

    $("#friends").click(function(){
      $("#photos").empty();
      $("#thumbs").empty();
      $("#serieTitle").html("Friends");
      for(var friend in friends){
         var ul = $("#photos").append("<ul id='listFriends'></ul>")
        $("#listFriends").append('<li class="friend"><span class="friendName">' + friend + '</span> : <a href ="' + friends[friend] + '" target="_blank">' + friends[friend] + '</a></li>');
        }
    });

    $(window).resize(function() {
      $("#photos").height($(window).height()-120);
    });

    $(".expand").click(function(){
      var ul = $(this).children("ul");
      $(".expand ul:not(#" + ul.attr("id") + ")").css('opacity', 1)
               .slideUp()
               .animate(
                  { opacity: 0 },
                  { queue: false, duration: 'slow' }
                );

      if (ul.is(':visible')) {
        ul.css('opacity', 1)
               .slideUp()
               .animate(
                  { opacity: 0 },
                  { queue: false, duration: 'slow' }
                );

      } else {
        ul.css('opacity', 0)
          .slideDown()
          .animate(
                  { opacity: 1 },
                  { queue: false, duration: 'slow' }
                  );

      }
    });

     $("#viewer").on("changePage", function(e, data){
         var oldSelectedThumb = $("#thumbs img.selected");
         oldSelectedThumb.removeClass("selected");
         $("#thumbs").find('*[nb]').each(function(index){
             if ($(this).attr('nb') == data.page) {
               $(this).addClass("selected");
             }
         });

       });

}

function displayThumbs(scrollId) {
  $("#thumbs img:first").addClass("selected");
  $(".jTscroller").removeAttr("style");
  $(".jTscrollerContainer").removeAttr("style");
  $(".jThumbnailScroller").removeAttr("style");
  $(".jThumbnailScroller").attr("id", scrollId);
  $("#" + scrollId).thumbnailScroller({
    scrollerType:"hoverPrecise",
    scrollerOrientation:"horizontal",
    scrollSpeed:2,
    scrollEasing:"easeOutCirc",
    scrollEasingAmount:600,
    acceleration:4,
    scrollSpeed:800,
    noScrollCenterSpace:10,
    autoScrolling:0,
    autoScrollingSpeed:2000,
    autoScrollingEasing:"easeInOutQuad",
    autoScrollingDelay:500
  });
}

function string_to_slug(str) {
  str = str.replace(/^\s+|\s+$/g, ''); // trim
  str = str.toLowerCase();

  // remove accents, swap ñ for n, etc
  var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;&";
  var to   = "aaaaeeeeiiiioooouuuunc_______";
  for (var i=0, l=from.length ; i<l ; i++) {
    str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
  }

  str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
    .replace(/\s+/g, '_') // collapse whitespace and replace by -
    .replace(/-+/g, '_'); // collapse dashes

  return str;
}
