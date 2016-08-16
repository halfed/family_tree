<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST["parentFName"] != "" && $_POST["parentLName"] != "" && $_POST["childFirstName"] != "" && $_POST["childLastName"] != "" && $_POST["childMemberGeneration"] != "") {
    //INCLUDE CONNECTION TO DB
    include 'assets/scripts/db_connection.php';
    
    $editChildren = array();
    $childrenForm = array();
    $pFirstName = $_POST["parentFName"];
    $pLastName = $_POST["parentLName"];
    $pDob = $_POST["parentDob"];
    $cFirstName = $_POST["childFirstName"];
    $cLastName = $_POST["childLastName"];
    $cMiddleName = $_POST["childMiddleName"];
    $cSuffix = $_POST["childSuffix"];
    $cNickName = $_POST["childNickName"];
    $cDob = $_POST["childDob"];
    $cBirthCity = $_POST["childBirthCity"];
    $cBirthState = $_POST["childBirthState"];
    $childMemberGeneration = $_POST["childMemberGeneration"];
    $cMemberImage = $_POST["childMemberImage"];
    
    $sql = "INSERT INTO desloge_family_member (first_name, middle_name, last_name, suffix, nick_name, dob, home_city, home_state, member_generation) VALUES ('$cFirstName', '$cMiddleName', '$cLastName', '$cSuffix', '$cNickName', '$cDob', '$cBirthCity', '$cBirthState', '$childMemberGeneration')";
    
    $stmt = $dbh->query($sql);

    //Get last inserted member id
    $cMemberId = $dbh->lastInsertId();
    
    //Insert member Id into member_family
    $sql3 = "INSERT INTO desloge_family (member_id, family_id, generation_id) VALUES ('$cMemberId', '1', '$childMemberGeneration')";
    $stmt = $dbh->query($sql3);

    //Insert member Id into member_siblings
    //First see if parent is in database to populate parent_id
    $sql_parent = "SELECT member_id FROM desloge_family_member WHERE first_name = '".$pFirstName."' AND last_name = '".$pLastName."' AND dob = '".$pDob."'";
    $stmt_result = $dbh->query($sql_parent);
    
    //At some point a member might have siblings
    $sql4 = "INSERT INTO desloge_family_siblings (member_id, member_generation) VALUES ('$cMemberId', '$childMemberGeneration')";
    $stmt = $dbh->query($sql4);

    if ($modx->getCount('modResource', $stmt_result) > 0) {
        // output data of each row
        while($row = $stmt_result->fetch(PDO::FETCH_ASSOC)) {
            $parentMemberID = $row["member_id"];
        }
        $sql_parentId = "SELECT marriage_id FROM desloge_member_spouse WHERE (spouse1_id = '".$parentMemberID."' or spouse2_id = '".$parentMemberID."')";
        $stmt_result = $dbh->query($sql_parentId);
        while($row = $stmt_result->fetch(PDO::FETCH_ASSOC)) {
            $parentParentId = $row["marriage_id"];
        }
        $sql_parent_insert = "UPDATE desloge_family_siblings SET parent_id = '".$parentParentId."' WHERE member_id = '".$cMemberId."'";
        $stmt = $dbh->query($sql_parent_insert);
    }

    //Insert member Id into member_spouse
    //All members initially get inserted(One day they might marry and have kids, so they will need to add parent id for their possible children) check if member is in the spouse table as spouse2, if not insert as spouse1
    $sql5 = "INSERT INTO desloge_member_spouse (spouse1_id) VALUES ('$memberId')";
    $stmt = $dbh->query($sql5);
    $cParentId = $dbh->lastInsertId();
    
    //Insert member Id into member_children
    //At some point a member might have children
    $sql6 = "INSERT INTO desloge_family_children (parent_id) VALUES ('$cParentId')";
    $stmt = $dbh->query($sql6);
}else {
    $errorTxt = "Please try again later";
    echo $errorTXt;
}
/*$editChildren['fi.hello'] = "hello";
$childrenForm['fi.display'] = "show";
$modx->setPlaceholders($editChildren);

$modx->setPlaceholders($childrenForm);*/
