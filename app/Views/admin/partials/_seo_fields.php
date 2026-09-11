<?php

use App\Core\View;

/** @var array|null $seo */
$seo = $seo ?? [];
?>
<div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6 space-y-3">
  <h3 class="font-semibold text-slate-900 mb-1">SEO</h3>
  <input type="text" name="seo_title" value="<?= View::e($seo['seo_title'] ?? '') ?>" placeholder="SEO title (defaults to the title above)"
         class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
  <textarea name="meta_description" rows="2" placeholder="Meta description"
            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"><?= View::e($seo['meta_description'] ?? '') ?></textarea>
  <input type="text" name="meta_keywords" value="<?= View::e($seo['meta_keywords'] ?? '') ?>" placeholder="Meta keywords (comma separated)"
         class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
  <input type="text" name="canonical_url" value="<?= View::e($seo['canonical_url'] ?? '') ?>" placeholder="Canonical URL (leave blank to use the default)"
         class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
  <div class="grid grid-cols-2 gap-3">
    <select name="robots_index" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      <option value="index" <?= ($seo['robots_index'] ?? 'index') === 'index' ? 'selected' : '' ?>>Index</option>
      <option value="noindex" <?= ($seo['robots_index'] ?? '') === 'noindex' ? 'selected' : '' ?>>No Index</option>
    </select>
    <select name="robots_follow" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      <option value="follow" <?= ($seo['robots_follow'] ?? 'follow') === 'follow' ? 'selected' : '' ?>>Follow</option>
      <option value="nofollow" <?= ($seo['robots_follow'] ?? '') === 'nofollow' ? 'selected' : '' ?>>No Follow</option>
    </select>
  </div>
  <input type="text" name="og_title" value="<?= View::e($seo['og_title'] ?? '') ?>" placeholder="Open Graph title (optional)"
         class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
  <textarea name="og_description" rows="2" placeholder="Open Graph description (optional)"
            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"><?= View::e($seo['og_description'] ?? '') ?></textarea>
  <select name="twitter_card" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
    <option value="summary_large_image" <?= ($seo['twitter_card'] ?? 'summary_large_image') === 'summary_large_image' ? 'selected' : '' ?>>Summary Large Image</option>
    <option value="summary" <?= ($seo['twitter_card'] ?? '') === 'summary' ? 'selected' : '' ?>>Summary</option>
  </select>
  <details class="text-xs">
    <summary class="cursor-pointer text-slate-500">Custom schema.org JSON-LD (advanced)</summary>
    <p class="text-slate-400 mt-1 mb-1">For schema types beyond the automatic ones (Organization, WebSite, Breadcrumb, FAQPage, Article) — e.g. Product, Video, Person, LocalBusiness. Leave blank to skip.</p>
    <input type="text" name="schema_type" value="<?= View::e($seo['schema_type'] ?? '') ?>" placeholder="Schema type (e.g. Product)"
           class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm mb-2">
    <textarea name="schema_json" rows="4" placeholder='{"@type": "Product", "name": "..."}'
              class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs font-mono"><?= View::e($seo['schema_json'] ?? '') ?></textarea>
  </details>
</div>
