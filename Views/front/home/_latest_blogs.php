<?php

use App\Core\Icon;
use App\Core\View;

/** @var array $content */
$blogPosts = $blogPosts ?? [];
?>
<?php if ($blogPosts !== []): ?>
<section class="py-20 bg-slate-50/60">
  <div class="max-w-6xl mx-auto px-6">
    <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 text-center mb-14"><?= View::e($content['title'] ?? '') ?></h2>
    <div class="grid md:grid-cols-3 gap-6">
      <?php foreach ($blogPosts as $post): ?>
        <a href="<?= View::url('blog/' . $post['slug']) ?>" class="rounded-2xl overflow-hidden bg-white border border-slate-100 shadow-sm hover:shadow-lg transition block">
          <div class="h-40 bg-gradient-to-br from-brand-500 to-accent-500 flex items-center justify-center text-white/70">
            <?= Icon::render('chat-bubble', 'w-10 h-10') ?>
          </div>
          <div class="p-6">
            <h3 class="font-semibold text-slate-900"><?= View::e($post['title']) ?></h3>
            <p class="mt-2 text-sm text-slate-500"><?= View::e($post['excerpt'] ?? '') ?></p>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
