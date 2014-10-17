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
