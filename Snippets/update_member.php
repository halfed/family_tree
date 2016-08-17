<?php
//FOR MEMBERS THERE IS NO INSERT SCRIPT AS WHEN THEY FIRST GET AN ACCOUNT A RECORD IS THEN INSERTED FOR THAT MEMBER
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST["firstName"] != "" && $_POST["lastName"] != "" && $_POST["memberGeneration"] != ""){
    $modxUserId = $_POST["uId"];
    $firstName = trim(addslashes($_POST["firstName"]));
    $lastName = trim(addslashes($_POST["lastName"]));
    $middleName = trim(addslashes($_POST["middleName"]));
    $suffix = trim(addslashes($_POST["suffix"]));
    $nickName = trim(addslashes($_POST["nickName"]));
    $gender = trim(addslashes($_POST["gender"]));
    $dobDay = $_POST["dobDay"];
    $dobMonth = $_POST["dobMonth"];
    $dobYear = $_POST["dobYear"];
    $dob = $dobYear."-".$dobMonth."-".$dobDay;
    $birthCity = trim(addslashes($_POST["birthCity"]));
    $birthState = trim(addslashes($_POST["birthState"]));
    $memberGeneration = trim($_POST["memberGeneration"]);
    
    $parentFName = trim(addslashes($_POST["parentFName"]));
    $parentLName = trim(addslashes($_POST["parentLName"]));
    $parentDob = $_POST["parentDob"];
  
    $bornIntoFamily = $_POST["bornIntoFamily"];
    if(!$bornIntoFamily == 1){
        $bornIntoFamily = 0;
    }
    $haveSpouse = $_POST["haveSpouse"];
    $spouseFName = trim(addslashes($_POST["spouseFName"]));
    $spouseLName = trim(addslashes($_POST["spouseLName"]));
    $spouseDob = $_POST["spouseDob"];
    
    if($_POST["memberImage"] != "") {
        $memberImage = str_replace(' ', '_', trim($_POST["memberImage"]));
    }
    
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

    //INCLUDE CONNECTION TO DB
    include 'assets/scripts/db_connection.php';
    //INCLUDE LIBRARY
    include 'assets/scripts/Library.php';
    
    //THIS FUNCTION BELOW COMES FROM LIBRARY.PHP, CUSTOM LIBRARY IN ASSETS FOLDER
    $resultCheckUser = checkMemberExists($dbh, $modxUserId);
    while($row = $resultCheckUser->fetch(PDO::FETCH_ASSOC)) {
            $memberId = $row["member_id"];
    }

    $sql = "UPDATE desloge_family_member 
            SET first_name = '$firstName', middle_name = '$middleName', last_name = '$lastName', suffix = '$suffix', 
            nick_name = '$nickName', gender = '$gender', dob = '$dob', home_city = '$birthCity', home_state = '$birthState', 
            member_generation = '$memberGeneration', member_image = '$memberImage'  
            WHERE member_id = '$memberId'";
    
    $stmt = $dbh->query($sql);

    //Insert into member info table with id from previous insert
    $sql2 = "UPDATE desloge_family_member_info 
             SET email = '$email', phone = '$phone', current_city = '$currentCity', current_state = '$currentState', college = '$college', 
             occupation = '$occupation', facebook = '$sanitizedFaceBook', linkedin = '$sanitizedLinkedIn', website = '$sanitizedWebsite', about_me = '$aboutMe'
             WHERE member_id = '$memberId'";
    
    $stmt = $dbh->query($sql2);

    //Insert member Id into member_family
    $sql3 = "UPDATE desloge_family SET family_id = '$bornIntoFamily', generation_id = '$memberGeneration' WHERE member_id = '$memberId'";
    $stmt = $dbh->query($sql3);
    
    //UPDATE SIBLING STATUS
    $sql4 = "UPDATE desloge_family_siblings SET member_generation = '$memberGeneration' WHERE member_id = '$memberId'";
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
    }else {
        //USER DOES NOT HAVE A SPOUSE BUT STILL NEED TO ADD A RECORD FOR ONE DAY THEY MIGHT MARRY AND NEED TO TIE A MEMBER TO THEM
        $sql_checkForMarriageId = "SELECT marriage_id FROM desloge_member_spouse WHERE (spouse1_id = '".$memberId."' OR spouse2_id = '".$memberId."')";
        $resultCheckForMarriageId = $dbh->query($sql_checkForMarriageId);
        if (!$resultCheckForMarriageId->rowCount()) {
            $sql7 = "INSERT INTO desloge_member_spouse (spouse1_id) VALUES ('$memberId')";
            $stmt = $dbh->query($sql7);
        }
    }
    
    $userProfile = $modx->user->getOne('Profile');
    $LoginCount = $userProfile->get('logincount');
    if($LoginCount == 1) {
        $LoginCount++;
        $sql6 = "UPDATE modx_user_attributes SET logincount = '".$LoginCount."' WHERE id = '".$modxUserId."'";
        $stmt = $dbh->query($sql6);
    }    

}else {
    $errorTxt = "Please try again later";
    echo $errorTXt;
}
