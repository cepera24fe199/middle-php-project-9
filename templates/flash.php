<?php foreach (($flashMessages ?? []) as $message) : ?>
    <?php
    $colorClasses = match ($message['type'] ?? 'info') {
        'success' => 'bg-green-50 text-green-900',
        'error' => 'bg-red-50 text-red-900',
        default => 'bg-blue-50 text-blue-900',
    };
    ?>
    <div role="alert" class="mb-4 rounded-md px-4 py-3 <?= $colorClasses ?>">
        <?= htmlspecialchars($message['text'] ?? $message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
    </div>
<?php endforeach; ?>
