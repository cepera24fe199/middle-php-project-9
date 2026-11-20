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
        <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6">
            <a href="/" class="text-lg font-semibold"><?= htmlspecialchars($translations['site_title']) ?></a>
        </div>
    </nav>
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6">
        <?php foreach (($flashMessages ?? []) as $message) : ?>
            <div role="alert" class="mb-4 rounded-md bg-blue-50 px-4 py-3 text-blue-900">
                <?= htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
            </div>
        <?php endforeach; ?>
        <?= $content ?>
    </main>
</body>
</html>
