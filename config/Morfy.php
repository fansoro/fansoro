<?php

    return array(
        'site_url' => '',
        'site_charset' => 'UTF-8',
        'site_timezone' => 'UTC',
        'site_theme' => 'default',
        'site_title' => 'Site title',
        'site_description' => 'Site description',
        'site_keywords' => 'site, keywords',
        'email' => 'admin@admin.com',
        'plugins' => array(
            'parsedown',
            'sitemap',
        ),
       	/**
       	 * https://github.com/fenom-template/fenom/blob/master/docs/ru/configuration.md
       	 */
        'fenom' =>  array( 
            // 'disable_methods' => false,
            // 'disable_native_funcs' => false,
            'auto_reload' => true,
            // 'force_compile' => false,
            // 'disable_cache' => false,
            'force_include' => true,
            // 'auto_escape' => false,
            // 'force_verify' => false,
            // 'disable_php_calls' => false,
            // 'disable_statics' => false,
            'strip' => true,
        )
    );
