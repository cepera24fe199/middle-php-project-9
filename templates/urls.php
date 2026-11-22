<h1 class="mb-4 text-3xl font-bold"><?= htmlspecialchars($translations['sites']) ?></h1>
<div class="overflow-x-auto">
    <table data-test="urls" class="w-full border-collapse text-left">
        <thead>
            <tr class="border-b border-slate-300">
                <th scope="col" class="p-3"><?= htmlspecialchars($translations['id']) ?></th>
                <th scope="col" class="p-3"><?= htmlspecialchars($translations['name']) ?></th>
                <th scope="col" class="p-3"><?= htmlspecialchars($translations['created_at']) ?></th>
                <th scope="col" class="p-3"><?= htmlspecialchars($translations['last_check_at']) ?></th>
                <th scope="col" class="p-3"><?= htmlspecialchars($translations['status_code']) ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($urls as $url) : ?>
                <tr class="border-b border-slate-200">
                    <td class="p-3"><?= (int) $url['id'] ?></td>
                    <td class="break-all p-3">
                        <a href="<?= htmlspecialchars($routes->urlFor('url.show', ['id' => $url['id']])) ?>"
                           class="text-blue-700 underline"><?= htmlspecialchars($url['name']) ?></a>
                    </td>
                    <td class="whitespace-nowrap p-3"><?= htmlspecialchars($url['created_at']) ?></td>
                    <td class="whitespace-nowrap p-3"><?= htmlspecialchars($url['last_check_at'] ?? '') ?></td>
                    <td class="p-3"><?= htmlspecialchars((string) ($url['last_check_status_code'] ?? '')) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
