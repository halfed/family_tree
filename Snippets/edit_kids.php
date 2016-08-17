<?php
if($_GET['cID'] != ""){
    //INCLUDE CONNECTION TO DB
    include 'assets/scripts/db_connection.php';
    
    //INCLUDE LIBRARY
    include 'assets/scripts/Library.php';

    $childInfoPlaceholder = array();
    $childParentValidator = compareChildToParent($dbh, $_GET['mID'], $_GET['cID']);

    if($childParentValidator->rowCount()) {
        //GET CHILD INFO
        $sql = "SELECT first_name, middle_name, last_name, suffix, nick_name, gender, dob, home_city, home_state, member_generation, member_image 
                FROM desloge_family_member 
                WHERE member_id = '".$_GET['cID']."'";
        $result = $dbh->query($sql);
        $sqlChildOrderCheck = "SELECT sibling_order FROM desloge_family_siblings WHERE member_id = '".$_GET['cID']."'";
        $result2 = $dbh->query($sqlChildOrderCheck);
        
        if ($result->rowCount()) {
             // output data of each row
             while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $childInfoPlaceholder['fi.firstName'] = $row["first_name"];
                $childInfoPlaceholder['fi.middleName'] = $row["middle_name"];
                $childInfoPlaceholder['fi.lastName'] = $row["last_name"];
                $childInfoPlaceholder['fi.suffix'] = $row["suffix"];
                $childInfoPlaceholder['fi.nickName'] = $row["nick_name"];
                $childInfoPlaceholder['fi.gender'] = $row["gender"];
                $childInfoPlaceholder['fi.dob'] = $row["dob"];
                $keywordsDate = preg_split('/[\-]+/', $row["dob"]);
                $childInfoPlaceholder['fi.dobYear'] = $keywordsDate[0];
                $childInfoPlaceholder['fi.dobMonth'] = $keywordsDate[1];
                $childInfoPlaceholder['fi.dobDay'] = $keywordsDate[2];
                $childInfoPlaceholder['fi.birthCity'] = $row["home_city"];
                $childInfoPlaceholder['fi.birthState'] = $row["home_state"];
                //$childInfoPlaceholder['fi.memberGeneration'] = $row["member_generation"];
                if($row["member_image"] != "") {
                    $childInfoPlaceholder['fi.memberPhoto'] = $modx->getOption('site_url'). "assets/images/profile/". $row["member_image"];
                    $childInfoPlaceholder['fi.memberImage'] = $row["member_image"];
                }
                $childInfoPlaceholder['fi.childId'] = $_GET['cID'];
             }
             while($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
                 $childInfoPlaceholder['fi.siblingOrder'] = $row2["sibling_order"];
             }
        }
    }
    $modx->setPlaceholders($childInfoPlaceholder);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST["firstName"] != "" && $_POST["lastName"] != ""){
    //INCLUDE CONNECTION TO DB
    include 'assets/scripts/db_connection.php';
    $childId = $_POST["childId"];
    $firstName = trim(addslashes($_POST["firstName"]));
    $lastName = trim(addslashes($_POST["lastName"]));
    $middleName = trim(addslashes($_POST["middleName"]));
    $suffix = trim(addslashes($_POST["suffix"]));
    $nickName = trim(addslashes($_POST["nickName"]));
    $gender = trim(addslashes($_POST["gender"]));
    $dob = $_POST["dob"];
    $birthCity = trim(addslashes($_POST["birthCity"]));
    $birthState = trim(addslashes($_POST["birthState"]));
    $childOrder = trim($_POST["siblingOrder"]);
    $memberGeneration = trim($_POST["memberGeneration"]);
    $siblingOrder = trim($_POST["siblingOrder"]);

    if($_POST["memberImage"] != "") {
        $memberImage = str_replace(' ', '_', trim($_POST["memberImage"]));
    }
    $memberId = $_POST["childId"];
    $parentMemberID = $_POST["parentId"];
    
    $checkParentGen = "SELECT member_generation 
        FROM desloge_family_member 
        WHERE member_id = '".$parentMemberID."'";
    $resultParentGen = $dbh->query($checkParentGen);    
    while($rowParentGen = $resultParentGen->fetch(PDO::FETCH_ASSOC)) {
        $parentGeneration = $rowParentGen["member_generation"];
    }
    $parentGeneration++;
    
    //$sqlChildCheck = "SELECT member_id FROM desloge_family_member WHERE first_name = '".$firstName."' AND last_name = '".$lastName."' AND dob = '".$dob."'";
    //$resultIfChildExists = $dbh->query($sqlChildCheck);
    if ($childId != "") {
        $sqlUpdateChildInfo = "UPDATE desloge_family_member SET first_name = '$firstName', middle_name = '$middleName', last_name = '$lastName', suffix = '$suffix', nick_name = '$nickName', gender = '$gender', dob = '$dob', home_city = '$birthCity', home_state = '$birthState', member_generation = '$parentGeneration', member_image = '$memberImage'
                               WHERE member_id = '$memberId'";
        $updateChildInfoResult = $dbh->query($sqlUpdateChildInfo);
        
        $sqlUpdateChildGeneration = "UPDATE desloge_family SET generation_id = '$parentGeneration', family_id = 1
                               WHERE member_id = '$memberId'";
        $updateChildInfoGeneration = $dbh->query($sqlUpdateChildGeneration);
        
        $sqlUpdateSiblingOrder = "UPDATE desloge_family_siblings SET sibling_order = '$siblingOrder' WHERE member_id = '".$memberId."'";
        $updateChildOrderResult = $dbh->query($sqlUpdateSiblingOrder);
    }
    
    else {
            
        $sql = "INSERT INTO desloge_family_member (first_name, middle_name, last_name, suffix, nick_name, gender, dob, home_city, home_state, member_generation, member_image) 
                VALUES ('$firstName', '$middleName', '$lastName', '$suffix', '$nickName', '$gender', '$dob', '$birthCity', '$birthState', '$parentGeneration', '$memberImage')";
        
        $stmt = $dbh->query($sql);
        
        $memberId = $dbh->lastInsertId();
      
        //Insert into member info table with id from previous insert
        $sql2 = "INSERT INTO desloge_family_member_info (member_id) VALUES ('$memberId')";
        
        $stmt = $dbh->query($sql2);
    
        //Insert member Id into member_family
        $sql3 = "INSERT INTO desloge_family (member_id, family_id, generation_id) VALUES ('$memberId', '1', '$parentGeneration')";
        $stmt = $dbh->query($sql3);
        
        //GET PARENTS MARRIAGE ID WHICH TIES ALL CHILDREN TO PARENT.  THEN WE WILL INSERT THAT ID FOR THE CHILD IN THE SIBLINGS TABLE
        $sql_parentId = "SELECT marriage_id FROM desloge_member_spouse WHERE (spouse1_id = '".$parentMemberID."' or spouse2_id = '".$parentMemberID."')";
        $stmt_result = $dbh->query($sql_parentId);
        while($row = $stmt_result->fetch(PDO::FETCH_ASSOC)) {
            $parentParentId = $row["marriage_id"];
        }
        
        //NOW WE WILL TIE THE PARENT TO THIS NEW CHILD
        $sql6 = "INSERT INTO desloge_family_children (parent_id, child_id, child_order) VALUES ('$parentParentId', '$memberId','$siblingOrder')";
        $stmt = $dbh->query($sql6);
        
        //At some point a member might have siblings
        $sql4 = "INSERT INTO desloge_family_siblings (member_id, member_generation, sibling_order, parent_id) 
                 VALUES ('$memberId', '$memberGeneration', '$childOrder', '$parentParentId')";
        $stmt = $dbh->query($sql4);
    
        //Insert member Id into member_spouse
        //All members initially get inserted(One day they might marry) check if member is in the spouse table as spouse2, if not insert as spouse1
        $sqlCheckIfSpouse = "SELECT marriage_id FROM desloge_member_spouse WHERE (spouse1_id = '".$memberId."' or spouse2_id = '".$memberId."')";
        $resultIfSpouse = $dbh->query($sqlCheckIfSpouse);
        //IF NO RESULTS THEN WE ADD THEM TO SPOUSE1 ROW, OTHERWISE THEY ARE ALLREADY IN AND WE DO NOTHING
        if (!$resultIfSpouse->rowCount()) {
            $sql5 = "INSERT INTO desloge_member_spouse (spouse1_id) VALUES ('$memberId')";
            $stmt = $dbh->query($sql5);
            $childParentId = $dbh->lastInsertId();
        }
        
        //Insert member Id into member_children
        //5/31/2016 taking this out as a new member should not have any kid info until they add kids in the members update profile page
        //At some point a member might have children
        //$sql6 = "INSERT INTO desloge_family_children (parent_id) VALUES ('$childParentId')";
        //$stmt = $dbh->query($sql6);
    }

}else {
    $errorTxt = "Please try again later";
    echo $errorTXt;
}
