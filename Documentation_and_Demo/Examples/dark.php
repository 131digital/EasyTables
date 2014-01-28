<!doctype html>
<html>
<?php 
    include("header.php"); 
    echo_header("dark");
?>
	<div id="container">
		<h2>Example Dark Style</h2>
		<div>
			<div id="tableContainer"></div>
			<script type="text/javascript">	
				$("#tableContainer").easyTable({
					configFile:"example1.php"
				});	
			</script>
		</div>
		<pre class="brush: js;">
	        &ltlink rel="stylesheet" href="easyTables/css/dark.css" type="text/css"&gt
        </pre>
	</div>
</body>
</html>