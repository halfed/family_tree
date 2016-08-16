<?php
$user = $modx->getUser();
if (!$user) return '';
$profile = $user->getOne('Profile');
if (!$profile) return '';
$extendedLoginCount = $profile->get('logincount');

if ($extendedLoginCount <= 1) {
    $redirectUser = $modx->makeUrl(3);
    $modx->sendRedirect($redirectUser);
    //echo "url is ".$urlCheck;
}
