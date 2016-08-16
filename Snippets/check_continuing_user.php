<?php
if($modx->user->get('username') != '(anonymous)') {
    $url = $modx->makeUrl(1);
    $modx->sendRedirect($url);
}
