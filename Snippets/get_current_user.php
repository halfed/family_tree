<?php
if($modx->user->get('id') > 0) {
    if($modx->user->get('id') == 1) {
        //ADMINS CAN ENTER PEOPLE MANUALLY
        $getUserCredentials['fi.getUserIdForProfile'] = '<a href="[[~2]]" title="memberProfile">Add/Edit A Member</a>';
    }
    else {
        $getUserCredentials = array();
        $getUserCredentials['fi.getUserIdForKids'] = '<a href="[[~11]]" title="Children">Children</a>';
        $getUserCredentials['fi.getUserIdForProfile'] = '<a href="[[~3]]" title="member Profile">Member Profile</a>';
         
    }
}
else {
    $getUserCredentials['fi.userId'] = '';
}

$modx->setPlaceholders($getUserCredentials);
