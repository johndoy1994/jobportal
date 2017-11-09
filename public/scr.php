<?php

ini_set('maximum_execution_time', 0);

$fConn = mysql_connect('localhost','root','password');
$fDb = mysql_select_db('internjo_intown', $fConn);

$tConn = mysql_connect('localhost','root','password');

$fQuery = mysql_query('select * from place_us', $fConn);

while($fState = mysql_fetch_assoc($fQuery)) {
	$tDb = mysql_select_db('provalue_group_inc', $tConn);

	$_q = mysql_query('select id from states where code = "'.$fState['state_code'].'"');
	if(mysql_affected_rows()>0) {
		$row = mysql_fetch_assoc($_q);
		$state_id = $row["id"];

		mysql_query('insert into cities(name, state_id) values("'.$fState["place"].'", '.$state_id.')', $tConn);
	} else {
		echo "Error:"."<br/>";
	}
}

