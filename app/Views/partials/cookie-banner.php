<?php

use App\Core\View;
use App\Models\Setting;

$enabled = Setting::get('cookie_banner', 'enabled', 'true') === 'true';

if (!$enabled) {
    return;
}

$message = Setting::get('cookie_banner', 'message', 'We use cookies to run this site and understand how it is used.');
$acceptText = Setting::get('cookie_banner', 'accept_text', 'Accept');
$learnMoreText = Setting::get('cookie_banner', 'learn_more_text', 'Cookie Policy');
?>
<div id="cookie-banner" class="hidden fixed inset-x-0 bottom-0 z-50 p-4">
  <div class="max-w-3xl mx-auto rounded-2xl bg-slate-950 text-slate-200 shadow-2xl p-5 flex flex-col sm:flex-row items-center gap-4">
    <p class="text-sm flex-1 text-center sm:text-left">
      <?= View::e($message) ?>
      <a href="<?= View::url('cookie-policy') ?>" class="underline hover:text-white"><?= View::e($learnMoreText) ?></a>
    </p>
    <button type="button" id="cookie-banner-accept"
            class="flex-shrink-0 rounded-full bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold px-6 py-2.5 whitespace-nowrap">
      <?= View::e($acceptText) ?>
    </button>
  </div>
</div>
<script>
(function () {
  var KEY = 'techslay_cookie_consent';
  var banner = document.getElementById('cookie-banner');
  if (!banner) return;
  if (!localStorage.getItem(KEY)) {
    banner.classList.remove('hidden');
  }
  document.getElementById('cookie-banner-accept').addEventListener('click', function () {
    localStorage.setItem(KEY, '1');
    banner.classList.add('hidden');
  });
})();
</script>
