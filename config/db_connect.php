
<?php
//$host = "ec2-52-1-92-133.compute-1.amazonaws.com";
//$port = "5432";
//$dbname = "d8klmol62f7oi1";
//$user = "djgymmheoobtax";
//$password = "7332bb6610847d90872b35e6ff411d5379628c314f848699b4aff90ac9029e76";

// Establecer la conexión

$conn = pg_connect("host=ec2-52-1-92-133.compute-1.amazonaws.com dbname=d8klmol62f7oi1 user=djgymmheoobtax password=7332bb6610847d90872b35e6ff411d5379628c314f848699b4aff90ac9029e76");

$result = pg_query($conn, "SELECT * FROM tu_tabla");

while ($row = pg_fetch_row($result)) {
  var_dump($row);
}

pg_close($conn);








?>
