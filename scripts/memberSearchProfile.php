<?php
include 'db_connection.php';

$firstName = $_POST["firstname"];
$lastName = $_POST["lastname"];
$occupation = $_POST["occupation"];
$currentState = $_POST["currentstate"];
$generation = $_POST["generation"];
$college = $_POST["college"];

$sql_searchQuery = "SELECT a.member_id, a.first_name, a.middle_name, a.last_name, a.suffix, a.nick_name, TIMESTAMPDIFF(YEAR, a.dob, CURDATE()) AS age, a.dod, a.home_city, a.home_state, a.member_image, b.email, b.phone, 
b.current_city, b.current_state, b.college, b.occupation, b.facebook, b.linkedIn, b.website, b.about_me

FROM desloge_family_member AS a LEFT JOIN desloge_family_member_info AS b

ON a.member_id = b.member_id

WHERE ";
if($firstName !== "") {
    $sql_searchQuery .= "a.first_name like '%".$firstName."%'";
}
if($lastName !== "") {
    if($firstName !== ""){
        $sql_searchQuery .= " AND a.last_name like '%".$lastName."%'";
    }
    else {
        $sql_searchQuery .= "a.last_name like '%".$lastName."%'";
    }
}
if($currentState !== "") {
    if($firstName !== "" || $lastName !== "") {
        $sql_searchQuery .= " AND b.current_state like '%".$currentState."%'";
    }
    else {
        $sql_searchQuery .= "b.current_state like '%".$currentState."%'";
    }
}
if($generation !== "") {
    if($firstName !== "" || $lastName !== "" || $currentState !== "") {
        $sql_searchQuery .= " AND a.member_generation = '".$generation."'";
    }
    else {
        $sql_searchQuery .= "a.member_generation = '".$generation."'";
    }
}
if($occupation !== "") {
    if($firstName !== "" || $lastName !== "" || $currentState !== "" || $generation !== "") {
        $sql_searchQuery .= " AND b.occupation like '%".$occupation."%'";
    }
    else {
        $sql_searchQuery .= "b.occupation like '%".$occupation."%'";
    }
}
if($college !== "") {
   if($firstName !== "" || $lastName !== "" || $currentState !== "" || $generation !== "" || $occupation !== "") {
       $sql_searchQuery .= " AND b.college like '%".$college."%'";
   } 
   else {
       $sql_searchQuery .= "b.college like '%".$college."%'";
   }
}

$rows = array();
$result = $dbh->query($sql_searchQuery);
while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $rows['memberResults'][] = $row;
}

echo json_encode( $rows );

?>
