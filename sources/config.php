
<?php

/* Wifi Hotspot app for YunoHost 
 * Copyright (C) 2015 Julien Vaubourg <julien@vaubourg.com>
 * Contribute at https://github.com/jvaubourg/hotspot_ynh
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */



// Limonade configuration
function configure() {
    option('env', ENV_PRODUCTION);
    option('debug', false);
    option('base_uri', '<TPL:NGINX_LOCATION>/');
    layout("layout.html.php");
    define('PUBLIC_DIR', '<TPL:NGINX_LOCATION>/public');
}

// Not found page
function not_found($errno, $errstr, $errfile=null, $errline=null) {
    $msg = h(rawurldecode($errstr));
    return render($msg, 'error_layout.html.php');
}

function T_($string) {
    return gettext($string);
}

// Before routing
function before($route) {
    $lang_mapping = array(
       'fr' => 'fr_FR'
    );

   /**
     * * Locale
     * */
    if (!isset($_SESSION['locale'])) {
        $locale = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $_SESSION['locale'] = strtolower(substr(chop($locale[0]),0,2));
    }
    $lang = $_SESSION['locale'];
    // Convert simple language code into full language code
    if (array_key_exists($lang, $lang_mapping)) {
        $lang = $lang_mapping[$lang];
    }
    $lang = $lang.'.utf8';
    $textdomain="localization";

    putenv('LANGUAGE='.$lang);
    putenv('LANG='.$lang);
    putenv('LC_ALL='.$lang);
    putenv('LC_MESSAGES='.$lang);
    setlocale(LC_ALL,$lang);
    setlocale(LC_CTYPE,$lang);
    $locales_dir = dirname(__FILE__).'/i18n';
    bindtextdomain($textdomain,$locales_dir);
    bind_textdomain_codeset($textdomain, 'UTF-8');
    textdomain($textdomain);
    // Set the $locale variable in template
    set('locale', $lang);
}

// After routing
function after($output, $route) {
    /*
    $time = number_format( (float)substr(microtime(), 0, 10) - LIM_START_MICROTIME, 6);
    $output .= "\n<!-- page rendered in $time sec., on ".date(DATE_RFC822)." -->\n";
    $output .= "<!-- for route\n";
    $output .= print_r($route, true);
    $output .= "-->";
    */
    return $output;
}
