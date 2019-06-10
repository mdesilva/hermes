<?php
/*
Simplified secure form submission with validation.
*/
include("class-form.php");
$form = new FormValidator(file_get_contents("php://input"));
$form->transmit();
 ?>
