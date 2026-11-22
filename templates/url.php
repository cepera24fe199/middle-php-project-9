<h1 class="mb-4 break-all text-3xl font-bold">
    <?= htmlspecialchars(sprintf($translations['site_details'], $url['name'])) ?>
</h1>
<table data-test="url" class="w-full border-collapse text-left">
    <tbody>
        <tr class="border-b border-slate-200">
            <th scope="row" class="p-3"><?= htmlspecialchars($translations['id']) ?></th>
            <td class="p-3"><?= (int) $url['id'] ?></td>
        </tr>
        <tr class="border-b border-slate-200">
            <th scope="row" class="p-3"><?= htmlspecialchars($translations['name']) ?></th>
            <td class="break-all p-3"><?= htmlspecialchars($url['name']) ?></td>
        </tr>
        <tr class="border-b border-slate-200">
            <th scope="row" class="p-3"><?= htmlspecialchars($translations['created_at']) ?></th>
            <td class="whitespace-nowrap p-3"><?= htmlspecialchars($url['created_at']) ?></td>
        </tr>
    </tbody>
</table>

<h2 class="mb-4 mt-8 text-2xl font-bold"><?= htmlspecialchars($translations['checks']) ?></h2>
<form method="post" action="<?= htmlspecialchars($routes->urlFor('url.checks.store', ['id' => $url['id']])) ?>"
      class="mb-4">
    <input type="submit" value="<?= htmlspecialchars($translations['run_check']) ?>"
           class="cursor-pointer rounded-md bg-blue-700 px-4 py-2 font-semibold text-white hover:bg-blue-800" />
</form>

<table data-test="checks" class="w-full border-collapse text-left">
    <thead>
        <tr class="border-b border-slate-300">
            <th scope="col" class="p-3"><?= htmlspecialchars($translations['id']) ?></th>
            <th scope="col" class="p-3"><?= htmlspecialchars($translations['status_code']) ?></th>
            <th scope="col" class="p-3">h1</th>
            <th scope="col" class="p-3">title</th>
            <th scope="col" class="p-3">description</th>
            <th scope="col" class="p-3"><?= htmlspecialchars($translations['created_at']) ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($checks as $check) : ?>
            <tr class="border-b border-slate-200">
                <td class="p-3"><?= (int) $check['id'] ?></td>
                <td class="p-3"><?= htmlspecialchars((string) ($check['status_code'] ?? '')) ?></td>
                <?php foreach (['h1', 'title', 'description'] as $field) : ?>
                    <?php
                    $value = $check[$field] ?? '';
                    if (mb_strlen($value, 'UTF-8') > 200) {
                        $value = mb_substr($value, 0, 196, 'UTF-8') . '....';
                    }
                    ?>
                    <td class="break-all p-3"><?= htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                <?php endforeach; ?>
                <td class="whitespace-nowrap p-3"><?= htmlspecialchars($check['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
