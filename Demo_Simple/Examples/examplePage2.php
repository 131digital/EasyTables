<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Easy Tables</title>
    <link rel="stylesheet" href="../easyTables/css/easyTable.css" type="text/css">
   	<link rel="stylesheet" href="../easyTables/css/white-gray.css" type="text/css">
   	<script src="../easyTables/js/jquery.js" type="text/javascript"></script>
    <script src="../easyTables/js/bootstrap-modal.js"></script>
   	<script src="../easyTables/js/easyTable.js" type="text/javascript"></script>
</head>
<body>
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
	</br><center><a href="../index.php">Back</a></center>
</body>
</html>