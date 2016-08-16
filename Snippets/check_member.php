<?php
if($modx->user->get('id') > 0) {
    $profile = $modx->user->getOne('Profile');
    $ext = $profile->get('extended');
    $usrDob = $ext['dobYear']."-".$ext['dobMonth']."-".$ext['dobDay'];
    $memberName = preg_split('/[\" "]+/', $profile->get('fullname'));
    $extendedLoginCount = $profile->get('logincount');
    $extendedEmail = $profile->get('email');
    $extendedLoginID = $profile->get('id');

    if($modx->user->get('id') > 1) {
        //INCLUDE CONNECTION TO DB
        include 'assets/scripts/db_connection.php';
    
        $sqlCheckUser = "SELECT first_name, member_id FROM desloge_family_member WHERE family_id =  '".$modx->user->get('id')."'";

        $resultCheckUser = $dbh->query($sqlCheckUser);

        if (!$resultCheckUser->rowCount()) {
            
            $sqlCheckUserName = "SELECT member_id FROM desloge_family_member WHERE first_name = '".$memberName[0]."' AND last_name = '".$memberName[1]."' AND dob = '".$usrDob."'";
    
            $resultCheckUserName = $dbh->query($sqlCheckUserName);
            if($resultCheckUserName->rowCount()) {
                while($row = $resultCheckUserName->fetch(PDO::FETCH_ASSOC)) {
                    $memberID = $row["member_id"];
                }
                //MEMBER HAS INFO IN DB FROM ADMIN BUT HAS NEVER LOGGED IN TIE MODX ID TO MEMBER ID IN DESLOGE FAMILY TABLE, IN CASES WHERE ADMIN HAS DELETED USER ACCOUNT IN MODX
                //AND USER GOES IN AND CREATES NEW ACCOUNT, MODX ID = FAMILY ID HAS TO BE UPDATED
                $insertMember = "UPDATE desloge_family_member SET family_id = ".$modx->user->get('id')." WHERE member_id = ".$memberID;
                $stmt = $dbh->query($insertMember);
                
                $insertMemberEmail = "UPDATE desloge_family_member_info SET email = ".$extendedEmail." WHERE member_id = ".$memberID;
                $stmtEmail = $dbh->query($insertMemberEmail);
    
                
            }else {
                //USER IS BRAND NEW AND HAS NO INFO IN DESLOGE FAMILY TABLES, WE NEED TO INITIALLY POPULATE ROWS FOR THIS PERSON AND GIVE THEM A MODX ID
                $insertMember = "INSERT INTO desloge_family_member (family_id, first_name, last_name, dob) VALUES ('".$modx->user->get('id')."', '".$memberName[0]."', '".$memberName[1]."', '$usrDob')";
                $stmt = $dbh->query($insertMember);
                //Get last inserted member id
                $newMemberId = $dbh->lastInsertId();
                
                 //Insert into member info table with id from previous insert
                 $sql2 = "INSERT INTO desloge_family_member_info (member_id, email) VALUES ('$newMemberId', '$extendedEmail')";
                
                 $stmt = $dbh->query($sql2);
            
                 //Insert member Id into member_family
                 $sql3 = "INSERT INTO desloge_family (member_id) VALUES ('$newMemberId')";
                 $stmt = $dbh->query($sql3);
                
                 //At some point a member might have siblings
                 $sql4 = "INSERT INTO desloge_family_siblings (member_id) VALUES ('$newMemberId')";
                 $stmt = $dbh->query($sql4);
                
                //AT THIS POINT WE WILL NOT HAVE TO INSERT MEMBER INTO SPOUSE TABLE, WE WILL FIGURE THAT OUT WHEN THEY UPDATE THERE MEMBER PROFILE
    
                //AS WELL, WE WILL FIGURE OUT IF THEY HAVE KIDS OR NOT AND POPULATE THE CHILDREN TABLE WHEN THEY UPDATE MEMBER PROFILE AND GO TO KIDS LINK
           }    
        }
        
        //IF A USER REGISTERS FOR THE FIRST TIME WE WANT TO HAVE THEM COMPLETE THE MEMBER PROFILE BEFORE THEY USE THE FAMILY TREE APP
        if ($extendedLoginCount <= 1) {
            $redirectUser = $modx->makeUrl(3);
            $modx->sendRedirect($redirectUser);
            //echo "url is ".$urlCheck;
        }
        
    }
    
}
else {
    //SEND USER TO LOGIN PAGE AFTER LOGOUT
    $redirectUser = $modx->makeUrl(7);
    $modx->sendRedirect($redirectUser);
}
