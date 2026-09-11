<?php

use App\Core\View;

/** @var string $query */
/** @var array $posts */
/** @var array $services */
/** @var array $pages */

$totalResults = count($posts) + count($services) + count($pages);
?>
<section class="relative overflow-hidden bg-slate-950 py-20">
  <div class="absolute inset-0 opacity-60" style="background-image: radial-gradient(circle at 12% 15%, rgb(var(--brand-600) / 0.35), transparent 40%), radial-gradient(circle at 88% 75%, rgb(var(--accent-600) / 0.3), transparent 40%);"></div>
  <div class="relative max-w-2xl mx-auto px-6 text-center">
    <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-6">Search</h1>
    <form action="<?= View::url('search') ?>" method="GET" class="flex gap-3">
      <input type="text" name="q" value="<?= View::e($query) ?>" placeholder="Search articles, services, pages..."
             class="flex-1 rounded-full border border-white/20 bg-white/10 text-white placeholder:text-slate-400 px-5 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-400">
      <button type="submit" class="rounded-full bg-gradient-to-r from-brand-500 to-accent-500 text-white font-semibold px-6 py-3">Search</button>
    </form>
  </div>
</section>

<?php if ($query !== ''): ?>
<section class="py-12">
  <div class="max-w-4xl mx-auto px-6">
    <p class="text-sm text-slate-500 mb-8"><?= $totalResults ?> result(s) for &ldquo;<?= View::e($query) ?>&rdquo;</p>

    <?php if ($posts !== []): ?>
      <h2 class="text-lg font-semibold text-slate-900 mb-3">Blog Articles</h2>
      <div class="space-y-3 mb-10">
        <?php foreach ($posts as $post): ?>
          <a href="<?= View::url('blog/' . $post['slug']) ?>" class="block rounded-xl border border-slate-100 bg-white shadow-sm p-4 hover:shadow-md transition">
            <div class="font-medium text-slate-900"><?= View::e($post['title']) ?></div>
            <div class="text-sm text-slate-500"><?= View::e($post['excerpt'] ?? '') ?></div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if ($services !== []): ?>
      <h2 class="text-lg font-semibold text-slate-900 mb-3">Services</h2>
      <div class="space-y-3 mb-10">
        <?php foreach ($services as $service): ?>
          <a href="<?= View::url('services/' . $service['slug']) ?>" class="block rounded-xl border border-slate-100 bg-white shadow-sm p-4 hover:shadow-md transition">
            <div class="font-medium text-slate-900"><?= View::e($service['title']) ?></div>
            <div class="text-sm text-slate-500"><?= View::e($service['short_description'] ?? '') ?></div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if ($pages !== []): ?>
      <h2 class="text-lg font-semibold text-slate-900 mb-3">Pages</h2>
      <div class="space-y-3">
        <?php foreach ($pages as $page): ?>
          <a href="<?= View::url($page['slug'] === 'home' ? '/' : $page['slug']) ?>" class="block rounded-xl border border-slate-100 bg-white shadow-sm p-4 hover:shadow-md transition">
            <div class="font-medium text-slate-900"><?= View::e($page['title']) ?></div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if ($totalResults === 0): ?>
      <p class="text-center text-slate-400 py-10">No results found. Try a different search term.</p>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>
