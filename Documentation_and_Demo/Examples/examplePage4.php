<!doctype html>
<html>
<?php 
    include("header.php"); 
    echo_header();
?>
	<div id="container">
		<h2>Example 4</h2>
		<h3>Features:</h3>
		<ul>
			<li>Friendly Display</li>
			<li>Changing the default alignments in a column</li>
			<li>Changing the default colors for columns and column headers</li>
			<li>Setting a specific width for each column</li>
			<li>Disabling options for Insert, Delete and Update rows</li>
			<li>Allowing export data only in PDF format</li>
		</ul>
		<div>

			<div id="tableContainer"></div>
			<script type="text/javascript">	
				$("#tableContainer").easyTable({
					configFile:"example4.php",
					exportOptions:["PDF"],
					deleteRow:false,
					newRow:false,
					updateRow:false,
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
    configFile:"example4.php", //Name of the php configuration file
    exportOptions:["PDF"], //Allowing export only in PDF format
    deleteRow:false, //Disabling the option to delete rows
    newRow:false, //Disabling the option to insert a new row
    updateRow:false //Disabling the option to udpdate rows
  });	
&lt/script&gt
</pre>
			</td></tr></table>
			<table class="cod"><tr><th>PHP configuration file parameters</th></tr><tr><td>
			
<pre class="brush: js;">
//DataSource Configuration Parameters
$db='mysql';
$host='host';
$dbname='dbname';
$user="userName";
$pass="password";
$table='products';

//Changing the default column width
$colSizes=array(
	"id"=>"50px",
	"name"=>"100px",
	"description"=>"500px"
);

//Displaying a picture instead of a particular string in a column
$friendlyOutput=array(
  'in_stock'=>array(
	'Y'=>'&ltimg src="easyTables/css/accept.png"&gt',
	'N'=>'&ltimg src="easyTables/css/cancel.png"&gt'
   )
);

//Changing the default color in the odd rows cells of two columns
$colColorsOdd=array(
	"id"=>"#E38F33",
	"price"=>"#E38F33",
);

//Changing the default color in the even rows cells of two columns
$colColorsEven=array(
	"id"=>"#EDCF76",
	"price"=>"#EDCF76",
);

//Changing the default text alignment in two columns
$colAlign=array(
	"price"=>"center",
	"in_stock"=>"center",
);

//Changing the default column header color in three colums
$colHeadColors=array(
	"id"=>"#CF0707",
	"price"=>"#CF0707",
	"name"=>"#1527CA",
);

//Changing the default color of the active sort column
$colOrderColors=array(
	"Even"=>"#7CE1F3",
	"Odd"=>"#3ECAF9"
);

//Changing the default font color of the active sort column
$colOrderFontColors=array(
	"Even"=>"#000",
	"Odd"=>"#000"
);
</pre>
			</td></tr></table>

	</div>
</body>
</html>