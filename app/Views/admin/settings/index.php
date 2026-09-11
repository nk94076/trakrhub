<?php

use App\Core\View;

/** @var array $groups */
/** @var string $activeGroup */
/** @var array $settings */

$labels = [
    'general' => 'General', 'branding' => 'Branding', 'theme' => 'Theme', 'typography' => 'Typography',
    'business' => 'Business Info', 'social' => 'Social Links', 'analytics' => 'Analytics', 'smtp' => 'SMTP',
    'recaptcha' => 'reCAPTCHA', 'seo' => 'SEO Defaults', 'cookie_banner' => 'Cookie Banner',
];

$fontLabels = [
    'inter' => 'Inter — clean, versatile sans-serif',
    'poppins' => 'Poppins — geometric, friendly',
    'sora' => 'Sora — modern, techy display',
    'plus-jakarta-sans' => 'Plus Jakarta Sans — warm, professional',
];
?>
<div class="flex flex-wrap gap-2 mb-6">
  <?php foreach ($groups as $group): ?>
    <a href="<?= View::url('admin/settings/' . $group) ?>"
       class="px-4 py-2 rounded-full text-sm font-medium <?= $group === $activeGroup ? 'bg-gradient-to-r from-brand-500 to-accent-500 text-white' : 'bg-white border border-slate-200 text-slate-600' ?>">
      <?= View::e($labels[$group] ?? ucfirst($group)) ?>
    </a>
  <?php endforeach; ?>
</div>

<div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6 max-w-2xl">
  <form action="<?= View::url('admin/settings/' . $activeGroup) ?>" method="POST" class="space-y-5">
    <?= View::csrfField() ?>
    <?php if ($settings === []): ?>
      <p class="text-sm text-slate-400">No settings in this group.</p>
    <?php endif; ?>
    <?php foreach ($settings as $setting): ?>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">
          <?= View::e(ucwords(str_replace('_', ' ', $setting['key']))) ?>
        </label>
        <?php if ($activeGroup === 'typography' && in_array($setting['key'], ['heading_font', 'body_font'], true)): ?>
          <select name="settings[<?= View::e($setting['key']) ?>]" class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm">
            <?php foreach ($fontLabels as $slug => $label): ?>
              <option value="<?= View::e($slug) ?>" <?= $setting['value'] === $slug ? 'selected' : '' ?>><?= View::e($label) ?></option>
            <?php endforeach; ?>
          </select>
        <?php elseif ($setting['type'] === 'textarea'): ?>
          <textarea name="settings[<?= View::e($setting['key']) ?>]" rows="3"
                    class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-400"><?= View::e($setting['value']) ?></textarea>
        <?php elseif ($setting['type'] === 'color'): ?>
          <div class="flex items-center gap-3">
            <input type="color" name="settings[<?= View::e($setting['key']) ?>]" value="<?= View::e($setting['value'] ?: '#7c3aed') ?>" class="h-10 w-14 rounded border border-slate-200">
            <span class="text-xs text-slate-400"><?= View::e($setting['value']) ?></span>
          </div>
        <?php elseif ($setting['type'] === 'boolean'): ?>
          <select name="settings[<?= View::e($setting['key']) ?>]" class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm">
            <option value="true" <?= $setting['value'] === 'true' ? 'selected' : '' ?>>Enabled</option>
            <option value="false" <?= $setting['value'] === 'false' ? 'selected' : '' ?>>Disabled</option>
          </select>
        <?php elseif ($setting['type'] === 'image'): ?>
          <?php $inputId = 'setting-image-' . $setting['key']; ?>
          <div class="flex items-center gap-3">
            <?php if ($setting['value']): ?>
              <img src="<?= View::e(View::url(ltrim($setting['value'], '/'))) ?>" alt="" class="h-10 w-10 rounded-lg object-contain border border-slate-200 bg-slate-50">
            <?php endif; ?>
            <input type="text" id="<?= $inputId ?>" name="settings[<?= View::e($setting['key']) ?>]" value="<?= View::e($setting['value']) ?>"
                   class="flex-1 rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-400"
                   placeholder="No image chosen">
            <button type="button" class="shrink-0 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50"
                    onclick="openMediaPicker((path) => { document.getElementById('<?= $inputId ?>').value = path; })">
              Browse&hellip;
            </button>
          </div>
        <?php else: ?>
          <input type="text" name="settings[<?= View::e($setting['key']) ?>]" value="<?= View::e($setting['value']) ?>"
                 class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-400">
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
    <?php if ($settings !== []): ?>
      <button type="submit" class="rounded-lg bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold px-6 py-2.5 shadow-lg shadow-brand-500/30 hover:shadow-xl transition">
        Save Changes
      </button>
    <?php endif; ?>
  </form>
</div>
