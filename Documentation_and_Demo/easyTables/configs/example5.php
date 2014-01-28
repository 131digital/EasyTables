<?php
	/*$db='mysql';
	$host='localhost';
	$dbname='test';
	$user="root";
	$pass="qoskA45";
	$query='select concat(concat(a.firs_name, " "), a.last_name) as "Author Name", b.title as "Book Name", b.language as "Lang", b.audience as "Audience" from authors a, books b where a.id_book = b.id';
	*/
	
	$db="sqlite";
	$dbname="../../Examples/Dabatase.s3db";
    $query='select a.firs_name || " " || a.last_name as "Author Name", b.title as "Book Name", b.language as "Lang", b.audience as "Audience" from authors a, books b where a.id_book = b.id';
	
	$fileName="Document Name";
    $csvChar=";";
    $colAlign=array(
            "Lang"=>"center",
            "Audience"=>"center"
        );
?>
