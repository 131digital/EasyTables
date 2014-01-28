<!doctype html>
<html>
<?php 
    include("header.php"); 
    echo_header();
?>
	<div id="container">
		<h2>Example 1</h2>
		<h3>Features</h3>
		<ul>
			<li>Basic Configuration</li>
		</ul>
		<div>
			<div id="tableContainer"></div>
			<script type="text/javascript">	
				$("#tableContainer").easyTable({
					configFile:"example1.php"
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
    configFile:"example1.php //Name of the php configuration file
  });	
&lt/script&gt
</pre>
			</td></table><table class="cod"><tr><th>PHP configuration file parameters</th></tr><tr><td>
<pre class="brush: js;">
//DataSource Configuration Parameters
$db='mysql';
$host='host';
$dbname='dbname';
$user="userName";
$pass="password";
$table='books';
</pre>
			</td></tr></table>

	</div>
</body>
</html>