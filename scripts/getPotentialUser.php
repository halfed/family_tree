<?php
    include 'db_connection.php';
    
    $firstName = $_POST["firstname"];
    $lastName = $_POST["lastname"];
    $dobDay = $_POST["dobday"];
    $dobMonth = $_POST["dobmonth"];
    $dobYear = $_POST["dobyear"];
    $dob = $dobYear."-".$dobMonth."-".$dobDay;

    $sql_checkUser = "
        SELECT t1.member_id, t1.first_name, t1.last_name, DATE_FORMAT(t1.dob, '%m/%d/%Y') AS newDob, t4.first_name AS parent_fName, t4.last_name AS parent_lName
        FROM desloge_family_member AS t1  
        LEFT JOIN desloge_family_children AS t2
        ON t1.member_id = t2.child_id 
        LEFT JOIN desloge_member_spouse AS t3
        ON t2.parent_id =  t3.marriage_id
        LEFT JOIN desloge_family_member AS t4
        ON t3.spouse1_id = t4.member_id
        WHERE (t1.first_name like '%".$firstName."%' AND t1.last_name like '%".$lastName."%')
        OR t1.dob = '".$dob."'
    ";

    $rows = array();
    $result = $dbh->query($sql_checkUser);
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
     $rows['members'][] = $row;
    }
    echo json_encode( $rows );
?>
