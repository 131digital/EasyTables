<!doctype html>
<html>
<?php 
    include("header.php"); 
    echo_header("minimalist");
?>
	<div id="container">
		<h2>Example Minimalist Style</h2>
		<div>
			<div id="tableContainer"></div>
			<script type="text/javascript">	
				$("#tableContainer").easyTable({
					configFile:"minimalist.php"
				});	
			</script>
		</div></br>
		<pre class="brush: js;">
	        &ltlink rel="stylesheet" href="easyTables/css/minimalist.css" type="text/css"&gt
			
			//and in the php configuration file add
			$colOrderColors=array("Even"=>"#3F4EFE","Odd"=>"#3F4EFE");
        </pre>
	</div>
</body>
</html>