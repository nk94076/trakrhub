<?php

use App\Core\Icon;
use App\Core\View;

/** @var array $posts */
/** @var array $categories */
/** @var string $activeCategory */
/** @var int $currentPage */
/** @var int $lastPage */

$featured = $currentPage === 1 && $activeCategory === '' ? ($posts[0] ?? null) : null;
$gridPosts = $featured !== null ? array_slice($posts, 1) : $posts;

$gridColsClass = match (true) {
    count($gridPosts) >= 3 => 'lg:grid-cols-3',
    count($gridPosts) === 2 => 'lg:grid-cols-2',
    default => 'lg:grid-cols-1',
};

$thumb = static function (array $post): ?string {
    return $post['featured_image'] ?? null;
};
?>
<?php View::partial('partials.page-banner', [
  'title' => 'Blog',
  'subtitle' => 'Performance marketing insights, playbooks and network updates.',
]); ?>

<section class="py-16">
  <div class="max-w-6xl mx-auto px-6">
    <?php if ($categories !== []): ?>
      <div class="flex flex-wrap justify-center gap-2 mb-12">
        <a href="<?= View::url('blog') ?>" class="px-4 py-2 rounded-full text-sm font-medium transition <?= $activeCategory === '' ? 'bg-gradient-to-r from-brand-500 to-accent-500 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:border-brand-300' ?>">All</a>
        <?php foreach ($categories as $cat): ?>
          <a href="<?= View::url('blog?category=' . $cat['slug']) ?>" class="px-4 py-2 rounded-full text-sm font-medium transition <?= $activeCategory === $cat['slug'] ? 'bg-gradient-to-r from-brand-500 to-accent-500 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:border-brand-300' ?>"><?= View::e($cat['name']) ?></a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if ($posts === []): ?>
      <p class="text-center text-slate-400">No articles published yet — check back soon.</p>
    <?php else: ?>

      <?php if ($featured !== null): ?>
        <a href="<?= View::url('blog/' . $featured['slug']) ?>" class="group grid md:grid-cols-2 gap-8 items-center rounded-3xl overflow-hidden bg-white border border-slate-100 shadow-sm hover:shadow-xl transition-shadow duration-200 mb-12">
          <div class="h-64 md:h-full bg-gradient-to-br from-brand-500 to-accent-500 flex items-center justify-center overflow-hidden">
            <?php if ($thumb($featured)): ?>
              <img src="<?= View::e(View::url(ltrim($thumb($featured), '/'))) ?>" alt="<?= View::e($featured['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            <?php else: ?>
              <?= Icon::render('chat-bubble', 'w-14 h-14 text-white/70') ?>
            <?php endif; ?>
          </div>
          <div class="p-6 md:p-4 md:pr-10">
            <span class="inline-block text-xs font-semibold text-brand-600 bg-brand-50 rounded-full px-3 py-1 mb-3">Latest Article</span>
            <?php if ($featured['category_name']): ?><div class="text-xs font-medium text-slate-400 mb-1"><?= View::e($featured['category_name']) ?></div><?php endif; ?>
            <h2 class="text-2xl font-bold text-slate-900 group-hover:text-brand-600 transition"><?= View::e($featured['title']) ?></h2>
            <p class="mt-3 text-slate-500 leading-relaxed"><?= View::e($featured['excerpt'] ?? '') ?></p>
            <div class="mt-4 flex items-center gap-3 text-xs text-slate-400">
              <?php if ($featured['author_name']): ?><span><?= View::e($featured['author_name']) ?></span><span>&middot;</span><?php endif; ?>
              <span><?= (int) $featured['reading_time_minutes'] ?> min read</span>
            </div>
          </div>
        </a>
      <?php endif; ?>

      <?php if ($gridPosts !== []): ?>
        <div class="grid sm:grid-cols-2 <?= $gridColsClass ?> gap-6">
          <?php foreach ($gridPosts as $post): ?>
            <a href="<?= View::url('blog/' . $post['slug']) ?>" class="group rounded-2xl overflow-hidden bg-white border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 block">
              <div class="h-40 bg-gradient-to-br from-brand-500 to-accent-500 flex items-center justify-center overflow-hidden">
                <?php if ($thumb($post)): ?>
                  <img src="<?= View::e(View::url(ltrim($thumb($post), '/'))) ?>" alt="<?= View::e($post['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                <?php else: ?>
                  <?= Icon::render('chat-bubble', 'w-10 h-10 text-white/70') ?>
                <?php endif; ?>
              </div>
              <div class="p-6">
                <?php if ($post['category_name']): ?><div class="text-xs font-medium text-brand-600 mb-1"><?= View::e($post['category_name']) ?></div><?php endif; ?>
                <h3 class="font-semibold text-slate-900 group-hover:text-brand-600 transition"><?= View::e($post['title']) ?></h3>
                <p class="mt-2 text-sm text-slate-500"><?= View::e($post['excerpt'] ?? '') ?></p>
                <div class="mt-3 text-xs text-slate-400"><?= (int) $post['reading_time_minutes'] ?> min read</div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if ($lastPage > 1): ?>
        <div class="flex justify-center gap-2 mt-12">
          <?php for ($i = 1; $i <= $lastPage; $i++): ?>
            <a href="<?= View::url('blog?page=' . $i . ($activeCategory !== '' ? '&category=' . $activeCategory : '')) ?>"
               class="w-9 h-9 flex items-center justify-center rounded-full text-sm transition <?= $i === $currentPage ? 'bg-brand-500 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:border-brand-300' ?>">
              <?= $i ?>
            </a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>
