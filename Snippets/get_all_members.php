<?php
if(isset($_COOKIE['dfCookie']) || $modx->user->get('id') == 1) {

//INCLUDE CONNECTION TO DB
include 'assets/scripts/db_connection.php';
//INCLUDE LIBRARY
include 'assets/scripts/Library.php';

$memberInfoPlaceholderStr = array();
$memberInfoPlaceholder = array();
$childInfoPlaceholder = array();
$parentInfoPlaceholder = array();
$arrayTotal = array();

if($_GET['fId'] != "") {
    $resultParentInfo = getParentInfo($dbh, $_GET['mId'], $_GET['sId']);
    if ($resultParentInfo->rowCount()) {
        $parentCount = 0;
        while($parentRow = $resultParentInfo->fetch(PDO::FETCH_ASSOC)) {
            $parentInfoPlaceholder[$parentCount]['memberId'] = $parentRow["member_id"];
            $parentInfoPlaceholder[$parentCount]['firstName'] = $parentRow["first_name"];
            $parentInfoPlaceholder[$parentCount]['middleName'] = $parentRow["middle_name"];
            $parentInfoPlaceholder[$parentCount]['lastName'] = $parentRow["last_name"];
            $parentInfoPlaceholder[$parentCount]['suffix'] = $parentRow["suffix"];
            $parentInfoPlaceholder[$parentCount]['gender'] = $parentRow["gender"];
            $parentInfoPlaceholder[$parentCount]['nickName'] = $parentRow["nick_name"];
            $parentInfoPlaceholder[$parentCount]['dob'] = $parentRow["age"];
            $parentInfoPlaceholder[$parentCount]['dod'] = $parentRow["dod"];
            $parentInfoPlaceholder[$parentCount]['birthCity'] = $parentRow["home_city"];
            $parentInfoPlaceholder[$parentCount]['birthState'] = $parentRow["home_state"];
            $parentInfoPlaceholder[$parentCount]['memberGeneration'] = $parentRow["member_generation"];
            if($parentRow["member_image"] == "") {
                $parentInfoPlaceholder[$parentCount]['memberImage'] = "image-placeholder_large.png";
            }else {
                $parentInfoPlaceholder[$parentCount]['memberImage'] = $parentRow["member_image"];
            }
            
            $parentInfoPlaceholder[$parentCount]['email'] = $parentRow["email"];
            $parentInfoPlaceholder[$parentCount]['phone'] = $parentRow["phone"];
            $parentInfoPlaceholder[$parentCount]['currentCity'] = $parentRow["current_city"];
            $parentInfoPlaceholder[$parentCount]['currentState'] = $parentRow["current_state"];
            $parentInfoPlaceholder[$parentCount]['college'] = $parentRow["college"];
            $parentInfoPlaceholder[$parentCount]['occupation'] = $parentRow["occupation"];
            $parentInfoPlaceholder[$parentCount]['address'] = $parentRow["address"];
            $parentInfoPlaceholder[$parentCount]['facebook'] = $parentRow["facebook"];
            $parentInfoPlaceholder[$parentCount]['linkedin'] = $parentRow["linkedin"];
            $parentInfoPlaceholder[$parentCount]['website'] = $parentRow["website"];
            $parentInfoPlaceholder[$parentCount]['aboutMe'] = $parentRow["about_me"];
            if($parentRow["family_id"] == 1) {
               $parentInfoPlaceholder[$parentCount]['familyStatus'] = "born-to-family"; 
            }
            else {
                $parentInfoPlaceholder[$parentCount]['familyStatus'] = "married";
            }
            $parentCount++;
        }
    }
    
    //OUTPUT EACH OF PARENT'S CHILDREN DATA
    $resultChildrenInfo = getChildrenInfo($dbh, $_GET['fId']);
    if ($resultChildrenInfo->rowCount()) {
        $childrenCount = 0;
        //childInfoPlaceholder REPRESENTS EACH CHILD AND THAT CHILD'S SPOUSE IF THEY HAVE ONE
        while($childRow = $resultChildrenInfo->fetch(PDO::FETCH_ASSOC)) {
            $childInfoPlaceholder[$childrenCount]['memberId'] = $childRow["member_id"];
            $childInfoPlaceholder[$childrenCount]['firstName'] = $childRow["first_name"];
            $childInfoPlaceholder[$childrenCount]['middleName'] = $childRow["middle_name"];
            $childInfoPlaceholder[$childrenCount]['lastName'] = $childRow["last_name"];
            $childInfoPlaceholder[$childrenCount]['suffix'] = $childRow["suffix"];
            $childInfoPlaceholder[$childrenCount]['nickName'] = $childRow["nick_name"];
            $childInfoPlaceholder[$childrenCount]['gender'] = $childRow["gender"];
            $childInfoPlaceholder[$childrenCount]['dob'] = $childRow["age"];
            $childInfoPlaceholder[$childrenCount]['dod'] = $childRow["dod"];
            $childInfoPlaceholder[$childrenCount]['birthCity'] = $childRow["home_city"];
            $childInfoPlaceholder[$childrenCount]['birthState'] = $childRow["home_state"];
            $childInfoPlaceholder[$childrenCount]['memberGeneration'] = $childRow["member_generation"];
            if($childRow["member_image"] == "") {
                $childInfoPlaceholder[$childrenCount]['memberImage'] = "image-placeholder_large.png";
            }else {
                $childInfoPlaceholder[$childrenCount]['memberImage'] = $childRow["member_image"];
            }
            
            $childInfoPlaceholder[$childrenCount]['email'] = $childRow["email"];
            $childInfoPlaceholder[$childrenCount]['phone'] = $childRow["phone"];
            $childInfoPlaceholder[$childrenCount]['currentCity'] = $childRow["current_city"];
            $childInfoPlaceholder[$childrenCount]['currentState'] = $childRow["current_state"];
            $childInfoPlaceholder[$childrenCount]['college'] = $childRow["college"];
            $childInfoPlaceholder[$childrenCount]['occupation'] = $childRow["occupation"];
            $childInfoPlaceholder[$childrenCount]['address'] = $childRow["address"];
            $childInfoPlaceholder[$childrenCount]['facebook'] = $childRow["facebook"];
            $childInfoPlaceholder[$childrenCount]['linkedin'] = $childRow["linkedin"];
            $childInfoPlaceholder[$childrenCount]['website'] = $childRow["website"];
            $childInfoPlaceholder[$childrenCount]['aboutMe'] = $childRow["about_me"];
            if($childRow["family_id"] == 1) {
               $childInfoPlaceholder[$childrenCount]['familyChildStatus'] = "born-to-family"; 
            }
            else {
                $childInfoPlaceholder[$childrenCount]['familyChildStatus'] = "married";
            }
            
            //IF MARRIED, GET CHILD MEMBER SPOUSE INFO FROM SPOUSE1
            $result_checkSpouseMarriageId = getMrgIdFrmSpouse1($dbh, $childRow['member_id']);
            if ($result_checkSpouseMarriageId->rowCount()) {
                
                while($row_memberSpouseMarriageId = $result_checkSpouseMarriageId->fetch(PDO::FETCH_ASSOC)) {
                    $spouseMarriageId = $row_memberSpouseMarriageId["marriage_id"];
                 }

                //GET MEMBER SPOUSE INFO
                $result_membersSpouceMemberId = getSpouseIdFromMemberSp2($dbh, $spouseMarriageId);

                if ($result_membersSpouceMemberId->rowCount()) {
                    while($row_membersSpouceMemberId = $result_membersSpouceMemberId->fetch(PDO::FETCH_ASSOC)) {
                        $membersSpouceMemberId = $row_membersSpouceMemberId["spouse2_id"];
                    }
                    //GET SPOUSE INFO
                    $result_membersSpouceMember = getSpouseIdInformation($dbh, $membersSpouceMemberId);
                    while($row_membersSpouceMember = $result_membersSpouceMember->fetch(PDO::FETCH_ASSOC)) {
                        $childInfoPlaceholder[$childrenCount]['spouseFName'] = $row_membersSpouceMember["first_name"];
                        $childInfoPlaceholder[$childrenCount]['spouseLName'] = $row_membersSpouceMember["last_name"];
                        $childInfoPlaceholder[$childrenCount]['spouseMName'] = $row_membersSpouceMember["middle_name"];
                        $childInfoPlaceholder[$childrenCount]['spouseSuffix'] = $row_membersSpouceMember["suffix"];
                        $childInfoPlaceholder[$childrenCount]['spouseNickName'] = $row_membersSpouceMember["nick_name"];
                        $childInfoPlaceholder[$childrenCount]['spouseGender'] = $row_membersSpouceMember["gender"];
                        $childInfoPlaceholder[$childrenCount]['spouseDob'] = $row_membersSpouceMember["age"];
                        $childInfoPlaceholder[$childrenCount]['spouseDod'] = $row_membersSpouceMember["dod"];
                        $childInfoPlaceholder[$childrenCount]['spouseHomeCity'] = $row_membersSpouceMember["home_city"];
                        $childInfoPlaceholder[$childrenCount]['spouseHomeState'] = $row_membersSpouceMember["home_state"];
                        $childInfoPlaceholder[$childrenCount]['spouseMemberGen'] = $row_membersSpouceMember["member_generation"];
                        $childInfoPlaceholder[$childrenCount]['spouseMemberId'] = $membersSpouceMemberId;
                        if($row_membersSpouceMember["member_image"] == "") {
                            $childInfoPlaceholder[$childrenCount]['spouseMemberImage'] = "image-placeholder_large.png";
                        }else {
                            $childInfoPlaceholder[$childrenCount]['spouseMemberImage'] = $row_membersSpouceMember["member_image"];
                        }
                        if($$row_membersSpouceMember["family_id"] == 1) {
                           $childInfoPlaceholder[$childrenCount]['familySpouseStatus'] = "born-to-family"; 
                        }
                        else {
                            $childInfoPlaceholder[$childrenCount]['familySpouseStatus'] = "married";
                        }
                    }

                    $result_membersSpouceMemberInfo = getSpouseSecondaryInfo($dbh, $membersSpouceMemberId);
                    while($row_membersSpouceMemberInfo = $result_membersSpouceMemberInfo->fetch(PDO::FETCH_ASSOC)) {
                        $childInfoPlaceholder[$childrenCount]['spouseEmail'] = $row_membersSpouceMemberInfo["email"];
                        $childInfoPlaceholder[$childrenCount]['spousePhone'] = $row_membersSpouceMemberInfo["phone"];
                        $childInfoPlaceholder[$childrenCount]['spouseCurrentCity'] = $row_membersSpouceMemberInfo["current_city"];
                        $childInfoPlaceholder[$childrenCount]['spouseCurrentState'] = $row_membersSpouceMemberInfo["current_state"];
                        $childInfoPlaceholder[$childrenCount]['spouseCollege'] = $row_membersSpouceMemberInfo["college"];
                        $childInfoPlaceholder[$childrenCount]['spouseOccupation'] = $row_membersSpouceMemberInfo["occupation"];
                        $childInfoPlaceholder[$childrenCount]['spouseAddress'] = $row_membersSpouceMemberInfo["address"];
                        $childInfoPlaceholder[$childrenCount]['spouseFacebook'] = $row_membersSpouceMemberInfo["facebook"];
                        $childInfoPlaceholder[$childrenCount]['spouseLinkedin'] = $row_membersSpouceMemberInfo["linkedin"];
                        $childInfoPlaceholder[$childrenCount]['spouseWebsite'] = $row_membersSpouceMemberInfo["website"];
                        $childInfoPlaceholder[$childrenCount]['spouseAboutMe'] = $row_membersSpouceMemberInfo["about_me"];
                    }
                    $childInfoPlaceholder[$childrenCount]['spouseMemberId'] = $membersSpouceMemberId;
                    $childInfoPlaceholder[$childrenCount]['familyId'] = $spouseMarriageId;
                }
            }else {

                //GET MEMBER SPOUSE INFO FROM SPOUSE2
                $result_checkSpouseMarriageId = getMrgIdFrmSpouse2($dbh, $childRow['member_id']);
                if ($result_checkSpouseMarriageId->rowCount()) {
                     while($row_memberSpouseMarriageId = $result_checkSpouseMarriageId->fetch(PDO::FETCH_ASSOC)) {
                        $spouseMarriageId = $row_memberSpouseMarriageId["marriage_id"];
                     }

                    //GET MEMBER SPOUSE INFO
                    $result_membersSpouceMemberId = getSpouseIdFromMemberSp1($dbh, $spouseMarriageId);
                    if ($result_membersSpouceMemberId->rowCount()) {
                        while($row_membersSpouceMemberId = $result_membersSpouceMemberId->fetch(PDO::FETCH_ASSOC)) {
                            $membersSpouceMemberId = $row_membersSpouceMemberId["spouse1_id"];
                        }

                        //GET SPOUSE INFO
                        $result_membersSpouceMember = getSpouseIdInformation($dbh, $membersSpouceMemberId);
                        while($row_membersSpouceMember = $result_membersSpouceMember->fetch(PDO::FETCH_ASSOC)) {
                            $childInfoPlaceholder[$childrenCount]['spouseFName'] = $row_membersSpouceMember["first_name"];
                            $childInfoPlaceholder[$childrenCount]['spouseLName'] = $row_membersSpouceMember["last_name"];
                            $childInfoPlaceholder[$childrenCount]['spouseMName'] = $row_membersSpouceMember["middle_name"];
                            $childInfoPlaceholder[$childrenCount]['spouseSuffix'] = $row_membersSpouceMember["suffix"];
                            $childInfoPlaceholder[$childrenCount]['spouseNickName'] = $row_membersSpouceMember["nick_name"];
                            $childInfoPlaceholder[$childrenCount]['spouseGender'] = $row_membersSpouceMember["gender"];
                            $childInfoPlaceholder[$childrenCount]['spouseDob'] = $row_membersSpouceMember["age"];
                            $childInfoPlaceholder[$childrenCount]['spouseDod'] = $row_membersSpouceMember["dod"];
                            $childInfoPlaceholder[$childrenCount]['spouseHomeCity'] = $row_membersSpouceMember["home_city"];
                            $childInfoPlaceholder[$childrenCount]['spouseHomeState'] = $row_membersSpouceMember["home_state"];
                            $childInfoPlaceholder[$childrenCount]['spouseMemberGen'] = $row_membersSpouceMember["member_generation"];
                            $childInfoPlaceholder[$childrenCount]['spouseMemberId'] = $membersSpouceMemberId;
                            if($row_membersSpouceMember["member_image"] == "") {
                                $childInfoPlaceholder[$childrenCount]['spouseMemberImage'] = "image-placeholder_large.png";
                            }else {
                                $childInfoPlaceholder[$childrenCount]['spouseMemberImage'] = $row_membersSpouceMember["member_image"];
                            }
                            if($row_membersSpouceMember["family_id"] == 1) {
                               $childInfoPlaceholder[$childrenCount]['familySpouseStatus'] = "born-to-family"; 
                            }
                            else {
                                $childInfoPlaceholder[$childrenCount]['familySpouseStatus'] = "married";
                            }
                        }

                        $result_membersSpouceMemberInfo = getSpouseSecondaryInfo($dbh, $membersSpouceMemberId);
                        while($row_membersSpouceMemberInfo = $result_membersSpouceMemberInfo->fetch(PDO::FETCH_ASSOC)) {
                            $childInfoPlaceholder[$childrenCount]['spouseEmail'] = $row_membersSpouceMemberInfo["email"];
                            $childInfoPlaceholder[$childrenCount]['spousePhone'] = $row_membersSpouceMemberInfo["phone"];
                            $childInfoPlaceholder[$childrenCount]['spouseCurrentCity'] = $row_membersSpouceMemberInfo["current_city"];
                            $childInfoPlaceholder[$childrenCount]['spouseCurrentState'] = $row_membersSpouceMemberInfo["current_state"];
                            $childInfoPlaceholder[$childrenCount]['spouseCollege'] = $row_membersSpouceMemberInfo["college"];
                            $childInfoPlaceholder[$childrenCount]['spouseOccupation'] = $row_membersSpouceMemberInfo["occupation"];
                            $childInfoPlaceholder[$childrenCount]['spouseAddress'] = $row_membersSpouceMemberInfo["address"];
                            $childInfoPlaceholder[$childrenCount]['spouseFacebook'] = $row_membersSpouceMemberInfo["facebook"];
                            $childInfoPlaceholder[$childrenCount]['spouseLinkedin'] = $row_membersSpouceMemberInfo["linkedin"];
                            $childInfoPlaceholder[$childrenCount]['spouseWebsite'] = $row_membersSpouceMemberInfo["website"];
                            $childInfoPlaceholder[$childrenCount]['spouseAboutMe'] = $row_membersSpouceMemberInfo["about_me"];
                        }
                        $childInfoPlaceholder[$childrenCount]['spouseMemberId'] = $membersSpouceMemberId;
                        $childInfoPlaceholder[$childrenCount]['familyId'] = $spouseMarriageId;
                    }
                }
            }

            //If children need to set a marker to display bottom arrow and link for this members's family
            $result_checkForChildren = getChildId($dbh, $spouseMarriageId);
            if ($result_checkForChildren->rowCount()) {
                $childInfoPlaceholder[$childrenCount]['hasChildren'] = 1;
                $childInfoPlaceholder[$childrenCount]['familyUrl'] = "[[~1? &mId=`".$childInfoPlaceholder[$childrenCount]['memberId']."` &sId=`".$childInfoPlaceholder[$childrenCount]['spouseMemberId']."` &fId=`".$childInfoPlaceholder[$childrenCount]['familyId']."`]]";
            }
            else {
                $childInfoPlaceholder[$childrenCount]['hasChildren'] = 0;
            }
            
            $childrenCount++;
        }
    }
}else {
    //WERE ON THE HOME PAGE SO GET ALL MEMBERS INFO FOR FIRST GENERATION
    $resultMemberInfo = getAllMembersFirstGen($dbh);
    if ($resultMemberInfo->rowCount()) {
        // output data of each row
        $spouseMarriageId = '';
        $memberCount = 0;
        while($row = $resultMemberInfo->fetch(PDO::FETCH_ASSOC)) {
            $memberInfoPlaceholder[$memberCount]['memberId'] = $row["member_id"];
            
            //GET MEMBER SPOUSE INFO FROM SPOUSE1
            $result_checkSpouseMarriageId = getMrgIdFrmSpouse1($dbh, $row['member_id']);
            $memberInfoPlaceholder[$memberCount]['spouseMemberId'] = null;
            
            if ($result_checkSpouseMarriageId->rowCount()) {
                while($row_memberSpouseMarriageId = $result_checkSpouseMarriageId->fetch(PDO::FETCH_ASSOC)) {
                    $spouseMarriageId = $row_memberSpouseMarriageId["marriage_id"];
                 }
                 //GET MEMBER SPOUSE INFO
                $result_membersSpouceMemberId = getSpouseIdFromMemberSp2($dbh, $spouseMarriageId);
                if ($result_membersSpouceMemberId->rowCount()) {
                    while($row_membersSpouceMemberId = $result_membersSpouceMemberId->fetch(PDO::FETCH_ASSOC)) {
                        $membersSpouceMemberId = $row_membersSpouceMemberId["spouse2_id"];
                    }

                        //GET SPOUSE INFO
                        $result_membersSpouceMember = getSpouseIdInformation($dbh, $membersSpouceMemberId);
                        while($row_membersSpouceMember = $result_membersSpouceMember->fetch(PDO::FETCH_ASSOC)) {
                            $memberInfoPlaceholder[$memberCount]['spouseFName'] = $row_membersSpouceMember["first_name"];
                            $memberInfoPlaceholder[$memberCount]['spouseLName'] = $row_membersSpouceMember["last_name"];
                            $memberInfoPlaceholder[$memberCount]['spouseMName'] = $row_membersSpouceMember["middle_name"];
                            $memberInfoPlaceholder[$memberCount]['spouseSuffix'] = $row_membersSpouceMember["suffix"];
                            $memberInfoPlaceholder[$memberCount]['spouseNickName'] = $row_membersSpouceMember["nick_name"];
                            $memberInfoPlaceholder[$memberCount]['spouseGender'] = $row_membersSpouceMember["gender"];
                            $memberInfoPlaceholder[$memberCount]['spouseDob'] = $row_membersSpouceMember["age"];
                            $memberInfoPlaceholder[$memberCount]['spouseDod'] = $row_membersSpouceMember["dod"];
                            $memberInfoPlaceholder[$memberCount]['spouseHomeCity'] = $row_membersSpouceMember["home_city"];
                            $memberInfoPlaceholder[$memberCount]['spouseHomeState'] = $row_membersSpouceMember["home_state"];
                            $memberInfoPlaceholder[$memberCount]['spouseMemberGen'] = $row_membersSpouceMember["member_generation"];
                            $memberInfoPlaceholder[$memberCount]['spouseMemberId'] = $membersSpouceMemberId;
                            if($row_membersSpouceMember["member_image"] == "") {
                                $memberInfoPlaceholder[$memberCount]['spouseMemberImage'] = "image-placeholder_large.png";
                            }else {
                                $memberInfoPlaceholder[$memberCount]['spouseMemberImage'] = $row_membersSpouceMember["member_image"];
                            }
                            if($row_membersSpouceMember["family_id"] == 1) {
                               $memberInfoPlaceholder[$memberCount]['familySpouseStatus'] = "born-to-family"; 
                            }
                            else {
                                $memberInfoPlaceholder[$memberCount]['familySpouseStatus'] = "married";
                            }
                        }

                        $result_membersSpouceMemberInfo = getSpouseSecondaryInfo($dbh, $membersSpouceMemberId);
                        while($row_membersSpouceMemberInfo = $result_membersSpouceMemberInfo->fetch(PDO::FETCH_ASSOC)) {
                            $memberInfoPlaceholder[$memberCount]['spouseEmail'] = $row_membersSpouceMemberInfo["email"];
                            $memberInfoPlaceholder[$memberCount]['spousePhone'] = $row_membersSpouceMemberInfo["phone"];
                            $memberInfoPlaceholder[$memberCount]['spouseCurrentCity'] = $row_membersSpouceMemberInfo["current_city"];
                            $memberInfoPlaceholder[$memberCount]['spouseCurrentState'] = $row_membersSpouceMemberInfo["current_state"];
                            $memberInfoPlaceholder[$memberCount]['spouseCollege'] = $row_membersSpouceMemberInfo["college"];
                            $memberInfoPlaceholder[$memberCount]['spouseOccupation'] = $row_membersSpouceMemberInfo["occupation"];
                            $memberInfoPlaceholder[$memberCount]['spouseAddress'] = $row_membersSpouceMemberInfo["address"];
                            $memberInfoPlaceholder[$memberCount]['spouseFacebook'] = $row_membersSpouceMemberInfo["facebook"];
                            $memberInfoPlaceholder[$memberCount]['spouseLinkedin'] = $row_membersSpouceMemberInfo["linkedin"];
                            $memberInfoPlaceholder[$memberCount]['spouseWebsite'] = $row_membersSpouceMemberInfo["website"];
                            $memberInfoPlaceholder[$memberCount]['spouseAboutMe'] = $row_membersSpouceMemberInfo["about_me"];
                        }
                }
            }else {
                 //GET MEMBER SPOUSE INFO FROM SPOUSE2
                 $result_checkSpouseMarriageId = getMrgIdFrmSpouse2($dbh, $row['member_id']);
                 if ($result_checkSpouseMarriageId->rowCount()) {
                     while($row_memberSpouseMarriageId = $result_checkSpouseMarriageId->fetch(PDO::FETCH_ASSOC)) {
                        $spouseMarriageId = $row_memberSpouseMarriageId["marriage_id"];
                     }
                    //GET MEMBER SPOUSE INFO
                    $result_membersSpouceMemberId = getSpouseIdFromMemberSp1($dbh, $spouseMarriageId);
                    if ($result_membersSpouceMemberId->rowCount()) {
                        while($row_membersSpouceMemberId = $result_membersSpouceMemberId->fetch(PDO::FETCH_ASSOC)) {
                            $membersSpouceMemberId = $row_membersSpouceMemberId["spouse1_id"];
                        }
                        if ($result_membersSpouceMemberId->rowCount()) {

                            //GET SPOUSE INFO
                            $result_membersSpouceMember = getSpouseIdInformation($dbh, $membersSpouceMemberId);
                            while($row_membersSpouceMember = $result_membersSpouceMember->fetch(PDO::FETCH_ASSOC)) {
                                $memberInfoPlaceholder[$memberCount]['spouseFName'] = $row_membersSpouceMember["first_name"];
                                $memberInfoPlaceholder[$memberCount]['spouseLName'] = $row_membersSpouceMember["last_name"];
                                $memberInfoPlaceholder[$memberCount]['spouseMName'] = $row_membersSpouceMember["middle_name"];
                                $memberInfoPlaceholder[$memberCount]['spouseSuffix'] = $row_membersSpouceMember["suffix"];
                                $memberInfoPlaceholder[$memberCount]['spouseNickName'] = $row_membersSpouceMember["nick_name"];
                                $memberInfoPlaceholder[$memberCount]['spouseGender'] = $row_membersSpouceMember["gender"];
                                $memberInfoPlaceholder[$memberCount]['spouseDob'] = $row_membersSpouceMember["age"];
                                $memberInfoPlaceholder[$memberCount]['spouseDod'] = $row_membersSpouceMember["dod"];
                                $memberInfoPlaceholder[$memberCount]['spouseHomeCity'] = $row_membersSpouceMember["home_city"];
                                $memberInfoPlaceholder[$memberCount]['spouseHomeState'] = $row_membersSpouceMember["home_state"];
                                $memberInfoPlaceholder[$memberCount]['spouseMemberGen'] = $row_membersSpouceMember["member_generation"];
                                $memberInfoPlaceholder[$memberCount]['spouseMemberId'] = $membersSpouceMemberId;
                                if($row_membersSpouceMember["member_image"] == "") {
                                    $memberInfoPlaceholder[$memberCount]['spouseMemberImage'] = "image-placeholder_large.png";
                                }else {
                                    $memberInfoPlaceholder[$memberCount]['spouseMemberImage'] = $row_membersSpouceMember["member_image"];
                                }
                                if($row_membersSpouceMember["family_id"] == 1) {
                                   $memberInfoPlaceholder[$memberCount]['familySpouseStatus'] = "born-to-family"; 
                                }
                                else {
                                    $memberInfoPlaceholder[$memberCount]['familySpouseStatus'] = "married";
                                }
                            }

                            $result_membersSpouceMemberInfo = getSpouseSecondaryInfo($dbh, $membersSpouceMemberId);
                            while($row_membersSpouceMemberInfo = $result_membersSpouceMemberInfo->fetch(PDO::FETCH_ASSOC)) {
                                $memberInfoPlaceholder[$memberCount]['spouseEmail'] = $row_membersSpouceMemberInfo["email"];
                                $memberInfoPlaceholder[$memberCount]['spousePhone'] = $row_membersSpouceMemberInfo["phone"];
                                $memberInfoPlaceholder[$memberCount]['spouseCurrentCity'] = $row_membersSpouceMemberInfo["current_city"];
                                $memberInfoPlaceholder[$memberCount]['spouseCurrentState'] = $row_membersSpouceMemberInfo["current_state"];
                                $memberInfoPlaceholder[$memberCount]['spouseCollege'] = $row_membersSpouceMemberInfo["college"];
                                $memberInfoPlaceholder[$memberCount]['spouseOccupation'] = $row_membersSpouceMemberInfo["occupation"];
                                $memberInfoPlaceholder[$memberCount]['spouseAddress'] = $row_membersSpouceMemberInfo["address"];
                                $memberInfoPlaceholder[$memberCount]['spouseFacebook'] = $row_membersSpouceMemberInfo["facebook"];
                                $memberInfoPlaceholder[$memberCount]['spouseLinkedin'] = $row_membersSpouceMemberInfo["linkedin"];
                                $memberInfoPlaceholder[$memberCount]['spouseWebsite'] = $row_membersSpouceMemberInfo["website"];
                                $memberInfoPlaceholder[$memberCount]['spouseAboutMe'] = $row_membersSpouceMemberInfo["about_me"];
                            }
                        }
                    }
                 }
            }
            //If children need to set a marker to display bottom arrow and link for this members's family
            $result_checkForChildren = getChildId($dbh, $spouseMarriageId);
            if ($result_checkForChildren->rowCount()) {
                $memberInfoPlaceholder[$memberCount]['hasChildren'] = 1;
                $memberInfoPlaceholder[$memberCount]['familyUrl'] = "[[~1? &mId=`".$memberInfoPlaceholder[$memberCount]['memberId']."` &sId=`".$memberInfoPlaceholder[$memberCount]['spouseMemberId']."` &fId=`".$spouseMarriageId."`]]";
            }
            else {
                $memberInfoPlaceholder[$memberCount]['hasChildren'] = 0;
            }
            
            $memberInfoPlaceholder[$memberCount]['firstName'] = $row["first_name"];
            $memberInfoPlaceholder[$memberCount]['middleName'] = $row["middle_name"];
            $memberInfoPlaceholder[$memberCount]['lastName'] = $row["last_name"];
            $memberInfoPlaceholder[$memberCount]['suffix'] = $row["suffix"];
            $memberInfoPlaceholder[$memberCount]['nickName'] = $row["nick_name"];
            $memberInfoPlaceholder[$memberCount]['gender'] = $row["gender"];
            $memberInfoPlaceholder[$memberCount]['dob'] = $row["age"];
            $memberInfoPlaceholder[$memberCount]['dod'] = $row["dod"];
            $memberInfoPlaceholder[$memberCount]['birthCity'] = $row["home_city"];
            $memberInfoPlaceholder[$memberCount]['birthState'] = $row["home_state"];
            $memberInfoPlaceholder[$memberCount]['memberGeneration'] = $row["member_generation"];
            if($row["member_image"] == "") {
                $memberInfoPlaceholder[$memberCount]['memberImage'] = "image-placeholder_large.png";
            }else {
                $memberInfoPlaceholder[$memberCount]['memberImage'] = $row["member_image"];
            }
            
            if($row["small_image"] != ""){
                $memberInfoPlaceholder[$memberCount]['memberImageSmall'] = $row["small_image"];
            }else {
                $memberInfoPlaceholder[$memberCount]['memberImageSmall'] = "image-placeholder-small";
            }
            if($row["family_id"] == 1) {
               $memberInfoPlaceholder[$memberCount]['familyStatus'] = "born-to-family"; 
            }
            else {
                $memberInfoPlaceholder[$memberCount]['familyStatus'] = "married";
            }
            
            $memberInfoPlaceholder[$memberCount]['email'] = $row_memberInfo["email"];
            $memberInfoPlaceholder[$memberCount]['phone'] = $row_memberInfo["phone"];
            $memberInfoPlaceholder[$memberCount]['currentCity'] = $row_memberInfo["current_city"];
            $memberInfoPlaceholder[$memberCount]['currentState'] = $row_memberInfo["current_state"];
            $memberInfoPlaceholder[$memberCount]['college'] = $row_memberInfo["college"];
            $memberInfoPlaceholder[$memberCount]['occupation'] = $row_memberInfo["occupation"];
            $memberInfoPlaceholder[$memberCount]['address'] = $row_memberInfo["address"];
            $memberInfoPlaceholder[$memberCount]['facebook'] = $row_memberInfo["facebook"];
            $memberInfoPlaceholder[$memberCount]['linkedin'] = $row_memberInfo["linkedin"];
            $memberInfoPlaceholder[$memberCount]['website'] = $row_memberInfo["website"];
            $memberInfoPlaceholder[$memberCount]['aboutMe'] = $row_memberInfo["about_me"];

            $memberCount++;
        }
    
    }
    
}
$arrayTotal = array("membersInfo"=>$memberInfoPlaceholder, "childrenInfo"=>$childInfoPlaceholder, "parentInfo"=>$parentInfoPlaceholder);
//print_r($testArray);
$memberInfoPlaceholderStr["fi.JSONDisplay"] = $modx->toJSON($arrayTotal);

$modx->setPlaceholders($memberInfoPlaceholderStr);

}
else {
    header('Location: http://www.deslogefamily.com');
}
