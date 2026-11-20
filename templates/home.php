<section class="rounded-lg bg-slate-100 p-6 sm:p-12">
    <h1 class="text-4xl font-bold sm:text-5xl"><?= htmlspecialchars($translations['site_title']) ?></h1>
    <p class="mt-3 text-lg"><?= htmlspecialchars($translations['home_description']) ?></p>
    <form action="/urls" method="post" class="mt-6 flex max-w-3xl flex-col gap-3 sm:flex-row">
        <label for="url" class="sr-only"><?= htmlspecialchars($translations['url_label']) ?></label>
        <input
            type="url"
            id="url"
            name="url[name]"
            placeholder="https://www.example.com"
            required
            class="w-full min-w-0 rounded-md border-slate-300 focus:border-blue-600 focus:ring-blue-600"
        >
        <button type="submit" class="rounded-md bg-blue-600 px-6 py-2 font-semibold text-white hover:bg-blue-700">
            <?= htmlspecialchars($translations['check_button']) ?>
        </button>
    </form>
</section>
