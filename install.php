<?php

/**
 * Morfy :: Installator
 */

// Get array with the names of all modules compiled and loaded
$php_modules = get_loaded_extensions();

// Get server port    
if ($_SERVER["SERVER_PORT"] == "80") $port = ""; else $port = ':'.$_SERVER["SERVER_PORT"];

// Get site URL
$site_url = 'http://'.$_SERVER["SERVER_NAME"].$port.str_replace(array("index.php", "install.php"), "", $_SERVER['PHP_SELF']);

// Replace last slash in site_url
$site_url = rtrim($site_url, '/');

// Rewrite base
$rewrite_base = str_replace(array("index.php", "install.php"), "", $_SERVER['PHP_SELF']);

// Errors array
$errors = array();

// Directories to check
$dir_array = array('content', 'themes');

// If pressed <Install> button then try to install
if (isset($_POST['install_submit'])) {

    // If errors is 0 then install cms
    if (count($errors) == 0) {

    	file_put_contents('config.php', "
<?php

    return array(
        'site_url' => '{$site_url}',
        'site_charset' => 'UTF-8',
        'site_timezone' => 'UTC',
        'site_theme' => 'default',
        'site_title' => 'Site title',
        'site_description' => 'Site description',
        'site_keywords' => 'site, keywords',
        'email' => 'admin@admin.com',
        'plugins' => array(
            'markdown',
        ),
    );
  		");

      	// Write .htaccess
        $htaccess = file_get_contents('.htaccess');
        $save_htaccess_content = str_replace("/%siteurlhere%/", $rewrite_base, $htaccess);

        $handle = fopen ('.htaccess', "w");
        fwrite($handle, $save_htaccess_content);
        fclose($handle);

        // Installation done :)
        header("location: index.php?install=done");
    }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Morfy Installer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="<?php echo $site_url; ?>/themes/default/assets/css/bootstrap.min.css" rel="stylesheet">
	<style>
		.container {
			max-width: 600px;
		}
	</style>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="container">
		<h1 class="text-center">Morfy</h1>
		<form role="form">
		  <div class="form-group">
		    <label for="sitename">Site Name</label>
		    <input type="text" class="form-control" id="sitename" placeholder="Enter Site Name">
		  </div>
		  <div class="form-group">
		    <label for="sitename">Site Description</label>
		    <input type="text" class="form-control" id="sitename" placeholder="Enter Site Description">
		  </div>
		  <div class="form-group">
		    <label for="sitename">Site Keywords</label>
		    <input type="text" class="form-control" id="sitename" placeholder="Enter Site Keywords">
		  </div>
		  <div class="form-group">
		    <label for="sitename">Site Url</label>
		    <input type="text" class="form-control" id="sitename" placeholder="Enter Site Url" value="<?php echo $site_url; ?>">
		  </div>
		  <div class="form-group">
		    <label for="sitename">Email</label>
		    <input type="text" class="form-control" id="sitename" placeholder="Enter Email">
		  </div>
		  <input type="submit" class="btn btn-primary form-control" value="Install">
		</form>
    </div>
  </body>
</html>