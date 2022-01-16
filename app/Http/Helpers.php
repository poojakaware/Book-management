<?php

date_default_timezone_set('Asia/Calcutta'); 
function calculateDueDate($book_due_days)
{

	// add  days to date
	$NewDate=Date('Y-m-d', strtotime("+$book_due_days days"));
	return $NewDate;
}


?>