<?php

/**
 * Morfy :: Installator
 */

include ROOT_DIR . '/libraries/Force/Url/Url.php';

// Sanitize URL to prevent XSS - Cross-site scripting
Url::runSanitizeURL();

// Send default header and set internal encoding
header('Content-Type: text/html; charset=UTF-8');
function_exists('mb_language') and mb_language('uni');
function_exists('mb_regex_encoding') and mb_regex_encoding('UTF-8');
function_exists('mb_internal_encoding') and mb_internal_encoding('UTF-8');

// Gets the current configuration setting of magic_quotes_gpc and kill magic quotes
if (get_magic_quotes_gpc()) {
    function stripslashesGPC(&$value)
    {
        $value = stripslashes($value);
    }
    array_walk_recursive($_GET, 'stripslashesGPC');
    array_walk_recursive($_POST, 'stripslashesGPC');
    array_walk_recursive($_COOKIE, 'stripslashesGPC');
    array_walk_recursive($_REQUEST, 'stripslashesGPC');
}

// Get array with the names of all modules compiled and loaded
$php_modules = get_loaded_extensions();

// Get server port
if ($_SERVER["SERVER_PORT"] == "80") {
    $port = "";
} else {
    $port = ':'.$_SERVER["SERVER_PORT"];
}

// Get site URL
$site_url = 'http://'.$_SERVER["SERVER_NAME"].$port.str_replace(array("index.php", "install.php"), "", $_SERVER['PHP_SELF']);

// Replace last slash in site_url
$site_url = rtrim($site_url, '/');

// Rewrite base
$rewrite_base = str_replace(array("index.php", "install.php"), "", $_SERVER['PHP_SELF']);

// Errors array
$errors = array();

// Directories to check
$dir_array = array('content', 'themes', 'cache');

if (version_compare(PHP_VERSION, "5.3.0", "<")) {
    $errors['php'] = 'error';
}

if (function_exists('apache_get_modules')) {
    if (! in_array('mod_rewrite', apache_get_modules())) {
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
    $post_site_url = isset($_POST['site_url']) ? trim($_POST['site_url']) : '';
    $post_site_timezone = isset($_POST['site_timezone']) ? trim($_POST['site_timezone']) : '';
    $post_site_title = isset($_POST['site_title']) ? trim($_POST['site_title']) : '';
    $post_site_description = isset($_POST['site_description']) ? trim($_POST['site_description']) : '';
    $post_site_keywords = isset($_POST['site_keywords']) ? trim($_POST['site_keywords']) : '';
    $post_email = isset($_POST['email']) ? trim($_POST['email']) : '';

    file_put_contents(ROOT_DIR . '/config/site.yml',
trim("title: '{$post_site_title}'
description: '{$post_site_description}'
keywords: '{$post_site_keywords}'
url: '{$post_site_url}'
autor:
  email: '{$post_email}'
charset: 'UTF-8'
timezone: '{$post_site_timezone}'
theme: 'default'

# Site Plugins
plugins:"));

    // Write .htaccess
    $htaccess = file_get_contents('.htaccess');
    $save_htaccess_content = str_replace("/%siteurlhere%/", $rewrite_base, $htaccess);

    $handle = fopen('.htaccess', "w");
    fwrite($handle, $save_htaccess_content);
    fclose($handle);

        // Installation done :)
        header("location: index.php?install=done");
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Morfy Installer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="<?php echo $site_url; ?>/themes/default/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700,900,400italic' type='text/css' rel='stylesheet' />
    <script src="<?php echo $site_url; ?>/themes/default/assets/js/jquery.min.js"></script>
    <script src="<?php echo $site_url; ?>/themes/default/assets/js/bootstrap.min.js"></script>
    <style>
        body {
            font-family: "Source Sans Pro","Helvetica","Arial",sans-serif;
            font-size: 16px;
            line-height: 26px;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin-bottom: 40px;
        }

        .ok {
            color: #3c763d;
            background-color: #dff0d8;
        }

        .error {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }

        .step-1 ul li {
            margin-bottom: 10px;
            padding: 5px 10px;
            border-radius: 4px;
        }

        .btn-primary {
            border-radius: 4px;
        }

        .form-control {
            border-color: #CECECE;
            border-radius: 4px;
        }

        .morfy-logo {
            width: 280px;
            height: 145px;
            margin-top: 10px;
            margin-bottom: 10px;
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
  </head>
  <body>
    <div class="container">

        <div class="text-center">
            <img class="morfy-logo" src="<?php echo $site_url; ?>/themes/default/assets/img/morfy-logo.png" alt="Morfy CMS" />
        </div>

        <div class="step-1">
            <ul class="list-unstyled">
            <?php

                if (version_compare(PHP_VERSION, "5.3.0", "<")) {
                    echo '<li class="alert alert-danger">PHP 5.3 or greater is required</li>';
                } else {
                    echo '<li class="alert alert-success">PHP Version '.PHP_VERSION.'</li>';
                }

                if (function_exists('apache_get_modules')) {
                    if (! in_array('mod_rewrite', apache_get_modules())) {
                        echo '<li class="alert alert-danger">Apache Mod Rewrite is required</li>';
                    } else {
                        echo '<li class="alert alert-success">Module Mod Rewrite is installed</li>';
                    }
                } else {
                    echo '<li class="alert alert-success">Module Mod Rewrite is installed</li>';
                }

                foreach ($dir_array as $dir) {
                    if (is_writable($dir.'/')) {
                        echo '<li class="alert alert-success">Directory: <b> '.$dir.' </b> writable</li>';
                    } else {
                        echo '<li class="alert alert-danger">Directory: <b> '.$dir.' </b> not writable</li>';
                    }
                }

                if (is_writable(__FILE__)) {
                    echo '<li class="alert alert-success">Install script writable</li>';
                } else {
                    echo '<li class="alert alert-danger">Install script not writable</li>';
                }

                if (is_writable('.htaccess')) {
                    echo '<li class="alert alert-success">Main .htaccess file writable.</li>';
                } else {
                    echo '<li class="alert alert-danger">Main .htaccess file not writable.</li>';
                }
            ?>
            </ul>
            <?php
                if (count($errors) == 0) {
                    ?>
                <a class="btn btn-primary btn-lg btn-block continue">Continue</a>
            <?php

                } else {
                    ?>
            <?php

                } ?>
        </div>

        <div class="step-2 hide">
        <form role="form" method="post">
          <div class="form-group">
            <label for="site_title">Site Name</label>
            <input type="text" name="site_title" class="form-control" id="site_title" placeholder="Enter Site Name" required>
          </div>
          <div class="form-group">
            <label for="site_description">Site Description</label>
            <input type="text" name="site_description" class="form-control" id="site_description" placeholder="Enter Site Description">
          </div>
          <div class="form-group">
            <label for="site_keywords">Site Keywords</label>
            <input type="text" name="site_keywords" class="form-control" id="site_keywords" placeholder="Enter Site Keywords">
          </div>
          <div class="form-group">
            <label for="site_url">Site Url</label>
            <input type="text" name="site_url" class="form-control" id="site_url" placeholder="Enter Site Url" value="<?php echo $site_url; ?>">
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="text" name="email" class="form-control" id="email" placeholder="Enter Email">
          </div>
          <div class="form-group">
            <label>Time zone</label>
            <select class="form-control" name="site_timezone">
                <option value="Kwajalein">(GMT-12:00) International Date Line West</option>
                <option value="Pacific/Samoa">(GMT-11:00) Midway Island, Samoa</option>
                <option value="Pacific/Honolulu">(GMT-10:00) Hawaii</option>
                <option value="America/Anchorage">(GMT-09:00) Alaska</option>
                <option value="America/Los_Angeles">(GMT-08:00) Pacific Time (US &amp; Canada)</option>
                <option value="America/Tijuana">(GMT-08:00) Tijuana, Baja California</option>
                <option value="America/Denver">(GMT-07:00) Mountain Time (US &amp; Canada)</option>
                <option value="America/Chihuahua">(GMT-07:00) Chihuahua, La Paz, Mazatlan</option>
                <option value="America/Phoenix">(GMT-07:00) Arizona</option>
                <option value="America/Regina">(GMT-06:00) Saskatchewan</option>
                <option value="America/Tegucigalpa">(GMT-06:00) Central America</option>
                <option value="America/Chicago">(GMT-06:00) Central Time (US &amp; Canada)</option>
                <option value="America/Mexico_City">(GMT-06:00) Guadalajara, Mexico City, Monterrey</option>
                <option value="America/New_York">(GMT-05:00) Eastern Time (US &amp; Canada)</option>
                <option value="America/Bogota">(GMT-05:00) Bogota, Lima, Quito, Rio Branco</option>
                <option value="America/Indiana/Indianapolis">(GMT-05:00) Indiana (East)</option>
                <option value="America/Caracas">(GMT-04:30) Caracas</option>
                <option value="America/Halifax">(GMT-04:00) Atlantic Time (Canada)</option>
                <option value="America/Manaus">(GMT-04:00) Manaus</option>
                <option value="America/Santiago">(GMT-04:00) Santiago</option>
                <option value="America/La_Paz">(GMT-04:00) La Paz</option>
                <option value="America/St_Johns">(GMT-03:30) Newfoundland</option>
                <option value="America/Argentina/Buenos_Aires">(GMT-03:00) Buenos Aires</option>
                <option value="America/Sao_Paulo">(GMT-03:00) Brasilia</option>
                <option value="America/Godthab">(GMT-03:00) Greenland</option>
                <option value="America/Montevideo">(GMT-03:00) Montevideo</option>
                <option value="America/Argentina/Buenos_Aires">(GMT-03:00) Georgetown</option>
                <option value="Atlantic/South_Georgia">(GMT-02:00) Mid-Atlantic</option>
                <option value="Atlantic/Azores">(GMT-01:00) Azores</option>
                <option value="Atlantic/Cape_Verde">(GMT-01:00) Cape Verde Is.</option>
                <option value="Europe/London">(GMT) Greenwich Mean Time : Dublin, Edinburgh, Lisbon, London</option>
                <option value="Atlantic/Reykjavik">(GMT) Monrovia, Reykjavik</option>
                <option value="Africa/Casablanca">(GMT) Casablanca</option>
                <option value="Europe/Belgrade">(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague</option>
                <option value="Europe/Sarajevo">(GMT+01:00) Sarajevo, Skopje, Warsaw, Zagreb</option>
                <option value="Europe/Brussels">(GMT+01:00) Brussels, Copenhagen, Madrid, Paris</option>
                <option value="Africa/Algiers">(GMT+01:00) West Central Africa</option>
                <option value="Europe/Amsterdam">(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna</option>
                <option value="Africa/Cairo">(GMT+02:00) Cairo</option>
                <option value="Europe/Helsinki">(GMT+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius</option>
                <option value="Europe/Athens">(GMT+02:00) Athens, Bucharest, Istanbul</option>
                <option value="Asia/Jerusalem">(GMT+02:00) Jerusalem</option>
                <option value="Asia/Amman">(GMT+02:00) Amman</option>
                <option value="Asia/Beirut">(GMT+02:00) Beirut</option>
                <option value="Africa/Windhoek">(GMT+02:00) Windhoek</option>
                <option value="Africa/Harare">(GMT+02:00) Harare, Pretoria</option>
                <option value="Asia/Kuwait">(GMT+03:00) Kuwait, Riyadh</option>
                <option value="Asia/Baghdad">(GMT+03:00) Baghdad</option>
                <option value="Europe/Minsk">(GMT+03:00) Minsk</option>
                <option value="Africa/Nairobi">(GMT+03:00) Nairobi</option>
                <option value="Asia/Tbilisi">(GMT+03:00) Tbilisi</option>
                <option value="Asia/Tehran">(GMT+03:30) Tehran</option>
                <option value="Asia/Muscat">(GMT+04:00) Abu Dhabi, Muscat</option>
                <option value="Asia/Baku">(GMT+04:00) Baku</option>
                <option value="Europe/Moscow">(GMT+04:00) Moscow, St. Petersburg, Volgograd</option>
                <option value="Asia/Yerevan">(GMT+04:00) Yerevan</option>
                <option value="Asia/Karachi">(GMT+05:00) Islamabad, Karachi</option>
                <option value="Asia/Tashkent">(GMT+05:00) Tashkent</option>
                <option value="Asia/Kolkata">(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi</option>
                <option value="Asia/Colombo">(GMT+05:30) Sri Jayawardenepura</option>
                <option value="Asia/Katmandu">(GMT+05:45) Kathmandu</option>
                <option value="Asia/Dhaka">(GMT+06:00) Astana, Dhaka</option>
                <option value="Asia/Yekaterinburg">(GMT+06:00) Ekaterinburg</option>
                <option value="Asia/Rangoon">(GMT+06:30) Yangon (Rangoon)</option>
                <option value="Asia/Novosibirsk">(GMT+07:00) Almaty, Novosibirsk</option>
                <option value="Asia/Bangkok">(GMT+07:00) Bangkok, Hanoi, Jakarta</option>
                <option value="Asia/Beijing">(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi</option>
                <option value="Asia/Krasnoyarsk">(GMT+08:00) Krasnoyarsk</option>
                <option value="Asia/Ulaanbaatar">(GMT+08:00) Irkutsk, Ulaan Bataar</option>
                <option value="Asia/Kuala_Lumpur">(GMT+08:00) Kuala Lumpur, Singapore</option>
                <option value="Asia/Taipei">(GMT+08:00) Taipei</option>
                <option value="Australia/Perth">(GMT+08:00) Perth</option>
                <option value="Asia/Seoul">(GMT+09:00) Seoul</option>
                <option value="Asia/Tokyo">(GMT+09:00) Osaka, Sapporo, Tokyo</option>
                <option value="Australia/Darwin">(GMT+09:30) Darwin</option>
                <option value="Australia/Adelaide">(GMT+09:30) Adelaide</option>
                <option value="Australia/Sydney">(GMT+10:00) Canberra, Melbourne, Sydney</option>
                <option value="Australia/Brisbane">(GMT+10:00) Brisbane</option>
                <option value="Australia/Hobart">(GMT+10:00) Hobart</option>
                <option value="Asia/Yakutsk">(GMT+10:00) Yakutsk</option>
                <option value="Pacific/Guam">(GMT+10:00) Guam, Port Moresby</option>
                <option value="Asia/Vladivostok">(GMT+11:00) Vladivostok</option>
                <option value="Pacific/Fiji">(GMT+12:00) Fiji, Kamchatka, Marshall Is.</option>
                <option value="Asia/Magadan">(GMT+12:00) Magadan, Solomon Is., New Caledonia</option>
                <option value="Pacific/Auckland">(GMT+12:00) Auckland, Wellington</option>
                <option value="Pacific/Tongatapu">(GMT+13:00) Nukualofa</option>
            </select>
           </div>
          <input type="submit" name="install_submit" class="btn btn-primary btn-lg btn-block" value="Install">
        </form>
        </div>
    </div>
  </body>
</html>
