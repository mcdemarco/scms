<?php

/* User configuration */
// If you change $content_dir and want to access raw files, also change the folder name in .htaccess
$content_dir =  'content'; // 'content' is the folder containing your markdown files
$file_format = '.md'; // this is the extension on your markdown files (with the period).
$use_CDN = true; // change this to false to serve javascript files locally (for speed or offline use)

$site_name = 'SCMS'; // the site name appears in the menus, so shorter is better
$random_theme = false; // fetch a random color palette for styling

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
define('DEBUG', true);
$default_colors = 'f0f7ee-c4d7f2-afdedc-91a8a4-776871';//d7f9f1-7aa095-40531b-618b4a-afbc88';

// Functions

function dlog($phpSuxRox,$message = '') {
  //debug logger
   if (DEBUG)
    {
      echo '<span class="debug">'.$message.': '.$phpSuxRox.'</span><br/>';
    }
}

function getContrastYIQ($hexcolor){
	//Get the contrast color using the YIQ formula.
	$r = intval(substr($hexcolor,0,2),16);
	$g = intval(substr($hexcolor,2,2),16);
	$b = intval(substr($hexcolor,4,2),16);
	//dlog($hexcolor);
	$yiq = (($r*299)+($g*587)+($b*114))/1000;
	return ($yiq >= 128) ? '0,0,0' : '255,255,255';
}

/* Processing */
// Get the request and script urls.
$url = '';
$request_url = (isset($_SERVER['REQUEST_URI'])) ? strtok($_SERVER['REQUEST_URI'],'?') : '';
$script_url  = (isset($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : '';
$color_query = (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : '';

// Get the url path and trim the / of the left and the right
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

if ($random_theme) {
	$colorURL = "http://www.colourlovers.com/api/palettes?format=json&orderCol=numViews&sortBy=DESC";
	$curl = curl_init();
	curl_setopt($curl,CURLOPT_URL,$colorURL);
	curl_setopt($curl,CURLOPT_HEADER,false);
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
	$raw = curl_exec($curl);
	curl_close($curl);
	$json = json_decode(utf8_encode($raw),true);
	$theme = $json[array_rand($json)];
} else {
	$theme['colors'] = explode("-", $default_colors);
	$theme['title'] = 'Coolors output';
	$theme['url'] = 'http://app.coolors.co/'.$default_colors;
}

$theme['contrast'] = array_map('getContrastYIQ', $theme['colors']);
//'f9f8f8-73628a-a7c0de-a0b2a6-313d5a';
$style = <<<STYLE
body {
    color: rgb({$theme['contrast'][0]});
    background-color: #{$theme['colors'][0]};}
header, nav, nav li li, nav li a {
    color: rgb({$theme['contrast'][3]});
    background-color: #{$theme['colors'][3]};
    border-color: rgba({$theme['contrast'][3]},0.3);}
footer, footer a {
    color: rgba({$theme['contrast'][4]},0.7);
    background-color: #{$theme['colors'][4]};}
#toc, #toc a {
    color: rgba({$theme['contrast'][1]},0.7);
    background-color: #{$theme['colors'][1]};}
.markdown-body hr,
.markdown-body code,
.markdown-body table tr,
.markdown-body table tr:nth-child(2n),
.markdown-body .highlight pre,
.markdown-body pre,
.markdown-body kbd,
.markdown-body a:hover {
    background-color: #{$theme['colors'][2]};}
.markdown-body a {color: rgba({$theme['contrast'][0]},0.9);}
.markdown-body a:hover {color: rgba({$theme['contrast'][2]},0.7);}

@media (min-width: 48em) {
    nav li li {
	border:none;
    }


STYLE;

?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo ($url != '' ? $url : $site_name); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <link rel="stylesheet" href="/css/github-markdown.css"/> <!--asciidoctor-default.css/-->
  <link rel="stylesheet" href="/css/scms.css"/>
  <style>
   <?php echo $style; ?>
  </style>
</head>
<body>

<header>
  <nav>
    <ul class="nav">
      <li class="logo"><a href="/"><?php echo $site_name; ?></a></li>
      <li class="btn"><a href="#" class="btn-link">&#9776;</a>
        <ul class="menu">
          <?php echo $menu; ?>
        </ul>
      </li>
    </ul>
  </nav>
</header>

  <main id="content">
    <div id="toc">
      <div id="toctitle">Contents</div>
      <ol id="scms-toc"></ol>
    </div>
    <xmp style="display:none;"><?php echo $content; ?></xmp>
  </main>
    
  <?php if (isset($timestamp) || isset($theme)) { ?>
  <footer>
    <div>
       <?php
       //echo var_dump($theme['colors']).'<br/>';						    
       //echo var_dump($theme['contrast']);						    
	  if (isset($theme)) echo '<span style="float:left;" title="' . implode('-',$theme['colors']) . '">Theme based on <a href="' . $theme['url']. '"> ' . $theme['title']. '</a></span>';
          if (isset($timestamp)) echo $timestamp;
	?>
    </div>
  </footer>
<?php } ?>

  <script src="<?php echo $marked_location; ?>"></script>
  <script src="/js/scms.js"></script>
</body>
</html>
