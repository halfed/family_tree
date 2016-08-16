<?php
//http://familytree.deslogefamily.com/family-tree/modx/assets/scripts/dfCookie.php?fd=dfCookie
if($_GET['df'] === "dfCookie"){
$cookie_name = "dfCookie";
$cookie_value = "true";

setcookie($cookie_name, $cookie_value, time() + (86400 * 1), "/family-tree/modx/"); // 86400 = 1 day
}
else {
    header('Location: http://www.deslogefamily.com');
}
?>
