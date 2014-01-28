<!doctype html>
<html>
<?php 
    include("header.php"); 
    echo_header();
?>
    
	<div id="container">
		<h2>Example 2</h2>
		<h3>Features:</h3>
		<ul>
			<li>Including only some of the fields of the table instead of all</li>
			<li>Changing the table size</li>
			<li>Changing the number of results that can be shown</li>
			<li>Option to delete rows disabled</li>
			<li>Columns name changed</li>
			<li>Instead of edit a row inline, a windows will be show</li>
		</ul>
	<div>
	
			<div id="tableContainer"></div>
			<script type="text/javascript">	
				$("#tableContainer").easyTable({
					configFile:"example2.php",
					tableWidth:"550px",
					nPResults:[5,10,20,40,80],
					deleteRow:false,
					useModal:true
				});	
			</script>
		    
		    <br/><table class="cod"><tr><th>HTML and Javascript</th></tr><tr><td>
<pre class="brush: js;">
&ltdiv id="tableContainer">&lt/div&gt
&ltscript type="text/javascript"&gt	
	$("#tableContainer").easyTable({
		configFile:"example2.php", //Name of the php configuration file
		tableWidth:"550px", //Width of the table
		nPResults:[5,10,20,40,80], //Number of result that can be displayed
		deleteRow:false, //Disabling the option to delete rows
		useModal:true //Instead of edit a row inline a windows will be open
	});	
&lt/script&gt
</pre>
			</td></tr></table>
			<table class="cod"><tr><th>PHP Configuration File</th></tr><tr><td>
<pre class="brush: js;">
//DataSource Configuration Parameters
$db='mysql';
$host='host';
$dbname='dbname';
$user="userName";
$pass="password";
$table='books';

//Table fields that will be displayed
$fields=array('isbn','name','audience');

//Changing the name that some fields will show
$colNames=array(
	"isbn"=>"Number",
	"name"=>"Book name"
);
</pre>
			</td></tr></table>
		</div>
	</div>
</body>
</html>