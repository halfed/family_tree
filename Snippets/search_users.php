<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST["memberFirstName"] != "" || $_POST["memberLastName"] != ""){
    //INCLUDE CONNECTION TO DB
    include 'assets/scripts/db_connection.php';
    //INCLUDE LIBRARY
    include 'assets/scripts/Library.php';
    
    ///[!include? 'assets/scripts/Library.php']
    $firstName = trim(addslashes($_POST["memberFirstName"]));
    $lastName = trim(addslashes($_POST["memberLastName"]));
    $displayMemberResults = array();
    
    /*$sqlCheckUser = "SELECT member_id, first_name, last_name, dob FROM desloge_family_member WHERE first_name like '%".$firstName."%' AND last_name like '%".$lastName."%'";
    $stmt_resultCheckUser = $dbh->query($sqlCheckUser);*/
    $stmt_resultCheckUser = searchUsers($dbh, $firstName, $lastName);
    if($stmt_resultCheckUser->rowCount()) {
        while($row = $stmt_resultCheckUser->fetch(PDO::FETCH_ASSOC)) {
                $memberId = $row["member_id"];
                $memberFirstName = $row["first_name"];
                $memberLastName = $row["last_name"];
                $memberDob = $row["newDob"];
                $memberParentFName = $row["parent_fName"];
                $memberParentLName = $row["parent_lName"];
                $displayMemberResults['fi.memberList'] .= '
                    <div class="large-11">
                        <div class="row">
                            <div class="columns large-3"> 
                                <a href="[[~2? &mID=`'.$memberId.'`]]" class="button tiny round">Edit</a>
                            </div>
                            <div class="large-3 columns"><label>Member Name: </label>'.$memberFirstName.' '.$memberLastName.'</div>
                            <div class="columns large-3"><label>Member DOB: </label>'.$memberDob.'</div>
                            <div class="columns large-3"><label>Member Parent: </label>'.$memberParentFName.' '.$memberParentLName.'</div>
                        </div>
                    </div>';
        }
    }else {
        $displayMemberResults['fi.memberList'] = '<div class="large-4">
                                                            No Results Found
                                                  </div>';
    }
    $modx->setPlaceholders($displayMemberResults);
}
