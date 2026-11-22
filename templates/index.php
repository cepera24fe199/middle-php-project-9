<section class="rounded-lg bg-slate-100 p-6 sm:p-12">
    <h1 class="text-4xl font-bold sm:text-5xl"><?= htmlspecialchars($translations['site_title']) ?></h1>
    <p class="mt-3 text-lg"><?= htmlspecialchars($translations['home_description']) ?></p>
    <form action="<?= htmlspecialchars($routes->urlFor('urls.store')) ?>" method="post" novalidate
          class="mt-6 max-w-3xl">
        <div class="flex flex-col gap-3 sm:flex-row">
            <label for="url" class="sr-only"><?= htmlspecialchars($translations['url_label']) ?></label>
            <input
                type="url"
                id="url"
                name="url"
                value="<?= htmlspecialchars($value ?? '') ?>"
                placeholder="https://www.example.com"
                required
                <?php if (isset($error)) : ?>
                    aria-invalid="true" aria-describedby="url-error"
                <?php endif; ?>
                class="w-full min-w-0 rounded-md border-slate-300 focus:border-blue-600 focus:ring-blue-600"
            >
            <input type="submit" value="<?= htmlspecialchars($translations['check_button']) ?>"
                   class="cursor-pointer rounded-md bg-blue-600 px-6 py-2 font-semibold text-white uppercase hover:bg-blue-700">
        </div>
        <?php if (isset($error)) : ?>
            <p id="url-error" role="alert" class="mt-2 text-red-700"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
    </form>
</section>
