<?php

/**
 *  GERENCIAMENTO DE ERROS
 */

if (strpos($_SERVER["HTTP_HOST"], "localhost")) {
    /**
     * DATABASE LOCAL
     */
    define("CONF_DB_HOST", "localhost");
    define("CONF_DB_USER", "root");
    define("CONF_DB_PASS", "");
    define("CONF_DB_NAME", "igreja");

    /**
     *  GERENCIAMENTO DE ERROS
     */
    ini_set('display_errors', 1);
    ini_set('display_startup_erros', 1);
    error_reporting(E_ALL);
    date_default_timezone_set("America/Sao_Paulo");

    /**
     * DEFINES
     */
    define("ENVIROMENT", "local");

} else {
    /**
     * DATABASE PRODUCTION
     */
    define("CONF_DB_HOST", "54.39.85.210");
    define("CONF_DB_USER", "treinese_treinese");
    define("CONF_DB_PASS", "z7g(pT0mJUG9-4");
    define("CONF_DB_NAME", "melhoreseuouvido");

    /**
     * DEFINES
     */
    define("ENVIROMENT", "production");
}

/**
 * PROJECT URLs
 */
define("CONF_URL_BASE", "https://www.treineseuouvido.online");
define("CONF_URL_TEST", "https://www.localhost/melhore-seu-ouvido");

/**
 * PROJECT CONFIGS
 */
define("INATIVE_TIME", 7);

/**
 * SITE
 */
define("CONF_SITE_NAME", "Melhore sua percepção musical");
define("CONF_SITE_TITLE", "Desenvolva seu ouvido musical! Tire musicas de ouvido.");
define("CONF_SITE_DESC", "Passe para um próximo nível como músico, desenvolva seu ouvido. Esqueça as cifras, você não precisará mais!");
define("CONF_SITE_LANG", "pt_BR");
define("CONF_SITE_DOMAIN", "melhoreseuouvido.online");

/**
 * SITE
 */
define("CONF_SOCIAL_TWITTER_CREATOR", "@gabrielsilva50");
define("CONF_SOCIAL_TWITTER_PUBLISHER", "@AgenciaGalg");
define("CONF_SOCIAL_FACEBOOK_APP", "681962308892416");
define("CONF_SOCIAL_FACEBOOK_PAGE", "gravataionline");
define("CONF_SOCIAL_INSTAGRAM_PAGE", "gravataionline");
define("CONF_SOCIAL_TWITTER_PAGE", "gravataionline");
define("CONF_SOCIAL_FACEBOOK_AUTHOR", "gabrielcaldeiradasilv");

/**
 * DATES
 */
define("CONF_DATE_BR", "d/m/Y H:i:s");
define("CONF_DATE_APP", "Y-m-d H:i:s");

/**
 * PASSWORD
 */
define("CONF_PASSWD_MIN_LEN", 8);
define("CONF_PASSWD_MAX_LEN", 40);
define("CONF_PASSWD_ALGO", PASSWORD_DEFAULT);
define("CONF_PASSWD_OPTION", ["cost" => 10]);

/**
 * MESSAGE
 */
define("CONF_MESSAGE_CLASS", "message");
define("CONF_MESSAGE_INFO", "info msg-info");
define("CONF_MESSAGE_SUCCESS", "success msg-check-circle-o");
define("CONF_MESSAGE_WARNING", "warning msg-warning");
define("CONF_MESSAGE_ERROR", "error msg-error");

/**
 * VIEW
 */
define("CONF_VIEW_PATH", __DIR__ . "/../../shared/views");
define("CONF_VIEW_EMAIL_PATH", __DIR__ . "/../../shared/views/email");
define("CONF_VIEW_EXT", "php");
define("CONF_VIEW_THEME", "web");
define("CONF_VIEW_APP", "app");
define("CONF_VIEW_APP_COMPANY", "company");
define("CONF_VIEW_APP_USER", "user");
define("CONF_VIEW_ADMIN", "admin");

/**
 * UPLOAD
 */
define("CONF_UPLOAD_DIR", "storage");
define("CONF_UPLOAD_IMAGE_DIR", "images");
define("CONF_UPLOAD_FILE_DIR", "files");
define("CONF_UPLOAD_MEDIA_DIR", "medias");

/**
 * IMAGES
 */
define("CONF_IMAGE_CACHE", CONF_UPLOAD_DIR . "/" . CONF_UPLOAD_IMAGE_DIR . "/cache");
define("CONF_IMAGE_SIZE", 2000);
define("CONF_IMAGE_QUALITY", ["jpg" => 75, "png" => 5]);

/**
 * MAIL
 */
define("CONF_MAIL_HOST", "smtp.sendgrid.net");
define("CONF_MAIL_PORT", "587");
define("CONF_MAIL_USER", "apikey");
define("CONF_MAIL_PASS", "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");
define("CONF_MAIL_SENDER", ["address" => "contato@manoojob.com", "name" => "Fabio"]);
define("CONF_MAIL_SUPPORT", "suporte@manoojob.com");
define("CONF_MAIL_OPTION_LANG", "br");
define("CONF_MAIL_OPTION_HTML", true);
define("CONF_MAIL_OPTION_AUTH", true);
define("CONF_MAIL_OPTION_SECURE", "tls");
define("CONF_MAIL_OPTION_CHARSET", "utf-8");
