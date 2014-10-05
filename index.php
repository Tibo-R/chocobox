<?php
  include 'imagethumb.php';

  $galleries = array();
  $thumbs = array();

  $conf = json_decode(file_get_contents("admin/conf.json"), true);
  $cats = json_decode(file_get_contents("admin/categories.json"), true);
  $friends = json_decode(file_get_contents("admin/friends.json"), true);

  foreach ($cats as $key => $cat) {
    $cats[$key] = loadCategorie($cat, $key);
  }

  function loadCategorie($cat, $k){
    global $galleries;
    global $thumbs;
    if(isset($cat["sub"]) && $subCats = $cat["sub"]){
      foreach ($subCats as $key => $subCat) {
        $subCats[$key] = loadCategorie($subCat, $key);
      }
      $cat["sub"] = $subCats;
      $cat["id"] = $k;
      return $cat;
    } else {
      $nb = 0;
      $images = array();
      $rep = opendir($cat["path"]);
      if ($rep) {
        while (false !== ($image = readdir($rep))) {
          if ($image != "." && $image != ".." && $image != "thumb"){
            $nb = $nb+1;
            $images[] = $image;
            if(createThumbnail($cat["path"], $image)){
              $thumbs[] = "/thumb/" . $image;
            }
          }
        }

        $thumbs[$k] = $thumbs;
        natcasesort($images);
        foreach($images as $key => $image) {
          $galleries[$k][] = $image;
          if(isset($cat["captions"]) && isset($cat["captions"][$image])) {
            $galleries["captions"][$image] = $cat["captions"][$image];
          }
        }
        $cat["nb"] = $nb;
        $cat["id"] = $k;
        closedir($rep);
      }
      return $cat;
    }

  }

  function createThumbnail($path, $pic){
    if(!file_exists($path . "/thumb/")) {
      mkdir($path . "/thumb/");
    }
    if(file_exists($path . "/thumb/" . $pic)) {
      return TRUE;
    }
    return imagethumb($path . "/" . $pic , $path . "/thumb/" . $pic , 40, FALSE, TRUE);
  }

?>

<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title></title>
  <meta name="description" content="">

  <meta name="viewport" content="width=device-width">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/main.css">
  <script src="js/libs/modernizr-2.5.2.min.js"></script>
  <link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" href="css/chocolat.css" type="text/css" media="screen" charset="utf-8">
  <link href="css/jquery.thumbnailScroller.css" rel="stylesheet" />

</head>
<body>
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->


  <div role="main">
    <div id="sidebar">
      <?php if (isset($conf['title'])) : ?>
      <h1><a href="."><?php echo $conf['title'] ?></a></h1>
      <?php endif; ?>
      <ul id="menu">
        <?php foreach ($cats as $key => $cat) : ?>
          <li class="expand" ><?php echo $cat['name'] ?>
            <ul style="display: none" id="l_<?php echo $cat['id'] ?>">
          <?php foreach ($cat['sub'] as $subkey => $subcat) : ?>
            <li><a href="<?php echo $subcat['path'] ?>" id="<?php echo $subcat['id'] ?>" class="gallerie"><?php echo $subcat['name'] ?></a></li>
          <?php endforeach; ?>
          </ul>
          </li>

        <?php endforeach; ?>
        <?php $index = 0; ?>
        <?php if (isset($conf['links'])) : ?>
          <?php foreach ($conf['links'] as $name => $link) : ?>
            <li <?php if ($index==0) echo "class='link' "?>><a href="<?php echo $link ?>" target="_blank"><?php echo $name ?></a></li>
            <?php $index++; ?>
          <?php endforeach; ?>
        <?php endif; ?>


    <li class="link" id="friends"><?php echo (isset($conf['friends_link_name']) ? $conf['friends_link_name'] : "Friends") ?></li>

      </ul>
    </div>
    <div id="viewer">
      <h2 id="serieTitle"></h2>

      <div id="photos"></div>

      <div id="scroller" class="jThumbnailScroller">
        <div class="jTscrollerContainer">
          <div id="thumbs" class="jTscroller"></div>
        </div>
      </div>
    </div>
  </div>
  <footer>

  </footer>


  <script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.1.min.js"><\/script>')</script>
  <script src="js/jquery-ui-1.8.13.custom.min.js"></script>
  <script src="js/jquery.thumbnailScroller.js"></script>
  <script src="js/jquery.chocolat.js"></script>
  <script src="js/script.js"></script>

  <script>
    $(document).ready(function() {
      var obj = jQuery.parseJSON('<?php echo json_encode($galleries) ?>');
      var friends = jQuery.parseJSON('<?php echo json_encode($friends) ?>');
      main(obj, friends);


    });
   </script>
</body>
</html>
