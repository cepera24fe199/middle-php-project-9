<!doctype html>
<html lang="<?= htmlspecialchars($locale) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($translations['site_title']) ?></title>
    <link href="/assets/css/main.css" rel="stylesheet" />
</head>
<body class="min-h-screen bg-white text-slate-900">
    <nav class="bg-slate-900 text-white">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-x-6 px-4 py-4 sm:px-6">
            <a href="<?= htmlspecialchars($routes->urlFor('home')) ?>" class="text-lg font-semibold">
                <?= htmlspecialchars($translations['site_title']) ?>
            </a>
            <a href="<?= htmlspecialchars($routes->urlFor('home')) ?>" class="hover:underline">
                <?= htmlspecialchars($translations['home']) ?>
            </a>
            <a href="<?= htmlspecialchars($routes->urlFor('urls.index')) ?>" class="hover:underline">
                <?= htmlspecialchars($translations['sites']) ?>
            </a>
        </div>
    </nav>
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6">
        <?php require __DIR__ . '/flash.php'; ?>
        <?= $content ?>
    </main>
</body>
</html>
