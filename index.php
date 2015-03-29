<?php

/* User configuration */
// If you change $content_dir and want to access raw files, also change the folder name in .htaccess
$content_dir =  'content'; // 'content' is the folder containing your markdown files
$file_format = '.md'; // this is the extension on your markdown files (with the period).
$use_CDN = true; // change this to false to serve javascript files locally (for speed or offline use)

$site_name = 'SCMS'; // the site name appears in the menus, so shorter is better
$bootswatch_theme = 'spacelab'; // choose any bootswatch theme; download it if not using CDN.
$invert_nav = true; // invert the bootstrap navbar

$index_filename = 'index'; // the default file to open in each directory
$use_random = false; // open a random file (probably the first one) if the default file isn't found 
$menu_style = 'flat'; // Options are 'breadcrumbs', 'flat', 'filename', and 'none'.

/* Internal configuration */
$local_js_dir = '/js/lib/';
$marked_location = ($use_CDN ? '//cdnjs.cloudflare.com/ajax/libs/marked/0.3.2/marked.js' :  $local_js_dir . 'marked.js');
$bootstrap_location = $local_js_dir . 'bootstrap-without-jquery.js'; // v.0.6.1, Bootstrap 3.
$bootswatch_location = ($use_CDN ? 'https://maxcdn.bootstrapcdn.com/bootswatch/3.3.2/'  :  $local_js_dir . 'bootswatch/') . $bootswatch_theme . '/bootstrap.min.css';
//Ignore date errors.
error_reporting(E_ALL ^ E_WARNING);


/* Innards */

define('ROOT_DIR', realpath(dirname(__FILE__)) .'/');
$content_dir = $content_dir . '/';
if ($menu_style != 'breadcrumbs' && $menu_style != 'flat' && $menu_style != 'filename') $menu_style = 'none';

// Get request url and script url
$url = '';
$request_url = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
$script_url  = (isset($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : '';
	
// Get our url path and trim the / of the left and the right
if ($request_url != $script_url)
  $url = trim(preg_replace('/'. str_replace('/', '\/', str_replace('index.php', '', $script_url)) .'/', '', $request_url, 1), '/');

// Get the file path
if ($url) {
  $file = $content_dir . $url; 
  $file_name = (strrchr($url, '/') ? strrchr($url, '/') : $url);
 } else {
  $file = $content_dir . $index_filename;
  $file_name = $index_filename;
 }

// Load the file
if (is_dir($file)) {
  $path = $url;
  $file = $content_dir . $url .'/' . $index_filename . $file_format;
  if (file_exists($file)) {
    $file_name = $index_filename;
    $content = file_get_contents($file);
  } else {
    $file_name = '';
    //Pick a file from the directory based on $use_random.
    if ($use_random) {
      $dir = new DirectoryIterator($content_dir . $path);
      foreach ($dir as $fileinfo) {
	if ($fileinfo->isFile() && '.' . $fileinfo->getExtension() == $file_format) {
	  $file_name = explode($file_format, $fileinfo)[0];
	  $file = $content_dir . $url . '/' . $fileinfo;
	  $content = file_get_contents($file);
	  $timestamp = "Last modified: " . date("F d Y H:i:s e.", filemtime($file));
	  break;
	}
      }
    }
    if ($file_name == '') {
      $content = <<< EOF
# Directory Listing

No index page was found for the directory you requested.

EOF;
	if ($menu_style == 'flat' || $menu_style == 'breadcrumbs')
	  $content .= 'Use the menu links to navigate to another page.';
      }
  }
} else {
  $path = substr($url, 0, strrpos($url, '/'));
  $file .=  $file_format;
  if (file_exists($file)) {
    $content = file_get_contents($file);
    $timestamp = "Last modified: " . date("F d Y H:i:s e.", filemtime($file));
  } else {
    $path = '';
    $file_name = '404';
    $content = <<< EOF
# 404

The file you requested was not found.

EOF;
    if ($menu_style == 'flat' || $menu_style == 'breadcrumbs')
      $content .= 'Use the menu links to navigate to another page.';
  }
}

if ($menu_style == 'none') {
  $menu = '';
 } elseif ($menu_style == 'filename') {
  $menu = "<li><a href='#'>$path$file_name</a></li>";
 } else {

  // Generate the menus.
  if (!isset($dir)) $dir = new DirectoryIterator($content_dir . $path);
  $submenu = '';

  foreach ($dir as $fileinfo) {
    if (!$fileinfo->isDot()) {// && ($fileinfo->getExtension() == '' || '.' . $fileinfo->getExtension() == $file_format)) {
      $displayName = explode($file_format, $fileinfo)[0];
      $submenu .= '<li><a href="/' . ($path != '' ? $path . '/' : '') . $displayName . '">';
      $submenu .= $displayName . ($fileinfo->isDir() ? '/' : '') . '</a></li>';
    }
  }

  if ($menu_style == 'flat') {
    $menu = $submenu . ($path == '' ? '' : "<li><a href='/$path/../'>up</a></li>");
  } else {
    $menu = '';

    $pathsplit = explode('/',$path);
    $currentpath = '/';

    for ($i = 0; $i < count($pathsplit); $i++) {
      $currentpath .= $pathsplit[$i];
      //Home link and > marker handling.
      if ($i == 0){
	if ($pathsplit[$i] == '') {
	  //Full home menu.
	  $menu .= '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">home <span class="caret"></span></a>';
	  $menu .= '<ul class="dropdown-menu" role="menu">' . $submenu . '</ul></li>';
	} else {
	  //Dummy home link
	  $menu .= '<li><a href="/">home</a></li><li><a class="scms-marker">&gt;</a></li>';
	}
      } else {
	$menu .= '<li><a class="scms-marker">&gt;</a></li>';
      }
      //Non-home menus.
      if ($pathsplit[$i] != '') {
	if ($i == count($pathsplit) - 1) {
	  //We can only index the tip.
	  $menu .= "<li class='dropdown'><a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-expanded='false'>$pathsplit[$i] <span class='caret'></span></a>";
	  $menu .= "<ul class='dropdown-menu' role='menu'>" . $submenu . '</ul></li>';
	} else {
	  $menu .= "<li><a href='$currentpath/'>{$pathsplit[$i]}</a></li>";
	}
      }
    }
    
    if ($file_name != '') {
      $file_name = str_replace('/','',$file_name);
      $menu .= "<li><a class='scms-marker'>&gt;</a></li><li><a href='#'>$file_name</a></li>";
    }
  }
 }


?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo ($url != '' ? $url : $site_name); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <link rel="stylesheet" href="/css/asciidoctor-default.css"/>
  <link rel="stylesheet" href="/css/scms.css"/>
</head>
<body>

<header id="header">
  <div class="navigation">
    <ul class="nav">
      <li class="logo"><a href="/"><?php echo $site_name; ?></a></li>
      <li class="btn"><a href="#" class="btn-link">&#9776;</a>
        <ul class="menu">
          <?php echo $menu; ?>
        </ul>
      </li>
    </ul>
  </div>
</header>

  <main class="container" id="content">
    <div id="toc" class="toc">
      <div id="toctitle">Table of Contents</div>
      <ul id="scms-toc" class="sectlevel1"></ul>
    </div>
    <xmp style="display:none;"><?php echo $content; ?></xmp>
  </main>

<?php if (isset($timestamp)) { ?>
  <footer id="footer">
    <div id="footer-text" class="text-right">
       <?php echo $timestamp; ?>
    </div>
  </footer>
<?php } ?>

  <script src="<?php echo $marked_location; ?>"></script>
  <script src="/js/scms.js"></script>
</body>
</html>