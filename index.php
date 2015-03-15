<?php

/* Basic configuration */
// If you change $content and want to access raw files, also change the folder name in .htaccess
$content =  'content'; // 'content' is the folder containing your markdown files
$site_name = 'scms'; // the site name appears in the menus, so shorter is better
$bootswatch_theme = 'spacelab'; // choose any bootswatch theme
$file_format = '.md'; // this is the extension on your markdown files (with the period).
$index = 'index'; // the default file to open in each directory
$use_random = false; // open a random file if a default file isn't found (TODO)
$use_CDN = true; // change this to false to serve javascript files locally (for speed or offline use)

/* Advanced configuration */
$local_js_dir = '/js/lib/';
$marked_location = ($use_CDN ? '//cdnjs.cloudflare.com/ajax/libs/marked/0.3.2/marked.js' :  $local_js_dir . 'marked.js');
$bootstrap_location = $local_js_dir . 'bootstrap-without-jquery.js'; // v.0.6.1, Bootstrap 3.
$bootswatch_location = 'https://maxcdn.bootstrapcdn.com/bootswatch/3.3.2/' . $bootswatch_theme . '/bootstrap.min.css';

/* Innards */

define('ROOT_DIR', realpath(dirname(__FILE__)) .'/');
$content_dir = $content . '/';

// Get request url and script url
$url = '';
$request_url = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
$script_url  = (isset($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : '';
	
// Get our url path and trim the / of the left and the right
if ($request_url != $script_url)
  $url = trim(preg_replace('/'. str_replace('/', '\/', str_replace('index.php', '', $script_url)) .'/', '', $request_url, 1), '/');

// Get the file path
if ($url) $file = $content_dir . $url;
else $file = $content_dir . $index;

// Load the file
if (is_dir($file)) {
  $path = $url;
  $file = $content_dir . $url .'/' . $index . $file_format;
  if (file_exists($file)) {
    $content = file_get_contents($file);
  } else {
    //To do: pick a file from the directory based on $use_random.
    $content = <<< EOF
# Directory Listing

No index page was found for the directory you requested.

Use the menu links to navigate to another page.
EOF;
  }
} else {
  $path = substr($url, 0, strrpos($url, '/'));
  $file .=  $file_format;
  if (file_exists($file)) {
    $content = file_get_contents($file);
  } else {
    $content = <<< EOF
# 404

The file you requested was not found.

Use the menu links to navigate to another page.
EOF;
  }
}

// Generate the menus.
$dir = new DirectoryIterator($content_dir . $path);
$menu = ($path != '' ? '<li><a href="../">up</a></li>' : '');

foreach ($dir as $fileinfo) {
  if (!$fileinfo->isDot()) {// && ($fileinfo->getExtension() == '' || '.' . $fileinfo->getExtension() == $file_format)) {
    $displayName = explode($file_format, $fileinfo)[0];
    $menu .= '<li><a href="/' . ($path != '' ? $path . '/' : '') . $displayName . '">';
    $menu .= $displayName . ($fileinfo->isDir() ? '/' : '') . '</a></li>';
  }
}


?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo ($url != '' ? $url : $site_name); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<link rel="stylesheet" href="<?php echo $bootswatch_location; ?>"/>
<link rel="stylesheet" href="/js/scms.css"/>
</head>
<body>
    <div class="navbar navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/"><?php echo $site_name; ?></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <?php echo $menu; ?>
          </ul>
        </div>
      </div>
    </div>

  <div class="container" id="content">
    <div class="list-group pull-right" id="scms-toc" title="Table of Contents"></div>
	  
    <xmp style="display:none;"><?php echo $content; ?></xmp>

  </div>
  <script src="<?php echo $bootstrap_location; ?>"></script>
  <script src="<?php echo $marked_location; ?>"></script>
  <script src="/js/scms.js"></script>
</body>
</html>