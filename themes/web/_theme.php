<!doctype html>
<html class="no-js" lang="pt_BR">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?= $head; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;900&display=swap" rel="stylesheet">

    <?php if (!empty($css)): ?>
        <?php foreach ($css as $file): ?>
            <link rel="stylesheet" href="<?= $file; ?>">
        <?php endforeach; ?>
    <?php endif; ?>

    <link rel="stylesheet" href="<?= theme("assets/styles.css"); ?>">

    <?= $v->section('styles') ?>
</head>

<body class="bg-body">
    <header class="container menu ds-fixed">
        <div class="content flex">
            <div class="flex flex-4 alc-center">
                <h1 class="uppercase">Treine seu ouvido</h1>
            </div>
            
            <div class="flex flex-3 alc-center">
                <ul class="menu_ul flex">
                    <li class="menu_ul_item"><a href="<?= $route->route("web.home"); ?>">Treino Prático</a></li>
                    <li class="menu_ul_item"><a href="<?= $route->route("web.challenge"); ?>">Desafio</a></li>
                </ul>
            </div>
        </div>
    </header>

    <main>
        <?= $v->section('content') ?>
    </main>

    <footer>

    </footer>

    <!-- Loader FullScreen and Modals -->
    <?= $v->section('modals') ?>

    <!-- JS here -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <?php if (!empty($js)): ?>
        <?php foreach ($js as $file): ?>
            <script src="<?= $file; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    <script src="<?= theme("assets/scripts.js"); ?>"></script>

    <?= $v->section('scripts') ?>
</body>
</html>