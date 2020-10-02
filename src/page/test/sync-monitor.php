<?php
$db = db::conn(true);
$rs = $db->query('SHOW SLAVE STATUS');
$rs = $rs->fetch(DB::ass);

echo $rs['Slave_IO_Running'], '/', $rs['Slave_SQL_Running'];

