<!doctype html>
<html>
<?php 
    include("header.php"); 
    echo_header();
?>
	<div id="container">
		<h2>Example 3</h2>
		<h3>Features:</h3>
		<ul>
			<li>Use a SQL query as data source for the table</li>
			<li>Allows to see the SQL query that the table is showing</li>
			<li>Limiting the number of allowed search fields</li>
			<li>Disabling the option to export data</li>
		</ul>
		<div>
			</td></tr></table>
			<div id="tableContainer"></div>
			<script type="text/javascript">	
				$("#tableContainer").easyTable({
					configFile:"example3.php",
					searchFields:["isbn","title"],
					autoSearch:false,
					exportOptions:false
				});	
			</script>
		</div></br>
       	<table class="cod">
			<tr><th>HTML and Javascript</th></tr>
			<tr><td>
<pre class="brush: js;">
&ltdiv id="tableContainer">&lt/div&gt
&ltscript type="text/javascript"&gt	
	$("#tableContainer").easyTable({
		configFile:"example3.php", //Name of the php configuration file
		searchFields:["isbn","title"], //Restricting the search to only two fields
		autoSearch:false, //Disabling auto search
		exportOptions:false //Disabling the option to Export results
	});	
&lt/script&gt
</pre>
			</td></tr></table><table class="cod"><tr><th>PHP configuration file parameters</th></tr><tr><td>
<pre class="brush: js;">
//DataBase Configuration Parameters
$db='mysql';
$host='host';
$dbname='dbname';
$user="userName";
$pass="password";
$table='books';

//Using a query as sorce of information
$query="select * from books where id>'200116' and id<'200300'";

//Activating debug mode for see the sql queries that are being executed
$debugMode=true;
</pre>
	</div>
</body>
</html>