<?php

use App\Core\Icon;
use App\Core\View;

/** @var array $post */
/** @var array $tableOfContents */
/** @var array $related */
/** @var array $tags */

$authorInitial = $post['author_name'] ? strtoupper(substr($post['author_name'], 0, 1)) : 'T';
?>
<article class="py-16">
  <div class="max-w-6xl mx-auto px-6">
    <nav class="text-xs text-slate-400 mb-4" aria-label="Breadcrumb">
      <a href="<?= View::url('/') ?>" class="hover:text-brand-600">Home</a>
      <span class="mx-1.5">/</span>
      <a href="<?= View::url('blog') ?>" class="hover:text-brand-600">Blog</a>
      <?php if ($post['category_name']): ?>
        <span class="mx-1.5">/</span>
        <a href="<?= View::url('blog?category=' . $post['category_slug']) ?>" class="hover:text-brand-600"><?= View::e($post['category_name']) ?></a>
      <?php endif; ?>
    </nav>

    <h1 class="text-3xl md:text-5xl font-extrabold text-slate-900 max-w-3xl"><?= View::e($post['title']) ?></h1>
    <div class="mt-4 flex items-center gap-3 text-sm text-slate-500">
      <?php if ($post['author_name']): ?><span><?= View::e($post['author_name']) ?></span><span>&middot;</span><?php endif; ?>
      <span><?= (int) $post['reading_time_minutes'] ?> min read</span>
      <span>&middot;</span>
      <span><?= (int) $post['views'] ?> views</span>
    </div>

    <?php if (!empty($post['featured_image'])): ?>
      <div class="mt-8 rounded-3xl overflow-hidden aspect-[21/9] bg-slate-100">
        <img src="<?= View::e(View::url(ltrim($post['featured_image'], '/'))) ?>" alt="<?= View::e($post['title']) ?>" class="w-full h-full object-cover">
      </div>
    <?php endif; ?>

    <div class="mt-10 grid lg:grid-cols-[240px_1fr] gap-10">
      <!-- Sticky sidebar: TOC + CTA -->
      <?php if ($tableOfContents !== []): ?>
        <aside class="hidden lg:block">
          <div class="sticky top-24 space-y-6">
            <div class="rounded-2xl bg-slate-50 border border-slate-100 p-5">
              <div class="text-xs font-semibold uppercase text-slate-400 mb-3">On This Page</div>
              <ul class="space-y-2 text-sm">
                <?php foreach ($tableOfContents as $item): ?>
                  <li><a href="#<?= View::e($item['anchor'] ?? '') ?>" class="text-slate-600 hover:text-brand-600 transition"><?= View::e($item['label'] ?? '') ?></a></li>
                <?php endforeach; ?>
              </ul>
            </div>
            <div class="rounded-2xl bg-gradient-to-br from-brand-600 to-accent-600 p-5 text-white">
              <h3 class="font-semibold mb-1.5">Talk to Us</h3>
              <p class="text-xs text-white/80 leading-relaxed mb-4">Have a question about performance marketing? Our team responds within one business day.</p>
              <a href="<?= View::url('contact') ?>" class="block text-center rounded-full bg-white text-brand-700 text-sm font-semibold py-2.5 hover:shadow-lg transition">Contact Us</a>
            </div>
          </div>
        </aside>
      <?php endif; ?>

      <div class="<?= $tableOfContents !== [] ? '' : 'lg:col-span-2' ?>">
        <!-- Mobile-only TOC (sidebar is desktop-only) -->
        <?php if ($tableOfContents !== []): ?>
          <div class="lg:hidden mb-8 rounded-2xl bg-slate-50 border border-slate-100 p-5">
            <div class="text-xs font-semibold uppercase text-slate-400 mb-3">On This Page</div>
            <ul class="space-y-2 text-sm">
              <?php foreach ($tableOfContents as $item): ?>
                <li><a href="#<?= View::e($item['anchor'] ?? '') ?>" class="text-brand-600 hover:underline"><?= View::e($item['label'] ?? '') ?></a></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <!-- Admin-authored rich content (not user input) — intentionally unescaped so headings/code blocks/links render. -->
        <div class="prose prose-slate max-w-none text-slate-700 leading-relaxed">
          <?= $post['content'] ?>
        </div>

        <?php if ($tags !== []): ?>
          <div class="mt-10 flex flex-wrap gap-2">
            <?php foreach ($tags as $tag): ?>
              <span class="text-xs rounded-full bg-slate-100 text-slate-600 px-3 py-1"><?= View::e($tag['name']) ?></span>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php if ($post['author_name']): ?>
          <div class="mt-12 rounded-2xl bg-slate-50 border border-slate-100 p-6 flex gap-4">
            <span class="shrink-0 w-14 h-14 rounded-full bg-gradient-to-br from-brand-500 to-accent-500 text-white text-xl font-bold flex items-center justify-center">
              <?= View::e($authorInitial) ?>
            </span>
            <div>
              <div class="font-semibold text-slate-900"><?= View::e($post['author_name']) ?></div>
              <?php if (!empty($post['author_job_title'])): ?>
                <div class="text-xs text-slate-400 mb-1.5"><?= View::e($post['author_job_title']) ?></div>
              <?php endif; ?>
              <?php if ($post['author_bio']): ?>
                <p class="text-sm text-slate-500 leading-relaxed"><?= View::e($post['author_bio']) ?></p>
              <?php endif; ?>
              <?php if (!empty($post['author_linkedin']) || !empty($post['author_twitter'])): ?>
                <div class="mt-2.5 flex items-center gap-3">
                  <?php if (!empty($post['author_linkedin'])): ?>
                    <a href="<?= View::e($post['author_linkedin']) ?>" target="_blank" rel="noopener" class="text-xs font-medium text-brand-600 hover:underline">LinkedIn</a>
                  <?php endif; ?>
                  <?php if (!empty($post['author_twitter'])): ?>
                    <a href="<?= View::e($post['author_twitter']) ?>" target="_blank" rel="noopener" class="text-xs font-medium text-brand-600 hover:underline">Twitter</a>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</article>

<?php if ($related !== []): ?>
<section class="py-16 bg-slate-50/60">
  <div class="max-w-6xl mx-auto px-6">
    <h2 class="text-2xl font-bold text-slate-900 mb-8 text-center">Related Articles</h2>
    <div class="grid sm:grid-cols-3 gap-6">
      <?php foreach ($related as $item): ?>
        <a href="<?= View::url('blog/' . $item['slug']) ?>" class="group rounded-2xl overflow-hidden bg-white border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 block">
          <div class="h-36 bg-gradient-to-br from-brand-500 to-accent-500 flex items-center justify-center overflow-hidden">
            <?php if (!empty($item['featured_image'])): ?>
              <img src="<?= View::e(View::url(ltrim($item['featured_image'], '/'))) ?>" alt="<?= View::e($item['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            <?php else: ?>
              <?= Icon::render('chat-bubble', 'w-8 h-8 text-white/70') ?>
            <?php endif; ?>
          </div>
          <div class="p-5">
            <?php if (!empty($item['category_name'])): ?><div class="text-xs font-medium text-brand-600 mb-1"><?= View::e($item['category_name']) ?></div><?php endif; ?>
            <h3 class="font-semibold text-slate-900 group-hover:text-brand-600 transition"><?= View::e($item['title']) ?></h3>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
