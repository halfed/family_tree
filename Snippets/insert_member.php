<?php
//Admin code to manually insert a person to the family tree
//INCLUDE LIBRARY
require_once('assets/scripts/Library.php');

if($_GET['mID'] != ""){
    //INCLUDE CONNECTION TO DB
    include 'assets/scripts/db_connection.php';
    
    
    //GET MEMBER INFO
    $sqlMemberInfo = "SELECT first_name, middle_name, last_name, suffix, nick_name, gender, dob, dod, home_city, home_state, member_generation, member_image, small_image FROM desloge_family_member WHERE member_id = '".$_GET['mID']."'";
    $resultMemberInfo = $dbh->query($sqlMemberInfo);
    //echo "result of member: ".$modx->getCount('modResource', $resultMemberInfo);
    //echo "<br/>result of member 2: ".$resultMemberInfo->rowCount();
    
    if ($resultMemberInfo->rowCount()) {
        // output data of each row
        $memberInfoPlaceholder = array();
        while($row = $resultMemberInfo->fetch(PDO::FETCH_ASSOC)) {
            $memberInfoPlaceholder['fi.firstName'] = $row["first_name"];
            $memberInfoPlaceholder['fi.middleName'] = $row["middle_name"];
            $memberInfoPlaceholder['fi.lastName'] = $row["last_name"];
            $memberInfoPlaceholder['fi.suffix'] = $row["suffix"];
            $memberInfoPlaceholder['fi.nickName'] = $row["nick_name"];
            $memberInfoPlaceholder['fi.gender'] = $row["gender"];
            $memberInfoPlaceholder['fi.dob'] = $row["dob"];
            $keywordsDate = preg_split('/[\-]+/', $row["dob"]);
            $memberInfoPlaceholder['fi.dobYear'] = $keywordsDate[0];
            $memberInfoPlaceholder['fi.dobMonth'] = $keywordsDate[1];
            $memberInfoPlaceholder['fi.dobDay'] = $keywordsDate[2];
            
            $memberInfoPlaceholder['fi.dod'] = $row["dod"];
            $keywordsDeathDate = preg_split('/[\-]+/', $row["dod"]);
            $memberInfoPlaceholder['fi.dodYear'] = $keywordsDeathDate[0];
            $memberInfoPlaceholder['fi.dodMonth'] = $keywordsDeathDate[1];
            $memberInfoPlaceholder['fi.dodDay'] = $keywordsDeathDate[2];
            
            $memberInfoPlaceholder['fi.birthCity'] = $row["home_city"];
            $memberInfoPlaceholder['fi.birthState'] = $row["home_state"];
            $memberInfoPlaceholder['fi.memberGeneration'] = $row["member_generation"];
            $memberInfoPlaceholder['fi.memberImage'] = $row["member_image"];
            $memberInfoPlaceholder['fi.memberImageSmall'] = $row["small_image"];
            $memberInfoPlaceholder['fi.mId'] = $_GET['mID'];
            if($row["member_image"] != "") {
                $memberInfoPlaceholder['fi.memberPhoto'] = $modx->getOption('site_url'). "assets/images/profile/". $row["member_image"];
            }
            
        }
        //GET MORE MEMBER INFO
        $sql_memberInfo = "SELECT email, phone, current_city, current_state, college, occupation, address, facebook, linkedin, website, about_me  FROM desloge_family_member_info WHERE member_id = '".$_GET['mID']."'";
        $result_memberInfo = $dbh->query($sql_memberInfo);
        //echo "<br/>result of member info: ".$modx->getCount('modResource', $result_memberInfo);
        //echo "<br/>result of member info 2: ".$result_memberInfo->rowCount();
        if ($result_memberInfo->rowCount()) {
             while($row_memberInfo = $result_memberInfo->fetch(PDO::FETCH_ASSOC)) {
                $memberInfoPlaceholder['fi.email'] = $row_memberInfo["email"];
                $memberInfoPlaceholder['fi.phone'] = $row_memberInfo["phone"];
                $memberInfoPlaceholder['fi.currentCity'] = $row_memberInfo["current_city"];
                $memberInfoPlaceholder['fi.currentState'] = $row_memberInfo["current_state"];
                $memberInfoPlaceholder['fi.college'] = $row_memberInfo["college"];
                $memberInfoPlaceholder['fi.occupation'] = $row_memberInfo["occupation"];
                $memberInfoPlaceholder['fi.address'] = $row_memberInfo["address"];
                $memberInfoPlaceholder['fi.faceBook'] = $row_memberInfo["facebook"];
                $memberInfoPlaceholder['fi.linkedIn'] = $row_memberInfo["linkedin"];
                $memberInfoPlaceholder['fi.website'] = $row_memberInfo["website"];
                $memberInfoPlaceholder['fi.aboutMe'] = $row_memberInfo["about_me"];
             }
        }
        
        $sql_memberFamily = "SELECT family_id FROM desloge_family WHERE member_id = '".$_GET['mID']."'";
        $result_memberFamily = $dbh->query($sql_memberFamily);
        if ($result_memberFamily->rowCount()) {
            // output data of each row
            while($row_memberFamily = $result_memberFamily->fetch(PDO::FETCH_ASSOC)) {
                if($row_memberFamily["family_id"] == 1){
                    $memberInfoPlaceholder['fi.bornIntoFamily'] = 'checked';
                }
            }
        }
        
         
        //GET MEMBER PARENT INFO
        $sql_membersParentId = "SELECT parent_id FROM desloge_family_siblings WHERE member_id = '".$_GET['mID']."'";
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
                    $memberInfoPlaceholder['fi.parentFName'] = $row_membersParentInfo["first_name"];
                    $memberInfoPlaceholder['fi.parentLName'] = $row_membersParentInfo["last_name"];
                    $memberInfoPlaceholder['fi.parentDob'] = $row_membersParentInfo["dob"];
                    $keywordsDateParent = preg_split('/[\-]+/', $row_membersParentInfo["dob"]);
                    $memberInfoPlaceholder['fi.parentDobYear'] = $keywordsDateParent[0];
                    $memberInfoPlaceholder['fi.parentDobMonth'] = $keywordsDateParent[1];
                    $memberInfoPlaceholder['fi.parentDobDay'] = $keywordsDateParent[2];
                }
            }
        }
         
        //GET MEMBER SPOUSE INFO FROM SPOUSE1
        $sql_memberSpouseMarriageId = "SELECT marriage_id FROM desloge_member_spouse WHERE spouse1_id = '".$_GET['mID']."'";
        $result_memberSpouseMarriageId = $dbh->query($sql_memberSpouseMarriageId);
        //echo "<br/>from check spouse spouse 1: ".$memberSpouseMarriageIdResult;
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
                if ($membersSpouceMemberId != "" || $membersSpouceMemberId != null) {
                    //GET SPOUSE INFO
                    $memberInfoPlaceholder['fi.haveSpouse'] = 'checked';
                    $sql_membersSpouceMemberInfo = "SELECT first_name, last_name, dob FROM desloge_family_member WHERE member_id = '".$membersSpouceMemberId."'";
                    $result_membersSpouceMemberInfo = $dbh->query($sql_membersSpouceMemberInfo);
                    while($row_membersSpouceMemberInfo = $result_membersSpouceMemberInfo->fetch(PDO::FETCH_ASSOC)) {
                        $memberInfoPlaceholder['fi.spouseFName'] = $row_membersSpouceMemberInfo["first_name"];
                        $memberInfoPlaceholder['fi.spouseLName'] = $row_membersSpouceMemberInfo["last_name"];
                        $memberInfoPlaceholder['fi.spouseDob'] = $row_membersSpouceMemberInfo["dob"];
                        $keywordsDateSpouse = preg_split('/[\-]+/', $row_membersSpouceMemberInfo["dob"]);
                        $memberInfoPlaceholder['fi.spouseDobYear'] = $keywordsDateSpouse[0];
                        $memberInfoPlaceholder['fi.spouseDobMonth'] = $keywordsDateSpouse[1];
                        $memberInfoPlaceholder['fi.spouseDobDay'] = $keywordsDateSpouse[2];
                    }
                }
            }
        }else {
             //GET MEMBER SPOUSE INFO FROM SPOUSE2
             $sql_memberSpouseMarriageId = "SELECT marriage_id FROM desloge_member_spouse WHERE spouse2_id = '".$_GET['mID']."'";
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
                    if ($membersSpouceMemberId != "" || $membersSpouceMemberId != null) {
                        //GET SPOUSE INFO
                        $memberInfoPlaceholder['fi.haveSpouse'] = 'checked';
                        $sql_membersSpouceMemberInfo = "SELECT first_name, last_name, dob FROM desloge_family_member WHERE member_id = '".$membersSpouceMemberId."'";
                        $result_membersSpouceMemberInfo = $dbh->query($sql_membersSpouceMemberInfo);
                        while($row_membersSpouceMemberInfo = $result_membersSpouceMemberInfo->fetch(PDO::FETCH_ASSOC)) {
                            $memberInfoPlaceholder['fi.spouseFName'] = $row_membersSpouceMemberInfo["first_name"];
                            $memberInfoPlaceholder['fi.spouseLName'] = $row_membersSpouceMemberInfo["last_name"];
                            $memberInfoPlaceholder['fi.spouseDob'] = $row_membersSpouceMemberInfo["dob"];
                            $keywordsDateSpouse = preg_split('/[\-]+/', $row_membersSpouceMemberInfo["dob"]);
                            $memberInfoPlaceholder['fi.spouseDobYear'] = $keywordsDateSpouse[0];
                            $memberInfoPlaceholder['fi.spouseDobMonth'] = $keywordsDateSpouse[1];
                            $memberInfoPlaceholder['fi.spouseDobDay'] = $keywordsDateSpouse[2];
                        }
                    }
                }
             }
        }
        $modx->setPlaceholders($memberInfoPlaceholder);
    }
}

$killSwitch = true;


if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST["firstName"] != "" && $_POST["lastName"] != "" && $_POST["memberGeneration"] != "" && $killSwitch){
    $mID = $_POST["mId"];
    $firstName = trim(addslashes($_POST["firstName"]));
    $lastName = trim(addslashes($_POST["lastName"]));
    $middleName = trim(addslashes($_POST["middleName"]));
    $suffix = trim(addslashes($_POST["suffix"]));
    $nickName = trim(addslashes($_POST["nickName"]));
    $gender = trim(addslashes($_POST["gender"]));
    $dobDay = $_POST["dobDay"];
    $dobMonth = $_POST["dobMonth"];
    $dobYear = $_POST["dobYear"];
    $dob = $_POST["dob"];
    $dod = $_POST["dod"];
    $birthCity = trim(addslashes($_POST["birthCity"]));
    $birthState = trim(addslashes($_POST["birthState"]));
    $memberGeneration = trim($_POST["memberGeneration"]);
    
    $parentFName = trim(addslashes($_POST["parentFName"]));
    $parentLName = trim(addslashes($_POST["parentLName"]));
    $parentDob = $_POST["parentDob"];

    $bornIntoFamily = $_POST["bornIntoFamily"];
    if($bornIntoFamily != 1){
        $bornIntoFamily = 0;
    }
    $haveSpouse = $_POST["haveSpouse"];
    $spouseFName = trim(addslashes($_POST["spouseFName"]));
    $spouseLName = trim(addslashes($_POST["spouseLName"]));
    $spouseDob = $_POST["spouseDob"];
    $memberImage = str_replace(' ', '_', trim($_POST["memberImage"])); //trim($_POST["memberImage"]);
    
    $email = trim(addslashes($_POST["email"]));
    $phone = trim($_POST["phone"]);
    $currentCity = trim(addslashes($_POST["currentCity"]));
    $currentState = trim(addslashes($_POST["currentState"]));
    $college = trim(addslashes($_POST["college"]));
    $occupation = trim(addslashes($_POST["occupation"]));
    $sanitizedFaceBook = filter_input(INPUT_POST, "faceBook", FILTER_SANITIZE_URL);
    $sanitizedLinkedIn = filter_input(INPUT_POST, "linkedIn", FILTER_SANITIZE_URL);
    $sanitizedWebsite = filter_input(INPUT_POST, "website", FILTER_SANITIZE_URL);
    $aboutMe = trim(addslashes($_POST["aboutMe"]));
    include 'assets/scripts/db_connection.php';

    if($mID != "") {
        $sql = "UPDATE desloge_family_member 
                SET first_name = '$firstName', middle_name = '$middleName', last_name = '$lastName', suffix = '$suffix', 
                nick_name = '$nickName', gender = '$gender', dob = '$dob', dod = '$dod', home_city = '$birthCity', home_state = '$birthState', member_generation = '$memberGeneration', member_image = '$memberImage' 
                WHERE member_id = '$mID'";
    
        $stmt = $dbh->query($sql);
    
        //Update into member info table with id from previous insert
        $sql2 = "UPDATE desloge_family_member_info 
                 SET email = '$email', phone = '$phone', current_city = '$currentCity', current_state = '$currentState', college = '$college', occupation = '$occupation', 
                 facebook = '$sanitizedFaceBook', linkedin = '$sanitizedLinkedIn', website = '$sanitizedWebsite', about_me = '$aboutMe'
                 WHERE member_id = '$mID'";
        
        $stmt = $dbh->query($sql2);
    
        //Update member Id into member_family
        $sql3 = "UPDATE desloge_family SET family_id = '$bornIntoFamily', generation_id = '$memberGeneration' WHERE member_id = '$mID'";
        $stmt = $dbh->query($sql3);
        
        //UPDATE SIBLING STATUS
        $sql4 = "UPDATE desloge_family_siblings SET member_generation = '$memberGeneration' WHERE member_id = '$mID'";
        $stmt = $dbh->query($sql4);
        
        addMemberParent($dbh, $mID, $parentFName, $parentLName, $parentDob, $memberGeneration);
        
        if($haveSpouse) {
            //All members initially get inserted(One day they might marry) check if member is in the spouse table as spouse2, if not insert as spouse1
            //Get spouse ID
            $sqlCheckSpouse = "SELECT member_id FROM desloge_family_member WHERE first_name = '".$spouseFName."' AND last_name = '".$spouseLName."' AND dob = '".$spouseDob."'";
            $initialResultSpouse = $dbh->query($sqlCheckSpouse);
            if (!$initialResultSpouse->rowCount()) {
                //SPOUSE DOES NOT EXIST YET IN DATABASE, INSERT THEIR INFO NOW
                addMemberSpouse($dbh, $spouseFName, $spouseLName, $spouseDob, $memberGeneration, $bornIntoFamily, $gender);
            }
            //RE RUN INITIAL SPOUSE CHECK TO TIE MEMBER AND NEW SPOUSE TOGETHER
            $resultSpouse = $dbh->query($sqlCheckSpouse);
            if ($resultSpouse->rowCount()) {
                while($row = $resultSpouse->fetch(PDO::FETCH_ASSOC)) {
                    $spouseMemberID = $row["member_id"];
                }
                //Get Spouse's Marriage ID
                $sql_spouse1 = "SELECT marriage_id FROM desloge_member_spouse WHERE spouse1_id = '".$spouseMemberID."'";
                $stmt_spouse1Result = $dbh->query($sql_spouse1);
                        
                if($stmt_spouse1Result->rowCount()) {
                    while($row_spouse = $stmt_spouse1Result->fetch(PDO::FETCH_ASSOC)) {
                        $spouseMarriageID = $row_spouse["marriage_id"];
                    }
                    $sql_checkIfBothExist = "SELECT marriage_id FROM desloge_member_spouse WHERE spouse1_id = '".$spouseMemberID."' AND spouse2_id = '".$mID."'";
                    $stmt_checkIfBothExistResult = $dbh->query($sql_checkIfBothExist);
                    if(!$stmt_checkIfBothExistResult->rowCount()) {
                        //COUPLE OF CASES WE NEED TO HANDLE:
                        //1: IF A MEMBER INITIALLY IS NOT TIED TO ANOTHER MEMBER WE NEED TO TIE BOTH OF THOSE TOGETHER SO THEY HAVE ONE MARRIAGE ID BETWEEN THEM AND DELETE 
                        // ONE MEMBER'S ROW.  WE HAVE DECIDED TO DELETE THE MEMBER'S ID WHO IS CURRENTLY BEING UPDATED AND TIE THAT TO THEIR SPOUSE'S ID IF (IF THEIR RECORD IS NOT TIED TO ANOTHER MEMBER ID)
                        //THIS SATISFIES THE CONDITION IF A MEMBER REMARRIES WE CAN STILL PRESERVE THE ORIGINAL MARRIAGE ID POSSIBLY TIED TO CHILDREN FROM PREVIOUS MARRAIAGE
                        $sql_ifMemberIsTiedToSomeone = "SELECT marriage_id FROM desloge_member_spouse WHERE spouse1_id = ".$mID." and spouse2_id IS NULL or spouse2_id = ''";
                        $stmt_ifMemberIsTiedToSomeoneResult = $dbh->query($sql_ifMemberIsTiedToSomeone);
                        if($stmt_ifMemberIsTiedToSomeoneResult->rowCount()) {
                            //DELETE INITIAL ROW THAT HAS MEMBER'S ID SO THERE WONT BE TWO MEMBERS RECORDS, WE CANT HAVE TWO ROWS THAT HAVE ONE TIED TO NO SPOUSE AND ONE THAT IS
                            $sql_deleteInitialMemberId = "DELETE FROM desloge_member_spouse WHERE spouse1_id = '".$mID."'";
                            $stmt_deleteIfBothExistResult = $dbh->query($sql_deleteInitialMemberId);
                        }
                        //SET MEMBER'S ID TO SPOUSES ROW IN SPOUSE 2
                        $sql5 = "UPDATE desloge_member_spouse SET spouse2_id = '".$mID."' WHERE marriage_id = '".$spouseMarriageID."'";
                        $stmt = $dbh->query($sql5);
                        ///$parentId = $dbh->lastInsertId();
                    }
                }else {
                    $sql_spouse2 = "SELECT marriage_id FROM desloge_member_spouse WHERE spouse2_id = '".$spouseMemberID."'";
                    $stmt_spouse2Result = $dbh->query($sql_spouse2);
                    if($stmt_spouse2Result->rowCount()) {
                        while($row_spouse2 = $stmt_spouse2Result->fetch(PDO::FETCH_ASSOC)) {
                            $spouseMarriageID = $row_spouse2["marriage_id"];
                        }
                        $sql_checkIfBothExist = "SELECT marriage_id FROM desloge_member_spouse WHERE spouse2_id = '".$spouseMemberID."' AND spouse1_id = '".$mID."'";
                        $stmt_checkIfBothExistResult = $dbh->query($sql_checkIfBothExist);
                        if(!$stmt_checkIfBothExistResult->rowCount()) {
                            //COUPLE OF CASES WE NEED TO HANDLE:
                            //1: IF A MEMBER INITIALLY IS NOT TIED TO ANOTHER MEMBER WE NEED TO TIE BOTH OF THOSE TOGETHER SO THEY HAVE ONE MARRIAGE ID BETWEEN THEM AND DELETE 
                            // ONE MEMBER'S ROW.  WE HAVE DECIDED TO DELETE THE MEMBER'S ID WHO IS CURRENTLY BEING UPDATED AND TIE THAT TO THEIR SPOUSE'S ID IF (IF THEIR RECORD IS NOT TIED TO ANOTHER MEMBER ID)
                            //THIS SATISFIES THE CONDITION IF A MEMBER REMARRIES WE CAN STILL PRESERVE THE ORIGINAL MARRIAGE ID POSSIBLY TIED TO CHILDREN FROM PREVIOUS MARRAIAGE
                            $sql_ifMemberIsTiedToSomeone = "SELECT marriage_id FROM desloge_member_spouse WHERE spouse2_id = ".$mID." and spouse1_id IS NULL or spouse1_id = ''";
                            $stmt_ifMemberIsTiedToSomeoneResult = $dbh->query($sql_ifMemberIsTiedToSomeone);
                            if($stmt_ifMemberIsTiedToSomeoneResult->rowCount()) {
                                //DELETE INITIAL ROW THAT HAS MEMBER'S ID SO THERE WONT BE TWO MEMBERS RECORDS, WE CANT HAVE TWO ROWS THAT HAVE ONE TIED TO NO SPOUSE AND ONE THAT IS
                                $sql_deleteInitialMemberId = "DELETE FROM desloge_member_spouse WHERE spouse2_id = '".$mID."'";
                                $stmt_deleteIfBothExistResult = $dbh->query($sql_deleteInitialMemberId);
                            }
                            //SET MEMBER'S ID TO SPOUSES ROW IN SPOUSE 2
                            $stmt_spouse1Result = $dbh->query($sql_spouse1);
                            $sql6 = "UPDATE desloge_member_spouse SET spouse1_id = '".$mID."' WHERE marriage_id = '".$spouseMarriageID."'";
                            $stmt = $dbh->query($sql6);
                        }
                    }
                }
            }
        }
    }else {
        $sql = "INSERT INTO desloge_family_member (first_name, middle_name, last_name, suffix, nick_name, gender, dob, dod, home_city, home_state, member_generation, member_image) VALUES ('$firstName', '$middleName', '$lastName', '$suffix', '$nickName', '$gender', '$dob', '$dod', '$birthCity', '$birthState', '$memberGeneration', '$memberImage')";
        
        $stmt = $dbh->query($sql);
    
        //Another way to execute code, but if $stmt is set like above
        //$stmt->execute();
        //Get last inserted member id
        $memberId = $dbh->lastInsertId();
      
        //Insert into member info table with id from previous insert
        $sql2 = "INSERT INTO desloge_family_member_info (member_id, email, phone, current_city, current_state, college, occupation, facebook, linkedin, website, about_me) 
                 VALUES ('$memberId', '$email', '$phone', '$currentCity', '$currentState', '$college', '$occupation', '$sanitizedFaceBook', '$sanitizedLinkedIn', '$sanitizedWebsite', '$aboutMe')";
        
        $stmt = $dbh->query($sql2);
    
        //Insert member Id into member_family
        $sql3 = "INSERT INTO desloge_family (member_id, family_id, generation_id) VALUES ('$memberId', '$bornIntoFamily', '$memberGeneration')";
        $stmt = $dbh->query($sql3);
    
        //At some point a member might have siblings
        $sql4 = "INSERT INTO desloge_family_siblings (member_id, member_generation) VALUES ('$memberId', '$memberGeneration')";
        $stmt = $dbh->query($sql4);
        
        addMemberParent($dbh, $memberId, $parentFName, $parentLName, $parentDob, $memberGeneration);
        
        if($haveSpouse) {
            //All members initially get inserted(One day they might marry) check if member is in the spouse table as spouse2, if not insert as spouse1
            $sqlCheckSpouse = "SELECT member_id FROM desloge_family_member WHERE first_name = '".$spouseFName."' AND last_name = '".$spouseLName."' AND dob = '".$spouseDob."'";
            $resultSpouse = $dbh->query($sqlCheckSpouse);
            if (!$resultSpouse->rowCount()) {
                //SPOUSE DOES NOT EXIST YET IN DATABASE, INSERT THEIR INFO NOW
                addMemberSpouse($dbh, $spouseFName, $spouseLName, $spouseDob, $memberGeneration, $bornIntoFamily, $gender);
            }
            //RE RUN INITIAL SPOUSE CHECK TO TIE MEMBER AND NEW SPOUSE TOGETHER
            $resultSpouse = $dbh->query($sqlCheckSpouse);
            if ($resultSpouse->rowCount()) {
                while($row = $resultSpouse->fetch(PDO::FETCH_ASSOC)) {
                    $spouseMemberID = $row["member_id"];
                }
                $sql_spouse1 = "SELECT marriage_id FROM desloge_member_spouse WHERE spouse1_id = '".$spouseMemberID."'";
                $stmt_spouse1Result = $dbh->query($sql_spouse1);
                        
                if($stmt_spouse1Result->rowCount()) {
                    while($row_spouse = $stmt_spouse1Result->fetch(PDO::FETCH_ASSOC)) {
                        $spouseMarriageID = $row_spouse["marriage_id"];
                    }
                    $sql5 = "UPDATE desloge_member_spouse SET spouse2_id = '".$memberId."' WHERE marriage_id = '".$spouseMarriageID."'";
                    $stmt = $dbh->query($sql5);
                    ///$parentId = $dbh->lastInsertId();
                }else {
                    $sql_spouse2 = "SELECT marriage_id FROM desloge_member_spouse WHERE spouse2_id = '".$spouseMemberID."'";
                    $stmt_spouse2Result = $dbh->query($sql_spouse2);
                    while($row_spouse2 = $stmt_spouse2Result->fetch(PDO::FETCH_ASSOC)) {
                        $spouseMarriageID = $row_spouse2["marriage_id"];
                    }
                    $stmt_spouse1Result = $dbh->query($sql_spouse1);
                    $sql6 = "UPDATE desloge_member_spouse SET spouse1_id = '".$memberId."' WHERE marriage_id = '".$spouseMarriageID."'";
                    $stmt = $dbh->query($sql6);
                }
            }else {
	            $sql7 = "INSERT INTO desloge_member_spouse (spouse1_id) VALUES ('$memberId')";
	            $stmt = $dbh->query($sql7);
	            //$parentId = $dbh->lastInsertId();
	        }
        }else {
            $sql7 = "INSERT INTO desloge_member_spouse (spouse1_id) VALUES ('$memberId')";
            $stmt = $dbh->query($sql7);
        }
    }

}else {
    $errorTxt = "Please try again later";
    echo $errorTXt;
}
