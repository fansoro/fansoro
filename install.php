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

if (version_compare(PHP_VERSION, "5.3.0", "<")) {
    $errors['php'] = 'error';
}

if (function_exists('apache_get_modules')) {
    if ( ! in_array('mod_rewrite', apache_get_modules())) {
        $errors['mod_rewrite'] = 'error';
    } 
}

if (!is_writable(__FILE__)) {
    $errors['install'] = 'error';
}


if (!is_writable('.htaccess')) {
    $errors['htaccess'] = 'error';
}

// Dirs 'public', 'storage', 'backups', 'tmp'
foreach ($dir_array as $dir) {
    if (!is_writable($dir.'/')) {
        $errors[$dir] = 'error';
    }
}


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
    <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700,900,400italic' type='text/css' rel='stylesheet' />
    <link href='http://fonts.googleapis.com/css?family=Audiowide' rel='stylesheet' type='text/css'>	
    <script src="<?php echo $site_url; ?>/themes/default/assets/js/jquery.min.js"></script>
    <script src="<?php echo $site_url; ?>/themes/default/assets/js/bootstrap.min.js"></script>
	<style>
		.container {
			max-width: 600px;
		}
		body {
	        font-family: "Source Sans Pro","Helvetica","Arial",sans-serif;        
	        font-size: 16px;
	        line-height: 26px;
	        color: #333;
	        
	        background: url('bg.jpg') no-repeat center center fixed; 
	        -webkit-background-size: cover;
	        -moz-background-size: cover;
	        -o-background-size: cover;
	        background-size: cover;
	    }
		h1 {
			font-size: 126px;
			font-family: 'Audiowide', cursive;
			color: #333;
		}
		.ok {
			color: green;
		}
		.error {
			color: red;
		}
	</style>
	<script>
		$(document).ready(function() {
			$('.continue').click(function() {
				$('.step-1').addClass('hide');
				$('.step-2').removeClass('hide');
			});
		});
	</script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="container">
		<h1 class="text-center">MORFY</h1>

		<div class="step-1">
 			<ul class="list-unstyled">
            <?php

                if (version_compare(PHP_VERSION, "5.3.0", "<")) {
                    echo '<li class="error">PHP 5.3 or greater is required</li>';
                } else {
                    echo '<li class="ok">PHP Version '.PHP_VERSION.'</li>';
                }

                if (function_exists('apache_get_modules')) {
                    if ( ! in_array('mod_rewrite',apache_get_modules())) {
                        echo '<li class="error">Apache Mod Rewrite is required</li>';
                    } else {
                        echo '<li class="ok">Module Mod Rewrite is installed</li>';
                    }
                } else {
                    echo '<li class="ok">Module Mod Rewrite is installed</li>';
                }

                foreach ($dir_array as $dir) {
                    if (is_writable($dir.'/')) {
                        echo '<li class="ok">Directory: <b> '.$dir.' </b> writable</li>';
                    } else {
                        echo '<li class="error">Directory: <b> '.$dir.' </b> not writable</li>';
                    }
                }

                if (is_writable(__FILE__)) {
                    echo '<li class="ok">Install script writable</li>';
                } else {
                    echo '<li class="error">Install script not writable</li>';
                }

                if (is_writable('.htaccess')) {
                    echo '<li class="ok">Main .htaccess file writable.</li>';
                } else {
                    echo '<li class="error">Main .htaccess file not writable.</li>';
                }
            ?>
            </ul>
            <?php
				if (count($errors) == 0) {
			?>
				<a class="btn btn-primary continue form-control">Continue</a>
			<?php
				} else {
            ?>
            <a class="btn btn-disabled form-control" disabled>Continue</a>
            <?php } ?>
		</div>

		<div class="step-2 hide">
		<form role="form" method="post">
		  <div class="form-group">
		    <label for="sitename">Site Name</label>
		    <input type="text" class="form-control" id="sitename" placeholder="Enter Site Name" required>
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
		    <input type="text" class="form-control" id="sitename" placeholder="Enter Site Url" value="<?php echo $site_url; ?>" required>
		  </div>
		  <div class="form-group">
		    <label for="sitename">Email</label>
		    <input type="text" class="form-control" id="sitename" placeholder="Enter Email" required>
		  </div>
		  <input type="submit" name="install_submit" class="btn btn-primary form-control" value="Install">
		</form>
		</div>
    </div>
  </body>
</html>