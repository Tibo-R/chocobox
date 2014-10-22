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
  <script src="js/lib/modernizr-2.5.2.min.js"></script>
  <link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" href="css/chocolat.css" type="text/css" media="screen" charset="utf-8">
  <link href="css/jquery.thumbnailScroller.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/main.css">

</head>
<body>
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
  <?php if (isset($conf['background_mode']) && $conf['background_mode'] == 'image') : ?>
  <div id="bg"></div>
  <?php endif; ?>
  <div role="main" id="all">
    <div id="sidebar">
      <?php if (isset($conf['title'])) : ?>
      <h1><a href="."><?= $conf['title'] ?></a></h1>
      <?php endif; ?>
      <ul id="menu">
        <?php foreach ($cats as $key => $cat) : ?>
          <?php if (isset($cat['sub'])) : ?>
            <li class="expand" ><?= $cat['name'] ?>
              <ul style="display: none" id="l_<?= $cat['id'] ?>">
              <?php foreach ($cat['sub'] as $subkey => $subcat) : ?>
                <li><a href="<?= $subcat['path'] ?>" id="<?= $subcat['id'] ?>" class="gallerie"><?= $subcat['name'] ?></a></li>
              <?php endforeach; ?>
              </ul>
            </li>
          <?php else : ?>
            <li><a href="<?= $cat['path'] ?>" id="<?= $cat['id'] ?>" class="gallerie"><?= $cat['name'] ?></a></li>
          <?php endif; ?>
        <?php endforeach; ?>

        <?php $index = 0; ?>
        <?php if (isset($conf['links'])) : ?>
          <?php foreach ($conf['links'] as $name => $link) : ?>
            <li <?php if ($index==0) echo "class='link' "?>><a href="<?= $link ?>" target="_blank"><?= $name ?></a></li>
            <?php $index++; ?>
          <?php endforeach; ?>
        <?php endif; ?>

      <?php if (isset($conf['friends_link_name'])) : ?>
        <li class="link" id="friends"><?= $conf['friends_link_name'] ?></li>
      <?php endif; ?>

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

  <footer></footer>

  <script>window.jQuery || document.write('<script src="js/lib/jquery-1.11.1.min.js"><\/script>')</script>
  <script src="js/lib/jquery-ui-1.8.13.custom.min.js"></script>
  <script src="js/lib/jquery.thumbnailScroller.js"></script>
  <script src="js/lib/jquery.chocolat.js"></script>
  <script src="js/lib/color-thief.js"></script>
  <script src="js/lib/jquery.blur.js"></script>
  <script src="js/utils.js"></script>
  <script src="js/thumbnail_manager.js"></script>
  <script src="js/background_manager.js"></script>
  <script src="js/script.js"></script>

  <script>
    $(document).ready(function() {
      var obj = jQuery.parseJSON('<?= json_encode($galleries, JSON_HEX_APOS) ?>');
      var friends = jQuery.parseJSON('<?= json_encode($friends, JSON_HEX_APOS) ?>');
      var settings = jQuery.parseJSON('<?= json_encode($conf, JSON_HEX_APOS) ?>');
      main(obj, friends, settings);


    });
   </script>
</body>
</html>
