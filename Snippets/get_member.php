<?php
//INCLUDE CONNECTION TO DB
include 'assets/scripts/db_connection.php';

// please sanitise your POST values, this is just an example
$placeholders = array();

if($modx->user->get('id') > 0) {
    if($modx->user->get('id') > 1) {
        $uMemberID = $modx->user->get('id');
        $sql = "SELECT member_id, first_name, middle_name, last_name, suffix, nick_name, gender, dob, home_city, home_state, member_generation, member_image FROM desloge_family_member WHERE family_id = '".$uMemberID."'";
        $result = $dbh->query($sql);
    
    if ($result->rowCount()) {
        // output data of each row
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            
            $memberId = $row["member_id"];
            $placeholders['fi.firstName'] = $row["first_name"];
            $placeholders['fi.middleName'] = $row["middle_name"];
            $placeholders['fi.lastName'] = $row["last_name"];
            $placeholders['fi.suffix'] = $row["suffix"];
            $placeholders['fi.nickName'] = $row["nick_name"];
            $placeholders['fi.gender'] = $row["gender"];
            $placeholders['fi.dob'] = $row["dob"];
            $keywordsDate = preg_split('/[\-]+/', $row["dob"]);
            $placeholders['fi.dobYear'] = $keywordsDate[0];
            $placeholders['fi.dobMonth'] = $keywordsDate[1];
            $placeholders['fi.dobDay'] = $keywordsDate[2];
            $placeholders['fi.birthCity'] = $row["home_city"];
            $placeholders['fi.birthState'] = $row["home_state"];
            $placeholders['fi.memberGeneration'] = $row["member_generation"];
            if($row["member_image"] != "") {
                $placeholders['fi.memberPhoto'] = $modx->getOption('site_url'). "assets/images/profile/". $row["member_image"];
                $placeholders['fi.memberImage'] = $row["member_image"];
            }
        }
    }
    
    $sql2 = "SELECT email, phone, current_city, current_state, college, occupation, address, facebook, linkedin, website, about_me FROM desloge_family_member_info WHERE member_id = '".$memberId."'";
    $result2 = $dbh->query($sql2);
    if ($result2->rowCount()) {
        // output data of each row
        while($row = $result2->fetch(PDO::FETCH_ASSOC)) {
            $placeholders['fi.email'] = $row["email"];
            $placeholders['fi.phone'] = $row["phone"];
            $placeholders['fi.currentCity'] = $row["current_city"];
            $placeholders['fi.currentState'] = $row["current_state"];
            $placeholders['fi.college'] = $row["college"];
            $placeholders['fi.occupation'] = $row["occupation"];
            $placeholders['fi.address'] = $row["address"];
            $placeholders['fi.faceBook'] = $row["facebook"];
            $placeholders['fi.linkedIn'] = $row["linkedin"];
            $placeholders['fi.website'] = $row["website"];
            $placeholders['fi.aboutMe'] = $row["about_me"];
        }
    }
    
    //GET MEMBER PARENT INFO
            $sql_membersParentId = "SELECT parent_id FROM desloge_family_siblings WHERE member_id = '".$memberId."'";
            $result_membersParentId = $dbh->query($sql_membersParentId);
            if ($result_membersParentId->rowCount()) {
                 while($row_membersParentId = $result_membersParentId->fetch(PDO::FETCH_ASSOC)) {
                    $memberParentId = $row_membersParentId["parent_id"];
                 }
                 //GET MEMBERS PARENT INFO
                $sql_membersParentInfo = "SELECT T2.first_name, T2.last_name, T2.dob FROM desloge_member_spouse AS T1, desloge_family_member AS T2 WHERE T2.member_id = T1.spouse1_id AND T1.marriage_id = '".$memberParentId."'";
                $result_membersParentInfo = $dbh->query($sql_membersParentInfo);
                if ($result_membersParentInfo->rowCount()) {
                    while($row_membersParentInfo = $result_membersParentInfo->fetch(PDO::FETCH_ASSOC)) {
                        $placeholders['fi.parentFName'] = $row_membersParentInfo["first_name"];
                        $placeholders['fi.parentLName'] = $row_membersParentInfo["last_name"];
                        $placeholders['fi.parentDob'] = $row_membersParentInfo["dob"];
                        $keywordsDateParent = preg_split('/[\-]+/', $row_membersParentInfo["dob"]);
                        $placeholders['fi.parentDobYear'] = $keywordsDateParent[0];
                        $placeholders['fi.parentDobMonth'] = $keywordsDateParent[1];
                        $placeholders['fi.parentDobDay'] = $keywordsDateParent[2];
                    }
                }
            }
    
            $sql5 = "SELECT generation_id, family_id FROM desloge_family WHERE member_id = '".$memberId."'";
            $result5 = $dbh->query($sql5);
            if ($result5->rowCount()) {
                // output data of each row
                while($row = $result5->fetch(PDO::FETCH_ASSOC)) {
                    if($row["family_id"] == 1){
                        //$placeholders['fi.bornIntoFamily'] = 'checked';
                        $placeholders['fi.bornIntoFamily'] = $row["family_id"];
                    }
                }
            }
    
            //GET MEMBER SPOUSE INFO FROM SPOUSE1
            $sql_memberSpouseMarriageId = "SELECT marriage_id FROM desloge_member_spouse WHERE spouse1_id = '".$memberId."'";
            $result_memberSpouseMarriageId = $dbh->query($sql_memberSpouseMarriageId);
            if ($result_memberSpouseMarriageId->rowCount()) {
                 while($row_memberSpouseMarriageId = $result_memberSpouseMarriageId->fetch(PDO::FETCH_ASSOC)) {
                    $spouseMarriageId = $row_memberSpouseMarriageId["marriage_id"];
                 }
                //GET MEMBER SPOUSE INFO
                $sql_membersSpouceMemberId = "SELECT spouse2_id FROM desloge_member_spouse WHERE marriage_id = '".$spouseMarriageId."'";
                $result_membersSpouceMemberId = $dbh->query($sql_membersSpouceMemberId);
                if ($result_membersSpouceMemberId->rowCount()) {
                    while($row_membersSpouceMemberId = $result_membersSpouceMemberId->fetch(PDO::FETCH_ASSOC)) {
                        $membersSpouceMemberId = $row_membersSpouceMemberId["spouse2_id"];
                    }
                    if ($membersSpouceMemberId != NULL || $membersSpouceMemberId != "") {
                        //GET SPOUSE INFO
                        $placeholders['fi.haveSpouse'] = 'checked';
                        $sql_membersSpouceMemberInfo = "SELECT first_name, last_name, dob FROM desloge_family_member WHERE member_id = '".$membersSpouceMemberId."'";
                        $result_membersSpouceMemberInfo = $dbh->query($sql_membersSpouceMemberInfo);
                        while($row_membersSpouceMemberInfo = $result_membersSpouceMemberInfo->fetch(PDO::FETCH_ASSOC)) {
                            $placeholders['fi.spouseFName'] = $row_membersSpouceMemberInfo["first_name"];
                            $placeholders['fi.spouseLName'] = $row_membersSpouceMemberInfo["last_name"];
                            $placeholders['fi.spouseDob'] = $row_membersSpouceMemberInfo["dob"];
                            $keywordsDateSpouse = preg_split('/[\-]+/', $row_membersSpouceMemberInfo["dob"]);
                            $placeholders['fi.spouseDobYear'] = $keywordsDateSpouse[0];
                            $placeholders['fi.spouseDobMonth'] = $keywordsDateSpouse[1];
                            $placeholders['fi.spouseDobDay'] = $keywordsDateSpouse[2];
                        }
                    }
                }
            }else {
                 //GET MEMBER SPOUSE INFO FROM SPOUSE2
                 $sql_memberSpouseMarriageId = "SELECT marriage_id FROM desloge_member_spouse WHERE spouse2_id = '".$memberId."'";
                 $result_memberSpouseMarriageId = $dbh->query($sql_memberSpouseMarriageId);
                 if ($result_memberSpouseMarriageId->rowCount()) {
                     while($row_memberSpouseMarriageId = $result_memberSpouseMarriageId->fetch(PDO::FETCH_ASSOC)) {
                        $spouseMarriageId = $row_memberSpouseMarriageId["marriage_id"];
                     }
                    //GET MEMBER SPOUSE INFO
                    $sql_membersSpouceMemberId = "SELECT spouse1_id FROM desloge_member_spouse WHERE marriage_id = '".$spouseMarriageId."'";
                    $result_membersSpouceMemberId = $dbh->query($sql_membersSpouceMemberId);
                    if ($result_membersSpouceMemberId->rowCount()) {
                        while($row_membersSpouceMemberId = $result_membersSpouceMemberId->fetch(PDO::FETCH_ASSOC)) {
                            $membersSpouceMemberId = $row_membersSpouceMemberId["spouse1_id"];
                        }
                        if ($membersSpouceMemberId != NULL || $membersSpouceMemberId != "") {
                            //GET SPOUSE INFO
                            $placeholders['fi.haveSpouse'] = 'checked';
                            $sql_membersSpouceMemberInfo = "SELECT first_name, last_name, dob FROM desloge_family_member WHERE member_id = '".$membersSpouceMemberId."'";
                            $result_membersSpouceMemberInfo = $dbh->query($sql_membersSpouceMemberInfo);
                            while($row_membersSpouceMemberInfo = $result_membersSpouceMemberInfo->fetch(PDO::FETCH_ASSOC)) {
                                $placeholders['fi.spouseFName'] = $row_membersSpouceMemberInfo["first_name"];
                                $placeholders['fi.spouseLName'] = $row_membersSpouceMemberInfo["last_name"];
                                $placeholders['fi.spouseDob'] = $row_membersSpouceMemberInfo["dob"];
                                $keywordsDateSpouse = preg_split('/[\-]+/', $row_membersSpouceMemberInfo["dob"]);
                                $placeholders['fi.spouseDobYear'] = $keywordsDateSpouse[0];
                                $placeholders['fi.spouseDobMonth'] = $keywordsDateSpouse[1];
                                $placeholders['fi.spouseDobDay'] = $keywordsDateSpouse[2];
                            }
                        }
                    }
                 }
            }
    
    
    //DON'T NEED THESE BUT WILL LEAVE THEM HERE FOR NOW
    /*$sql6 = "SELECT spouse_id FROM desloge_member_spouse WHERE member_id = '".$uMemberID."'";
    $result6 = $dbh->query($sql6);
    
    $sql7 = "SELECT member_id, first_name, last_name FROM desloge_family_member WHERE member_id = '".$row_["spouse_id"]."'";
    $result7 = $dbh->query($sql7);*/
    
    $placeholders['fi.uId'] = $uMemberID;
    }
}
$modx->setPlaceholders($placeholders);
