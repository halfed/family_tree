<?php
if($modx->user->get('username') == '(anonymous)') {
    $url = $modx->makeUrl(7);
    $modx->sendRedirect($url);
}
