<section class="py-8 text-center sm:py-16">
    <p class="mb-2 font-semibold text-slate-600">404</p>
    <h1 class="text-4xl font-bold"><?= htmlspecialchars($translations['not_found_title']) ?></h1>
    <p class="mt-4 text-slate-600"><?= htmlspecialchars($translations['not_found_description']) ?></p>
    <a
        href="<?= htmlspecialchars($routes->urlFor('home')) ?>"
        class="mt-6 inline-block rounded-md bg-slate-900 px-4 py-2 font-semibold text-white hover:underline"
    >
        <?= htmlspecialchars($translations['back_to_home']) ?>
    </a>
</section>
