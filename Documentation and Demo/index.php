<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>EasyTables</title>
	<link rel="stylesheet" href="styles/style.css" type="text/css">
    <link rel="stylesheet" href="easyTables/css/easyTable.css" type="text/css">
	<link rel="stylesheet" href="easyTables/css/white-gray.css" type="text/css">
	<script src="easyTables/js/jquery.js" type="text/javascript"></script>
    <script src="easyTables/js/bootstrap-modal.js"></script>
	<script src="easyTables/js/easyTable.js" type="text/javascript"></script>
	<script type="text/javascript" src="syntaxhighlighter/scripts/shCore.js"></script>
	<script type="text/javascript" src="syntaxhighlighter/scripts/shBrushJScript.js"></script>
	<link type="text/css" rel="stylesheet" href="syntaxhighlighter/styles/shCoreDefault.css"/>
	<script type="text/javascript">SyntaxHighlighter.all();</script>
</head>
<body>
	<div id="top"><div>EasyTables</div></div>
    	<ul id="navigation">
            <li><a href="index.php">Main</a></li>
            <li class="sub">
             
                <a href="#">Examples</a>
                    <ul>
                        <li><a href="Examples/examplePage1.php">Example 1</a></li>
                        <li><a href="Examples/examplePage2.php">Example 2</a></li>
                        <li><a href="Examples/examplePage3.php">Example 3</a></li>
                        <li><a href="Examples/examplePage4.php">Example 4</a></li>
						<li><a href="Examples/example5/examplePage5.php">Example 5</a></li>
                    </ul>
                 
            </li>
                     
            <li class="sub">
                <a href="#">Styles</a>
                    <ul>
                        <li><a href="Examples/dark.php">Dark</a></li>
                        <li><a href="Examples/white-gray.php">White-Gray</a></li>
                        <li><a href="Examples/minimalist.php">Minimalist</a></li>
                    </ul>
            </li>
            <li class="sub">
             
                <a href="#">Documentation</a>
                    <ul>
                        <li><a href="Documentation/configuration.html">Configuration</a></li>
                        <li><a href="Documentation/jsParameters.html">JS Parameters</a></li>
                        <li><a href="Documentation/phpParameters.html">PHP Parameters</a></li>
                    </ul>
                 
            </li>
        </ul>
<div id="container">
    <h3>Description:</h3>
    <p>This script allows you to show, export, and edit the information of a database in a easy and interactive way and with several configuration options. You only need to write a few lines of code and you'll have a wonderful display of the data in a table. You'll have a table with options to do searchs, change the number of rows to be shown, sort the data according to a column, export the information and XML, CSV, Excel and PDF files, edit the info inline, etc.</p>
	<h3>Features:</h3>
	<ul>    
	    <li>You can use the script with MySQL, PostgreSQL, SQLServer and SQLite.</li>
	    <li>The script use PDO for connect to the database.</li>
	    <li>Protected agains SQL injection.</li>
	    <li>You only need to set the connection parameters and select a table as source of information to use it.</li>
	    <li>You can use a SQL query as information source. It allows you to display join queries and almost all sql queries result.</li>
	    <li>It offers several parameters to customize the way the information is displayed. Ej. Which columns will be displayed, the column header name that will be shown, the width and color of each column, etc.</li>
	    <li>Use ajax for a more interactive application.</li>
	    <li>Allows you to export the data in XLS, CSV, PDF and Excel file with only a  click.</li>
	    <li>You can configure a friendly display for the information.</li>
	    <li>You can delete, insert and update the information in a interactive way.</li>
	    <li>Very interactive tables with pager and sort options.</li>
	    <li>Several predesigned css files.</li>
	    <li>Easy to configure.</li>
	    <li>Commented code, easy to change.</li>
	</ul>
	<div style='border:1px solid #000; padding:15px; margin-top:50px;'>
        <h3>Example:</h3>
    	<div id="identifier"></div>
    	<script type="text/javascript">	
    		$("#identifier").easyTable({
    		    phpRute:"easyTables/php/",
    			configFile:"config.php",
    			tableWidth:"800px",
    			//nPResults:[5,10,20,40,80], //none for desactivate this option
    			//searchFields:["id"]//false for desactivate this option
    			//autoSearch:false, //Activate ajax search
    			//useModal:true, //Use or don´t use modals
    			//textInSingleRow:false,
    			//MultipleTableQuery:false,
    			//newRow:false,
    			//updateRow:false,
    			//deleteRow:false,
    			//exportOptions:false
    			//onDBClick:'details'
    		});	
    	</script>
    </div>
    <div style="text-align:right"><p><i>Double click in a row for edit, click in a header to sort table.</i></p></div>
</div>
</body>
</html>