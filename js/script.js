var choco = null;
var colorThief = new ColorThief();
function main(obj, friends, settings){

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
            setWebsiteColor(choco, settings['background_mode']);
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
              setWebsiteColor(choco, settings['background_mode']);
               $(this).addClass("selected");
             }
         });

       });

}
