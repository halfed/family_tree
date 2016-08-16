<?php
//library of functions

//require_once('assets/scripts/Library.php');

//function to check if a user is logged in, if there session is lost, we need to redirect them to the log in page to prevent them from 
//updating there profile/kids profile without a user id.
function checkSession() {
    
}


//function to sanitize data
//not used but good to have
function sanitizeInput($inputToSanitize) {
    $thisSanitizedInput = filter_input(INPUT_POST, "sanitizeInput", FILTER_SANITIZE_URL);
    
    return $thisSanitizedInput;
}

function searchUsers($dbh, $userFName, $userLName) {
    //$sqlCheckUser = "SELECT member_id, first_name, last_name, dob FROM desloge_family_member WHERE first_name like '%".$userFName."%' AND last_name like '%".$userLName."%'";
    $sqlCheckUser = "
        SELECT t1.member_id, t1.first_name, t1.last_name, DATE_FORMAT(t1.dob, '%m/%d/%Y') AS newDob, t4.first_name AS parent_fName, t4.last_name AS parent_lName
        FROM desloge_family_member AS t1  
        LEFT JOIN desloge_family_children AS t2
        ON t1.member_id = t2.child_id 
        LEFT JOIN desloge_member_spouse AS t3
        ON t2.parent_id =  t3.marriage_id
        LEFT JOIN desloge_family_member AS t4
        ON t3.spouse1_id = t4.member_id
        WHERE t1.first_name like '%".$userFName."%' AND t1.last_name like '%".$userLName."%'
    ";
    $stmt_resultCheckUser = $dbh->query($sqlCheckUser);
    return $stmt_resultCheckUser;
}

function updateFamilyMember() {
    
}

//Check if Member exists by member id
function checkMemberExists ($dbh, $modxMemberId) {
    $sqlCheckUser = "SELECT member_id FROM desloge_family_member WHERE family_id =  '".$modxMemberId."'";
    $resultCheckUser = $dbh->query($sqlCheckUser);
    return $resultCheckUser;
}

//Get member and member's spouse information
function getParentInfo($dbh, $memberId, $memberSpousesId) {
    $sqlParentInfo = "SELECT t1.member_id, t1.first_name, t1.middle_name, t1.last_name, t1.suffix, t1.nick_name, t1.gender, TIMESTAMPDIFF(YEAR, t1.dob, CURDATE()) AS age, 
                            t1.dod, t1.home_city, t1.home_state, t1.member_generation, t1.member_image, t1.small_image,  t2.email, t2.phone, t2.current_city, t2.current_state, t2.college, 
                            t2.occupation, t2.address, t2.facebook, t2.linkedin, t2.website, t2.about_me, t4.family_id
                            FROM desloge_family_member AS t1 
                            INNER JOIN desloge_family_member_info AS t2 
                            ON t1.member_id = t2.member_id 
                            INNER JOIN desloge_family AS t3 
                            ON t3.member_id = t1.member_id
                            INNER JOIN desloge_family AS t4
                            ON t1.member_id = t4.member_id
                            WHERE t1.member_id = '".$memberId."'
                            OR t1.member_id = '".$memberSpousesId."'";
                            
    $resultParentInfo = $dbh->query($sqlParentInfo);
    return $resultParentInfo;
}

//Get children info of parents
function getChildrenInfo($dbh, $memberFamilyId) {
    $sqlChildrenInfo = "SELECT t3.member_id, t3.family_id, t3.first_name, t3.middle_name, t3.last_name, t3.suffix, t3.nick_name, t3.gender, TIMESTAMPDIFF(YEAR, t3.dob, CURDATE()) AS age, 
                        t3.dod, t3.home_city, t3.home_state, t3.member_generation, t3.member_image, t3.small_image, t4.email, t4.phone, t4.current_city, t4.current_state, t4.college, 
                        t4.occupation, t4.address, t4.facebook, t4.linkedin, t4.website, t4.about_me, t5.family_id 
                        FROM desloge_family_children AS t1
                        LEFT JOIN desloge_member_spouse AS t2 
                        ON t1.parent_id=t2.marriage_id
                        LEFT JOIN desloge_family_member AS t3
                        ON t1.child_id=t3.member_id
                        LEFT JOIN desloge_family_member_info AS t4
                        ON t3.member_id=t4.member_id
                        INNER JOIN desloge_family AS t5
                        ON t3.member_id = t5.member_id
                        WHERE t2.marriage_id = '".$_GET['fId']."' 
                        ORDER by t3.dob ASC";
    $resultChildrenInfo = $dbh->query($sqlChildrenInfo);
    return $resultChildrenInfo;
}

//Get marriage id from member by means of spouse 1 table
function getMrgIdFrmSpouse1($dbh, $memberId) {
    $sql_checkSpouseMarriageId = "SELECT marriage_id FROM desloge_member_spouse WHERE spouse1_id = '".$memberId."'";
    $result_checkSpouseMarriageId = $dbh->query($sql_checkSpouseMarriageId);
    return $result_checkSpouseMarriageId;
}

//Get spouse id from marriage id by means of looking for them under spouse 2 column
function getSpouseIdFromMemberSp2($dbh, $spouseMarriageId) {
    $sql_membersSpouceMemberId = "SELECT spouse2_id FROM desloge_member_spouse WHERE marriage_id = '".$spouseMarriageId."'";

    $result_membersSpouceMemberId = $dbh->query($sql_membersSpouceMemberId);
    return $result_membersSpouceMemberId;
}

//Get spouse information from spouse id from marriage id of member
function getSpouseIdInformation($dbh, $membersSpouceMemberId) {
    $sql_membersSpouceMember = "SELECT t1.first_name, t1.middle_name, t1.last_name, t1.suffix, t1.nick_name, t1.gender, TIMESTAMPDIFF(YEAR, t1.dob, CURDATE()) AS age, t1.dod, t1.home_city, t1.home_state, t1.member_generation, t1.member_image, t1.small_image, t2.family_id
                               FROM desloge_family_member AS t1
                               INNER JOIN desloge_family AS t2
                               ON t1.member_id = t2.member_id
                               WHERE t1.member_id = '".$membersSpouceMemberId."'";
                               
    $result_membersSpouceMember = $dbh->query($sql_membersSpouceMember);
    return $result_membersSpouceMember;
}

//Get spouse secondary information from spouse id from marriage id of member
function getSpouseSecondaryInfo($dbh, $membersSpouceMemberId) {
    $sql_membersSpouceMemberInfo = "SELECT email, phone, current_city, current_state, college, occupation, address, facebook, linkedin, website, about_me
                                    FROM desloge_family_member_info 
                                    WHERE member_id = '".$membersSpouceMemberId."'";

    $result_membersSpouceMemberInfo = $dbh->query($sql_membersSpouceMemberInfo);
    return $result_membersSpouceMemberInfo;
}

//Get marriage id from member by means of spouse 2 table
function getMrgIdFrmSpouse2($dbh, $memberId) {
    $sql_checkSpouseMarriageId = "SELECT marriage_id FROM desloge_member_spouse WHERE spouse2_id = '".$memberId."'";
    $result_checkSpouseMarriageId = $dbh->query($sql_checkSpouseMarriageId);
    return $result_checkSpouseMarriageId;
}

//Get spouse id from marriage id by means of looking for them under spouse 1 column                   
function getSpouseIdFromMemberSp1($dbh, $spouseMarriageId) {
    $sql_membersSpouceMemberId = "SELECT spouse1_id FROM desloge_member_spouse WHERE marriage_id = '".$spouseMarriageId."'";
    $result_membersSpouceMemberId = $dbh->query($sql_membersSpouceMemberId);
    return $result_membersSpouceMemberId;
}
                    
//Get child id from member
function getChildId($dbh, $spouseMarriageId) {
    $sql_checkForChildren = "select child_id FROM desloge_family_children WHERE parent_id = '".$spouseMarriageId."'";
    $result_checkForChildren = $dbh->query($sql_checkForChildren);
    return $result_checkForChildren;
}

//Get all members starting from 1st Generation
function getAllMembersFirstGen($dbh){
    $sqlMemberInfo = "SELECT t1.member_id, t1.family_id, t1.first_name, t1.middle_name, t1.last_name, t1.suffix, t1.nick_name, t1.gender, TIMESTAMPDIFF(YEAR, t1.dob, CURDATE()) AS age, 
                  t1.dod, t1.home_city, t1.home_state, t1.member_generation, t1.member_image, t1.small_image, t2.email, t2.phone, t2.current_city, t2.current_state, t2.college, 
                  t2.occupation, t2.address, t2.facebook, t2.linkedin, t2.website, t2.about_me, t3.family_id
                  FROM desloge_family_member AS t1 
                  INNER JOIN desloge_family_member_info AS t2 ON t1.member_id = t2.member_id 
                  INNER JOIN desloge_family AS t3 ON t3.member_id = t1.member_id
                  WHERE t3.family_id = '1' AND t3.generation_id =  '1'";
    $resultMemberInfo = $dbh->query($sqlMemberInfo);
    return $resultMemberInfo;
}

function addMemberParent($dbh, $memberId, $parentFName, $parentLName, $parentDob, $memberGenNum) {
    if($parentFName != "" && $parentLName != "" && $parentDob != "") {
        //Update or insert member Id into member_siblings
        $sql_parent = "SELECT member_id, member_generation FROM desloge_family_member WHERE first_name = '".$parentFName."' AND last_name = '".$parentLName."' AND dob = '".$parentDob."'";
        $stmt_result = $dbh->query($sql_parent);
        if ($stmt_result->rowCount()) {
            // output data of each row
            while($row = $stmt_result->fetch(PDO::FETCH_ASSOC)) {
                $parentMemberID = $row["member_id"];
                $parentGeneration = $row["member_generation"];
            }
            $sql_parentId = "SELECT marriage_id FROM desloge_member_spouse WHERE (spouse1_id = '".$parentMemberID."' or spouse2_id = '".$parentMemberID."')";
            $stmt_result = $dbh->query($sql_parentId);
            while($row_parentId = $stmt_result->fetch(PDO::FETCH_ASSOC)) {
                //PARENTPARENTID IS THE MARRIAGE ID OF BOTH PARENTS WHICH TIE A SINGLE FAMILY UNIT WITHIN THE WHOLE FAMILY
                $parentParentId = $row_parentId["marriage_id"];
            }
            //NOW WE WILL CHECK IF THE PARENT IS TIED TO THIS NEW / UPDATED CHILD
            $sql_checkParentChild = "SELECT children_id FROM desloge_family_children WHERE child_id = '".$memberId."' AND parent_id = '".$parentParentId."'";
            $stmt_parentChildResult = $dbh->query($sql_checkParentChild);
            if(!$stmt_parentChildResult->rowCount()) {
               //NOW WE WILL TIE THE PARENT TO THIS NEW CHILD
                $sql6 = "INSERT INTO desloge_family_children (parent_id, child_id) VALUES ('$parentParentId', '$memberId')";
                $stmt = $dbh->query($sql6);
                
                $sql_parent_insert = "UPDATE desloge_family_siblings SET parent_id = '".$parentParentId."' WHERE member_id = '".$memberId."'";
                $stmt = $dbh->query($sql_parent_insert); 
            }
            //LASTLY WE NEED TO CHECK IF A USER ACCIDENTLY SELECTED WRONG MEMBER GENERATION AS THEY COULD BE DISPLAYED IN A GENERATION 
            //THEY DIDN'T INTEND TO BE AND STILL BE DISPLAYED UNDER THEIR PARENTS TREE.
            $compareParentGeneration = $parentGeneration + 1;
            if($compareParentGeneration != $memberGenNum) {
                $updateMemberProfileGen = "UPDATE desloge_family_member 
                SET member_generation = '$compareParentGeneration'
                WHERE member_id = '$memberId'";
        
                $executeUpdateMemberProfileGen = $dbh->query($updateMemberProfileGen);
                
                $updateMemberFamilyGen = "UPDATE desloge_family 
                SET generation_id = '$compareParentGeneration'
                WHERE member_id = '$memberId'";
        
                $executeUpdateMemberFamilyGen = $dbh->query($updateMemberFamilyGen);
            }
        }
    }
}

function addMemberSpouse($dbh, $firstName, $lastName, $dob, $memberGeneration, $bornIntoFamily, $gender) {
    if($gender == "male") {
        $spouseGender = "female";
    }
    else {
        $spouseGender = "male";
    }
    $sql = "INSERT INTO desloge_family_member (first_name, last_name, gender, dob) VALUES ('$firstName', '$lastName', '$spouseGender', '$dob')";
        
    $stmt = $dbh->query($sql);
    
    //Another way to execute code, but if $stmt is set like above
    //$stmt->execute();
    //Get last inserted member id
    $spouseMemberId = $dbh->lastInsertId();
    
    //Insert into member info table with id from previous insert
    $sql2 = "INSERT INTO desloge_family_member_info (member_id) VALUES ('$spouseMemberId')";
    
    $stmt = $dbh->query($sql2);
    
    //Insert member Id into member_family
    if($bornIntoFamily == 1){
        $spouseBornIntoFamily = 0;
    }
    else {
    	$spouseBornIntoFamily = 1;
    }
    $sql3 = "INSERT INTO desloge_family (member_id, family_id, generation_id) VALUES ('$spouseMemberId', '$spouseBornIntoFamily', '$memberGeneration')";
    $stmt = $dbh->query($sql3);
    
    //At some point a member might have siblings
    $sql4 = "INSERT INTO desloge_family_siblings (member_id, member_generation) VALUES ('$spouseMemberId', '$memberGeneration')";
    $stmt = $dbh->query($sql4);
    
    
    $sql7 = "INSERT INTO desloge_member_spouse (spouse1_id) VALUES ('$spouseMemberId')";
    $stmt = $dbh->query($sql7);
    
}                   

//THIS FUNCTION SERVES THE PURPOSE OF ONLY ON AN UPDATE OF A MEMBER'S PROFILE IF THEY HAVE A SPOUSE.  NOT TO BE USED ON AN INSERT OF A MEMBER'S PROFILE
function updateMembersSpouse($dbh, $spouseFName, $spouseLName, $spouseDob, $memberId, $memberGeneration, $bornIntoFamily) {
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
            $sql_checkIfBothExist = "SELECT marriage_id FROM desloge_member_spouse WHERE spouse1_id = '".$spouseMemberID."' AND spouse2_id = '".$memberId."'";
            $stmt_checkIfBothExistResult = $dbh->query($sql_checkIfBothExist);
            if(!$stmt_checkIfBothExistResult->rowCount()) {
                //COUPLE OF CASES WE NEED TO HANDLE:
                //1: IF A MEMBER INITIALLY IS NOT TIED TO ANOTHER MEMBER WE NEED TO TIE BOTH OF THOSE TOGETHER SO THEY HAVE ONE MARRIAGE ID BETWEEN THEM AND DELETE 
                // ONE MEMBER'S ROW.  WE HAVE DECIDED TO DELETE THE MEMBER'S ID WHO IS CURRENTLY BEING UPDATED AND TIE THAT TO THEIR SPOUSE'S ID IF (IF THEIR RECORD IS NOT TIED TO ANOTHER MEMBER ID)
                //THIS SATISFIES THE CONDITION IF A MEMBER REMARRIES WE CAN STILL PRESERVE THE ORIGINAL MARRIAGE ID POSSIBLY TIED TO CHILDREN FROM PREVIOUS MARRAIAGE
                $sql_ifMemberIsTiedToSomeone = "SELECT marriage_id FROM desloge_member_spouse WHERE spouse1_id = ".$memberId." and spouse2_id IS NULL or spouse2_id = ''";
                $stmt_ifMemberIsTiedToSomeoneResult = $dbh->query($sql_ifMemberIsTiedToSomeone);
                if($stmt_ifMemberIsTiedToSomeoneResult->rowCount()) {
                    //DELETE INITIAL ROW THAT HAS MEMBER'S ID SO THERE WONT BE TWO MEMBERS RECORDS, WE CANT HAVE TWO ROWS THAT HAVE ONE TIED TO NO SPOUSE AND ONE THAT IS
                    $sql_deleteInitialMemberId = "DELETE FROM desloge_member_spouse WHERE spouse1_id = '".$memberId."'";
                    $stmt_deleteIfBothExistResult = $dbh->query($sql_deleteInitialMemberId);
                }
                //SET MEMBER'S ID TO SPOUSES ROW IN SPOUSE 2
                $sql5 = "UPDATE desloge_member_spouse SET spouse2_id = '".$memberId."' WHERE marriage_id = '".$spouseMarriageID."'";
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
                $sql_checkIfBothExist = "SELECT marriage_id FROM desloge_member_spouse WHERE spouse2_id = '".$spouseMemberID."' AND spouse1_id = '".$memberId."'";
                $stmt_checkIfBothExistResult = $dbh->query($sql_checkIfBothExist);
                if(!$stmt_checkIfBothExistResult->rowCount()) {
                    //COUPLE OF CASES WE NEED TO HANDLE:
                    //1: IF A MEMBER INITIALLY IS NOT TIED TO ANOTHER MEMBER WE NEED TO TIE BOTH OF THOSE TOGETHER SO THEY HAVE ONE MARRIAGE ID BETWEEN THEM AND DELETE 
                    // ONE MEMBER'S ROW.  WE HAVE DECIDED TO DELETE THE MEMBER'S ID WHO IS CURRENTLY BEING UPDATED AND TIE THAT TO THEIR SPOUSE'S ID IF (IF THEIR RECORD IS NOT TIED TO ANOTHER MEMBER ID)
                    //THIS SATISFIES THE CONDITION IF A MEMBER REMARRIES WE CAN STILL PRESERVE THE ORIGINAL MARRIAGE ID POSSIBLY TIED TO CHILDREN FROM PREVIOUS MARRAIAGE
                    $sql_ifMemberIsTiedToSomeone = "SELECT marriage_id FROM desloge_member_spouse WHERE spouse2_id = ".$memberId." and spouse1_id IS NULL or spouse1_id = ''";
                    $stmt_ifMemberIsTiedToSomeoneResult = $dbh->query($sql_ifMemberIsTiedToSomeone);
                    if($stmt_ifMemberIsTiedToSomeoneResult->rowCount()) {
                        //DELETE INITIAL ROW THAT HAS MEMBER'S ID SO THERE WONT BE TWO MEMBERS RECORDS, WE CANT HAVE TWO ROWS THAT HAVE ONE TIED TO NO SPOUSE AND ONE THAT IS
                        $sql_deleteInitialMemberId = "DELETE FROM desloge_member_spouse WHERE spouse2_id = '".$memberId."'";
                        $stmt_deleteIfBothExistResult = $dbh->query($sql_deleteInitialMemberId);
                    }
                    //SET MEMBER'S ID TO SPOUSES ROW IN SPOUSE 2
                    $stmt_spouse1Result = $dbh->query($sql_spouse1);
                    $sql6 = "UPDATE desloge_member_spouse SET spouse1_id = '".$memberId."' WHERE marriage_id = '".$spouseMarriageID."'";
                    $stmt = $dbh->query($sql6);
                }
            }
        }
    }    
}

function compareChildToParent($dbh, $parentId, $childId) {
    $sqlGetParentMID = "SELECT member_id FROM desloge_family_member WHERE family_id = '".$parentId."'";
    $stmt_result = $dbh->query($sqlGetParentMID);
    
    if ($stmt_result->rowCount()) {
        while($row = $stmt_result->fetch(PDO::FETCH_ASSOC)) {
             $parentMemberID = $row["member_id"];
        }

        $sql_checkForMarriageId = "SELECT marriage_id FROM desloge_member_spouse WHERE (spouse1_id = '".$parentMemberID."' OR spouse2_id = '".$parentMemberID."')";
        $result_marriageId = $dbh->query($sql_checkForMarriageId);
        while($row_marriageId = $result_marriageId->fetch(PDO::FETCH_ASSOC)) {
             $parentMarriageId = $row_marriageId["marriage_id"];
        }
        
        $sqlValidateParentChild = "SELECT children_id FROM desloge_family_children where parent_id = '".$parentMarriageId."' and child_id = '".$childId."'";
        
        $result_sqlValidateParentChild = $dbh->query($sqlValidateParentChild);
        return $result_sqlValidateParentChild;
    }
}
                    
?>
