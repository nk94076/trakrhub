<?php

use App\Core\Theme;
use App\Core\View;
?>
<link rel="stylesheet" href="<?= View::e(View::asset('css/app.css')) ?>">
<style><?= Theme::cssVariables() ?></style>
