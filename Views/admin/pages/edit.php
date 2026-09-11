<?php

use App\Core\Csrf;
use App\Core\View;

/** @var array $page */
/** @var array $sections */
/** @var array|null $seo */

$knownTypes = [
    'hero', 'trusted_by', 'statistics', 'services_grid', 'campaign_types', 'why_choose_us',
    'how_it_works', 'publisher_benefits', 'advertiser_benefits', 'industries', 'technology',
    'process', 'testimonials', 'latest_blogs', 'faq', 'contact_cta', 'newsletter', 'custom_html',
    'analytics_chart',
];
?>
<div class="grid lg:grid-cols-[1fr_320px] gap-6">
  <div>
    <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6 mb-6">
      <h3 class="font-semibold text-slate-900 mb-4">Page Details</h3>
      <form action="<?= View::url('admin/pages/' . $page['id']) ?>" method="POST" class="grid sm:grid-cols-2 gap-4">
        <?= View::csrfField() ?>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Title</label>
          <input type="text" name="title" value="<?= View::e($page['title']) ?>" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Slug</label>
          <input type="text" name="slug" value="<?= View::e($page['slug']) ?>" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" <?= $page['is_system'] ? 'readonly' : '' ?>>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
          <select name="status" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
            <option value="draft" <?= $page['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
            <option value="published" <?= $page['status'] === 'published' ? 'selected' : '' ?>>Published</option>
          </select>
        </div>
        <div class="flex items-end">
          <button type="submit" class="w-full rounded-lg bg-slate-900 text-white text-sm font-semibold py-2.5">Save Page Details</button>
        </div>
        <div class="sm:col-span-2">
          <?php View::partial('admin.partials._seo_fields', ['seo' => $seo]); ?>
        </div>
      </form>
    </div>

    <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6">
      <h3 class="font-semibold text-slate-900 mb-1">Sections <span class="text-xs text-slate-400 font-normal">(drag to reorder)</span></h3>
      <p class="text-xs text-slate-400 mb-4">Every block on this page is built from these sections. Nothing is hardcoded — add, edit, reorder, publish or remove sections freely.</p>

      <ul id="sections-list" class="space-y-2">
        <?php foreach ($sections as $section): ?>
          <li draggable="true" data-id="<?= (int) $section['id'] ?>"
              class="rounded-lg border border-slate-100 bg-slate-50 px-4 py-3 cursor-move">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <span class="text-slate-300">&#8942;&#8942;</span>
                <div>
                  <div class="text-sm font-medium text-slate-800"><?= View::e($section['component_type']) ?></div>
                  <span class="text-[10px] uppercase tracking-wide rounded-full px-2 py-0.5 <?= $section['status'] === 'published' ? 'bg-green-50 text-green-700' : 'bg-slate-200 text-slate-500' ?>">
                    <?= View::e($section['status']) ?>
                  </span>
                </div>
              </div>
              <div class="flex items-center gap-3 text-xs">
                <button type="button" onclick='openEditSection(<?= json_encode($section, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="text-brand-600 hover:underline">Edit</button>
                <form action="<?= View::url('admin/pages/' . $page['id'] . '/sections/' . $section['id'] . '/toggle') ?>" method="POST">
                  <?= View::csrfField() ?>
                  <button class="text-slate-500 hover:underline"><?= $section['status'] === 'published' ? 'Unpublish' : 'Publish' ?></button>
                </form>
                <form action="<?= View::url('admin/pages/' . $page['id'] . '/sections/' . $section['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Delete this section?');">
                  <?= View::csrfField() ?>
                  <button class="text-red-600 hover:underline">Delete</button>
                </form>
              </div>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
      <?php if ($sections === []): ?>
        <p class="text-sm text-slate-400">No sections yet — add one using the panel on the right.</p>
      <?php endif; ?>
    </div>
  </div>

  <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6 h-fit">
    <h3 id="section-form-title" class="font-semibold text-slate-900 mb-4">Add Section</h3>
    <form id="section-form" action="<?= View::url('admin/pages/' . $page['id'] . '/sections') ?>" method="POST" class="space-y-3">
      <?= View::csrfField() ?>
      <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Component Type</label>
        <input list="known-types" id="section-type" name="component_type" required placeholder="e.g. hero"
               class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
        <datalist id="known-types">
          <?php foreach ($knownTypes as $type): ?><option value="<?= $type ?>"><?php endforeach; ?>
        </datalist>
      </div>
      <div>
        <div class="flex items-center justify-between mb-2">
          <label class="block text-xs font-medium text-slate-600">Content</label>
          <button type="button" id="mode-toggle" class="text-xs text-brand-600 hover:underline">Switch to raw JSON</button>
        </div>

        <div id="simple-fields" class="space-y-3"></div>

        <div id="json-mode-wrapper" class="hidden">
          <div class="flex justify-end mb-1">
            <button type="button" onclick="insertImagePath()" class="text-xs text-brand-600 hover:underline">Insert image path&hellip;</button>
          </div>
          <textarea id="section-content" name="content" rows="10"
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-brand-400">{
  "title": ""
}</textarea>
          <p class="text-[11px] text-slate-400 mt-1">Must be valid JSON. Match the keys the section's template expects (e.g. <code>title</code>, <code>items</code>).</p>
        </div>
      </div>
      <button type="submit" class="w-full rounded-lg bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold py-2.5">Save Section</button>
      <button type="button" onclick="resetSectionForm()" class="w-full text-xs text-slate-400 hover:text-slate-600">Cancel edit</button>
    </form>
  </div>
</div>

<script>
const sectionsBaseUrl = '<?= View::url('admin/pages/' . $page['id'] . '/sections') ?>';
const iconKeys = <?= json_encode($iconKeys ?? [], JSON_UNESCAPED_SLASHES) ?>;
let currentData = { title: '' };
let jsonModeActive = false;

function fieldLabel(key) {
  return key.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
}

function isImageKey(key) {
  return key === 'image' || key === 'logo' || key === 'og_image' || /image$/i.test(key);
}

function makeBlankLike(sample) {
  if (typeof sample === 'number') return 0;
  if (Array.isArray(sample)) return [];
  if (sample !== null && typeof sample === 'object') {
    const blank = {};
    Object.keys(sample).forEach((k) => { blank[k] = makeBlankLike(sample[k]); });
    return blank;
  }
  return '';
}

function textInputClasses() {
  return 'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-400';
}

function renderScalarInput(key, value, onChange) {
  const wrap = document.createElement('div');
  const label = document.createElement('label');
  label.className = 'block text-xs font-medium text-slate-600 mb-1';
  label.textContent = fieldLabel(key);
  wrap.appendChild(label);

  if (key === 'icon' && iconKeys.length) {
    const select = document.createElement('select');
    select.className = textInputClasses();
    iconKeys.forEach((k) => {
      const opt = document.createElement('option');
      opt.value = k;
      opt.textContent = k;
      if (k === value) opt.selected = true;
      select.appendChild(opt);
    });
    select.addEventListener('input', () => onChange(select.value));
    wrap.appendChild(select);
    return wrap;
  }

  if (isImageKey(key)) {
    const row = document.createElement('div');
    row.className = 'flex items-center gap-2';
    const input = document.createElement('input');
    input.type = 'text';
    input.value = value ?? '';
    input.className = 'flex-1 ' + textInputClasses();
    input.addEventListener('input', () => onChange(input.value));
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.textContent = 'Browse…';
    btn.className = 'shrink-0 rounded-lg border border-slate-200 px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50';
    btn.addEventListener('click', () => openMediaPicker((path) => { input.value = path; onChange(path); }));
    row.appendChild(input);
    row.appendChild(btn);
    wrap.appendChild(row);
    return wrap;
  }

  const longText = typeof value === 'string' && (value.length > 90 || ['body', 'description', 'subtitle', 'message'].includes(key));
  const field = document.createElement(longText ? 'textarea' : 'input');

  if (longText) {
    field.rows = 3;
  } else {
    field.type = 'text';
  }

  field.className = textInputClasses();
  field.value = value ?? '';
  field.addEventListener('input', () => onChange(field.value));
  wrap.appendChild(field);
  return wrap;
}

function renderArrayField(parentData, key) {
  const arr = parentData[key];
  const wrap = document.createElement('div');
  const label = document.createElement('span');
  label.className = 'block text-xs font-medium text-slate-600 mb-2';
  label.textContent = fieldLabel(key);
  wrap.appendChild(label);

  const list = document.createElement('div');
  list.className = 'space-y-2';
  wrap.appendChild(list);

  function rerenderList() {
    list.innerHTML = '';
    arr.forEach((item, idx) => {
      const row = document.createElement('div');
      row.className = 'relative rounded-lg border border-slate-200 p-3 pr-16 space-y-2 bg-slate-50/50';

      if (item !== null && typeof item === 'object' && !Array.isArray(item)) {
        Object.keys(item).forEach((subKey) => {
          row.appendChild(renderNode(item, subKey));
        });
      } else {
        row.classList.remove('space-y-2');
        const input = document.createElement('input');
        input.type = 'text';
        input.value = item ?? '';
        input.className = textInputClasses();
        input.addEventListener('input', () => { arr[idx] = input.value; syncToTextarea(); });
        row.appendChild(input);
      }

      const removeBtn = document.createElement('button');
      removeBtn.type = 'button';
      removeBtn.textContent = 'Remove';
      removeBtn.className = 'absolute top-2.5 right-2.5 text-[10px] font-medium text-red-500 hover:underline';
      removeBtn.addEventListener('click', () => { arr.splice(idx, 1); rerenderList(); syncToTextarea(); });
      row.appendChild(removeBtn);

      list.appendChild(row);
    });
  }

  rerenderList();

  const addBtn = document.createElement('button');
  addBtn.type = 'button';
  addBtn.textContent = '+ Add ' + fieldLabel(key).replace(/s$/i, '');
  addBtn.className = 'mt-2 text-xs text-brand-600 hover:underline';
  addBtn.addEventListener('click', () => {
    const sample = arr.length > 0 ? arr[arr.length - 1] : '';
    arr.push(makeBlankLike(sample));
    rerenderList();
    syncToTextarea();
  });
  wrap.appendChild(addBtn);

  return wrap;
}

function renderNode(data, key) {
  const value = data[key];

  if (Array.isArray(value)) {
    return renderArrayField(data, key);
  }

  if (value !== null && typeof value === 'object') {
    const box = document.createElement('div');
    box.className = 'rounded-lg border border-slate-200 p-3 space-y-3';
    const legend = document.createElement('div');
    legend.className = 'text-xs font-semibold text-slate-500 uppercase tracking-wide';
    legend.textContent = fieldLabel(key);
    box.appendChild(legend);
    Object.keys(value).forEach((k) => box.appendChild(renderNode(value, k)));
    return box;
  }

  return renderScalarInput(key, value, (v) => { data[key] = v; syncToTextarea(); });
}

function renderSimpleForm(data) {
  currentData = data;
  const container = document.getElementById('simple-fields');
  container.innerHTML = '';
  Object.keys(data).forEach((key) => container.appendChild(renderNode(data, key)));
  syncToTextarea();
}

function syncToTextarea() {
  document.getElementById('section-content').value = JSON.stringify(currentData);
}

function setMode(toJson) {
  jsonModeActive = toJson;
  document.getElementById('simple-fields').classList.toggle('hidden', toJson);
  document.getElementById('json-mode-wrapper').classList.toggle('hidden', !toJson);
  document.getElementById('mode-toggle').textContent = toJson ? 'Switch to simple editor' : 'Switch to raw JSON';

  if (toJson) {
    document.getElementById('section-content').value = JSON.stringify(currentData, null, 2);
  } else {
    try {
      const parsed = JSON.parse(document.getElementById('section-content').value || '{}');
      renderSimpleForm(parsed);
    } catch (e) {
      alert('That JSON is not valid, so it can't be shown in the simple editor. Fix it here first, or it will be rejected on save.');
      jsonModeActive = true;
      document.getElementById('simple-fields').classList.add('hidden');
      document.getElementById('json-mode-wrapper').classList.remove('hidden');
      document.getElementById('mode-toggle').textContent = 'Switch to simple editor';
    }
  }
}

document.getElementById('mode-toggle').addEventListener('click', () => setMode(!jsonModeActive));

function openEditSection(section) {
  document.getElementById('section-form-title').textContent = 'Edit Section';
  document.getElementById('section-type').value = section.component_type;
  document.getElementById('section-form').action = sectionsBaseUrl + '/' + section.id;
  jsonModeActive = false;
  document.getElementById('simple-fields').classList.remove('hidden');
  document.getElementById('json-mode-wrapper').classList.add('hidden');
  document.getElementById('mode-toggle').textContent = 'Switch to raw JSON';
  renderSimpleForm(JSON.parse(section.content));
}

function insertImagePath() {
  const textarea = document.getElementById('section-content');
  openMediaPicker((path) => {
    const start = textarea.selectionStart ?? textarea.value.length;
    const end = textarea.selectionEnd ?? textarea.value.length;
    textarea.value = textarea.value.slice(0, start) + path + textarea.value.slice(end);
    textarea.focus();
    textarea.selectionStart = textarea.selectionEnd = start + path.length;
  });
}

function resetSectionForm() {
  document.getElementById('section-form-title').textContent = 'Add Section';
  document.getElementById('section-type').value = '';
  document.getElementById('section-form').action = sectionsBaseUrl;
  jsonModeActive = false;
  document.getElementById('simple-fields').classList.remove('hidden');
  document.getElementById('json-mode-wrapper').classList.add('hidden');
  document.getElementById('mode-toggle').textContent = 'Switch to raw JSON';
  renderSimpleForm({ title: '' });
}

renderSimpleForm({ title: '' });

(function () {
  const list = document.getElementById('sections-list');
  if (!list) return;
  let dragged = null;

  list.addEventListener('dragstart', (e) => { dragged = e.target.closest('li'); });

  list.addEventListener('dragover', (e) => {
    e.preventDefault();
    const target = e.target.closest('li');
    if (!target || target === dragged) return;
    const rect = target.getBoundingClientRect();
    const after = (e.clientY - rect.top) / rect.height > 0.5;
    list.insertBefore(dragged, after ? target.nextSibling : target);
  });

  list.addEventListener('drop', () => {
    const ids = Array.from(list.children).map((li) => li.dataset.id);
    const params = new URLSearchParams();
    ids.forEach((id) => params.append('order[]', id));
    params.append('_csrf_token', '<?= View::e(Csrf::token()) ?>');

    fetch('<?= View::url('admin/pages/' . $page['id'] . '/sections/reorder') ?>', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
      body: params.toString(),
    });
  });
})();
</script>
