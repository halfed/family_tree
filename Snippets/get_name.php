<?php
include 'assets/scripts/db_connection.php';

$sql = "SELECT member_id, first_name, middle_name, last_name, suffix, nick_name, dob, home_city, home_state, member_generation, member_image FROM desloge_family_member";
$result = $dbh->query($sql);

$members_info;

if ($result->rowCount()) {
    // output data of each row
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        if($row["member_id"] == 1) {
            $hide = "";
        }
        else {
            $hide = "hidden";
        }
        $members_info .= '<div id="member'.$row["member_id"].'" class="individual-member '.$hide.'">';
        $members_info .= '<div class="member-image"><img src="assets/images/'.$row["member_image"].'"/></div>';
        $members_info .= '<div class="member-info">';
        $members_info .= "First Name: " . $row["first_name"] . "<br/> " . "Middle Name: ".$row["middle_name"]."<br/>". "Last Name: " . $row["last_name"]. "<br/>". "Suffix: ". $row["suffix"]."<br/>". "Nick Name: ". $row["nick_name"]."<br/>". "DOB: ". $row["dob"]."<br/>". "Home City: ". $row["home_city"]."<br/>". "Home State: ". $row["home_state"]."<br/>". "Generation: ". $row["member_generation"];
        $members_info .= "</div>";

if($row["member_id"] == 14) {
    $members_info .= '<div class="clearfix"></div>';
}

$sql2 = "select a.member_id, first_name FROM desloge_family_member as a, desloge_member_spouse as b where a.member_id = b.spouse_id AND b.member_id =".$row["member_id"];
$result2 = $dbh->query($sql2);
while($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
        $members_info .= '<div class="spouse-member" style="clear:both">';
        $members_info .= "Spouse: ".'<a href="#" class="'.$row2["member_id"].'" style="color:blue;text-decoration:underline;">'.$row2["first_name"].'</a>';
        $members_info .= '</div>';
}
$sql3 = "select a.member_id, first_name from desloge_family_member as a, desloge_family as b where a.member_id = b.member_id and b.parent_id =".$row["member_id"];
$result3 = $dbh->query($sql3);
if ($result3->rowCount()) {
    while($row3 = $result3->fetch(PDO::FETCH_ASSOC)) {
        $members_info .= '<div class="child-member" style="clear:both">';
        $members_info .= "Child: ".'<a href="#" class="'.$row3["member_id"].'" style="color:blue;   text-decoration:underline;">'.$row3["first_name"].'</a>';
        $members_info .= '</div>';
    }
}
else {
    $members_info .= '<div class="clearfix"></div>';
}
        $members_info .= "</div>";
        


        $members_info .= '<div class="clearfix"></div>';
    }
    echo $members_info;
} else {
    echo "0 results";
}
