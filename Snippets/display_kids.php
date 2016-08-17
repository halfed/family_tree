<?php
//INCLUDE CONNECTION TO DB
include 'assets/scripts/db_connection.php';
    
$displayChildren = array();
$parentMemberId = $modx->user->get('id');
$childExists = $_GET['cID'];
//First see if parent is in database to populate parent_id
$sql_parent = "SELECT member_id FROM desloge_family_member WHERE family_id = '".$modx->user->get('id')."'";
$stmt_result = $dbh->query($sql_parent);

if ($stmt_result->rowCount()) {
    // output data of each row
    while($row = $stmt_result->fetch(PDO::FETCH_ASSOC)) {
         $parentMemberID = $row["member_id"];
    }
    //POPULATE HIDDEN FIELD FOR PARENT ID
    $displayChildren['fi.parentId'] = $parentMemberID;
    $sql_parentId = "SELECT marriage_id FROM desloge_member_spouse WHERE (spouse1_id = '".$parentMemberID."' or spouse2_id = '".$parentMemberID."')";
    $stmt_result = $dbh->query($sql_parentId);
    while($row = $stmt_result->fetch(PDO::FETCH_ASSOC)) {
        $parentParentId = $row["marriage_id"];
    }
    $sql_children = "SELECT child_id FROM desloge_family_children WHERE parent_id = '".$parentParentId."'";
    $stmt_results = $dbh->query($sql_children);
    if ($stmt_results->rowCount()) {
        // output data of each row
        while($row = $stmt_results->fetch(PDO::FETCH_ASSOC)) {
            $childMemberId = $row["child_id"];
            $sql_childName = "SELECT first_name, last_name FROM desloge_family_member WHERE member_id = '".$childMemberId."'";
            $children_result = $dbh->query($sql_childName);
            while($rows = $children_result->fetch(PDO::FETCH_ASSOC)) {
                $childFirstName = $rows["first_name"];
                $childLastName = $rows["last_name"];
                $displayChildren['fi.childList'] .= '<div class="columns">
                                            <div class="large-4">
                                                <a href="[[~11? &mID=`'.$parentMemberId.'`&cID=`'.$childMemberId.'`]]" class="button tiny round edit">Edit</a>
                                                '.$childFirstName.' '.$childLastName.'
                                            </div>
                                        </div>';
            }
        }
        if($childExists != "") {
            $displayChildren['fi.childList'] .= '<div class="columns">
                                            <div class="large-4">
                                                <a href="[[~11? &mID=`'.$parentMemberId.'`]]" class="button tiny round addKid">Add Kid</a>
                                            </div>
                                        </div>';
        }
    }
}

$modx->setPlaceholders($displayChildren);
