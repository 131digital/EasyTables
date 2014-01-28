<!doctype html>
<html>
<head>
                <meta charset="utf-8">
                <title>Easy Tables</title>
            	<link rel="stylesheet" href="../../styles/style.css" type="text/css">
                <link rel="stylesheet" href="../../easyTables/css/easyTable.css" type="text/css">
            	<link rel="stylesheet" href="../../easyTables/css/white-gray.css" type="text/css">
            	<script src="../../easyTables/js/jquery.js" type="text/javascript"></script>
                <script src="../../easyTables/js/bootstrap-modal.js"></script>
            	<script src="../../easyTables/js/easyTable.js" type="text/javascript"></script>
            	<script type="text/javascript" src="../../syntaxhighlighter/scripts/shCore.js"></script>
            	<script type="text/javascript" src="../../syntaxhighlighter/scripts/shBrushJScript.js"></script>
            	<link type="text/css" rel="stylesheet" href="../../syntaxhighlighter/styles/shCoreDefault.css"/>
            	<script type="text/javascript">SyntaxHighlighter.all();</script>
            </head>
            <body>
            	<div id="top"><div>EasyTables</div></div>
            	<ul id="navigation">
                    <li><a href="../../index.php">Main</a></li>
                    <li class="sub">
                     
                        <a href="#">Examples</a>
                        <ul>
                            <li><a href="../examplePage1.php">Example 1</a></li>
                            <li><a href="../examplePage2.php">Example 2</a></li>
                            <li><a href="../examplePage3.php">Example 3</a></li>
                            <li><a href="../examplePage4.php">Example 4</a></li>
                            <li><a href="examplePage5.php">Example 5</a></li>
                        </ul>
                     
                    </li>
                     
                    <li class="sub">
                        <a href="#">Styles</a>
                        <ul>
                            <li><a href="../dark.php">Dark</a></li>
                            <li><a href="../white-gray.php">White-Gray</a></li>
                            <li><a href="../minimalist.php">Minimalist</a></li>
                        </ul>
                    </li>
                    
                    <li class="sub">
                        <a href="#">Documentation</a>
                        <ul>
                            <li><a href="../../Documentation/configuration.html">Configuration</a></li>
                            <li><a href="../../Documentation/jsParameters.html">JS Parameters</a></li>
                            <li><a href="../../Documentation/phpParameters">PHP Parameters</a></li>
                        </ul>
                    </li>
                </ul>
	<div id="container">
		<h2>Example 5</h2>
		<h3>Features:</h3>
		<ul>
			<li>Using a complex SQL query as data source for the table</li>
			<li>Changing the default name of the export files</li>
			<li>Changing the default CSV character used in the CSV export option</li>
		</ul>
		<div>
			</td></tr></table>
			<div id="tableContainer"></div>
			<script type="text/javascript">	
				$("#tableContainer").easyTable({
					configFile:"example5.php",
					phpRute:"../../easyTables/php/",
					multipleTableQuery:true,
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
	    configFile:"example5.php", //Name of the php configuration file
	    phpRute:"../../easyTables/php/", //Working in a diferent directory level
		multipleTableQuery:true, /*You need to enable this option when you 
		                            are using a complex sql query as source of data*/
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

//Using a query as sorce of information
$query='select concat(concat(a.firs_name, " "), a.last_name) as "Author Name", 
        b.title as "Book Name", b.language as "Lang", b.audience as "Audience" 
        from authors a, books b where a.id_book = b.id';
        
$fileName="Document Name"; //Changing the default export document name
$csvChar=";"; //Changing the default CSV file character

//Changing the default text aligment in two columns
$colAlign=array(
            "Lang"=>"center",
            "Audience"=>"center"
        );
</pre>
	</div>
</body>
</html>