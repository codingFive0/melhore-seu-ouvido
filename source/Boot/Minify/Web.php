<?php
if (strpos(url(), "localhost")) {

    /**
     * CSS
     */
    $minCSS = new MatthiasMullie\Minify\CSS();
    $minCSS->add(dirname(__DIR__, 3) . "/shared/styles/css/all.min.css");
    $minCSS->add(dirname(__DIR__, 3) . "/shared/styles/boot.css");

    $cssDir = scandir(__DIR__ . "/../../../themes/" . CONF_VIEW_THEME . "/assets/css");
    if (!empty($cssDir)) {
        foreach ($cssDir as $css) {
            $cssFile = __DIR__ . "/../../../themes/" . CONF_VIEW_THEME . "/assets/css/{$css}";
            if (is_file($cssFile) && pathinfo($cssFile)['extension'] == "css") {
                $minCSS->add($cssFile);
            }
        }
    }

    // Minify CSS
    $minCSS->minify(__DIR__ . "/../../../themes/" . CONF_VIEW_THEME . "/assets/styles.css");

    /**
     * JS
     */
    $minJS = new MatthiasMullie\Minify\JS();
    $minJS->add(dirname(__DIR__, 3) . "/shared/scripts/jquery.form.js");
    $minJS->add(dirname(__DIR__, 3) . "/shared/scripts/jquery.mask.js");
    $minJS->add(dirname(__DIR__, 3) . "/shared/scripts/core.js");

    $jsDir = scandir(__DIR__ . "/../../../themes/" . CONF_VIEW_THEME . "/assets/js");
    if (!empty($jsDir)) {
        foreach ($jsDir as $js) {
            $jsFile = __DIR__ . "/../../../themes/" . CONF_VIEW_THEME . "/assets/js/{$js}";
            if (is_file($jsFile) && pathinfo($jsFile)['extension'] == "js") {
                $minJS->add($jsFile);
            }
        }
    }

    // Minify JS
    $minJS->minify(__DIR__ . "/../../../themes/" . CONF_VIEW_THEME . "/assets/scripts.js");
}