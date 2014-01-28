<!doctype html>
<html>
<?php 
    include("header.php"); 
    echo_header("white-gray");
?>
	<div id="container">
		<h2>Example White-Gray Style</h2>
		<div>
			<div id="tableContainer"></div>
			<script type="text/javascript">	
				$("#tableContainer").easyTable({
					configFile:"example1.php"
				});	
			</script>
		</div>
		<pre class="brush: js;">
	        &ltlink rel="stylesheet" href="easyTables/css/white-gray.css" type="text/css"&gt
        </pre>
	</div>
</body>
</html>