<?php

use App\Core\View;

/**
 * Shared image-browser modal, included once in admin.layouts.app so every
 * admin screen with an image field can open it. Call window.openMediaPicker
 * (see below) with a callback that receives the chosen file's public path.
 */
?>
<div id="media-picker-backdrop" class="hidden fixed inset-0 z-[100] bg-slate-900/50 flex items-center justify-center p-6">
  <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl max-h-[80vh] flex flex-col">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
      <h3 class="font-semibold text-slate-900">Choose an image</h3>
      <div class="flex items-center gap-3">
        <a href="<?= View::url('admin/media') ?>" target="_blank" class="text-xs text-brand-600 hover:underline">Upload new in Media Manager &#8599;</a>
        <button type="button" id="media-picker-close" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
      </div>
    </div>
    <div class="px-5 py-3 border-b border-slate-100">
      <input type="text" id="media-picker-search" placeholder="Search by filename..."
             class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-400">
    </div>
    <div id="media-picker-grid" class="flex-1 overflow-y-auto p-5 grid grid-cols-4 sm:grid-cols-5 gap-3">
    </div>
  </div>
</div>

<script>
(function () {
  const backdrop = document.getElementById('media-picker-backdrop');
  const grid = document.getElementById('media-picker-grid');
  const search = document.getElementById('media-picker-search');
  const closeBtn = document.getElementById('media-picker-close');
  let onPick = null;
  let debounceTimer = null;

  function render(items) {
    grid.innerHTML = '';

    if (items.length === 0) {
      grid.innerHTML = '<p class="col-span-full text-sm text-slate-400 text-center py-8">No images found.</p>';
      return;
    }

    items.forEach((item) => {
      const thumb = item.webp_path || item.path;
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'group relative aspect-square rounded-lg border border-slate-200 overflow-hidden hover:border-brand-400 hover:ring-2 hover:ring-brand-200 transition';
      btn.title = item.original_name;
      btn.innerHTML = '<img src="' + thumb + '" alt="' + (item.alt_text || '') + '" class="w-full h-full object-cover">'
        + '<span class="absolute inset-x-0 bottom-0 bg-slate-900/70 text-white text-[10px] px-1 py-0.5 truncate opacity-0 group-hover:opacity-100 transition">' + item.original_name + '</span>';
      btn.addEventListener('click', () => {
        if (onPick) onPick(item.path);
        close();
      });
      grid.appendChild(btn);
    });
  }

  function load(query) {
    grid.innerHTML = '<p class="col-span-full text-sm text-slate-400 text-center py-8">Loading...</p>';

    fetch('<?= View::url('admin/media-picker') ?>?q=' + encodeURIComponent(query || ''), {
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
    })
      .then((res) => res.json())
      .then((data) => render(data.media || []))
      .catch(() => { grid.innerHTML = '<p class="col-span-full text-sm text-red-500 text-center py-8">Failed to load images.</p>'; });
  }

  function close() {
    backdrop.classList.add('hidden');
    onPick = null;
  }

  window.openMediaPicker = function (callback) {
    onPick = callback;
    search.value = '';
    backdrop.classList.remove('hidden');
    load('');
  };

  closeBtn.addEventListener('click', close);
  backdrop.addEventListener('click', (e) => { if (e.target === backdrop) close(); });

  search.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => load(search.value), 250);
  });
})();
</script>
