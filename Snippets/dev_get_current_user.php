<?php
if($modx->user->get('id') > 0) {
    if($modx->user->get('id') == 1) {
        //ADMINS CAN ENTER PEOPLE MANUALLY
        $getUserCredentials['fi.getUserIdForProfile'] = '<a href="[[~2]]" title="memberProfile">Add/Edit A Member</a>';
    }
    else {
        $getUserCredentials = array();
        $getUserCredentials['fi.getUserIdForKids'] = '<li class="name"><h1><a href="[[~11]]" title="Children">Children</a></h1></li>';
        $getUserCredentials['fi.getUserIdForProfile'] = '<li class="name"><h1><a href="[[~3]]" title="member Profile">Member Profile</a></h1></li>';
         
    }
}
else {
    $getUserCredentials['fi.userId'] = '';
}

$modx->setPlaceholders($getUserCredentials);
