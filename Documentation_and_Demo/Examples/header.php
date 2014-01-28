<?php 
    function echo_header($css_file="white-gray"){
        echo 
            '<head>
                <meta charset="utf-8">
                <title>Easy Tables</title>
            	<link rel="stylesheet" href="../styles/style.css" type="text/css">
                <link rel="stylesheet" href="../easyTables/css/easyTable.css" type="text/css">
            	<link rel="stylesheet" href="../easyTables/css/'.$css_file.'.css" type="text/css">
            	<script src="../easyTables/js/jquery.js" type="text/javascript"></script>
                <script src="../easyTables/js/bootstrap-modal.js"></script>
            	<script src="../easyTables/js/easyTable.js" type="text/javascript"></script>
            	<script type="text/javascript" src="../syntaxhighlighter/scripts/shCore.js"></script>
            	<script type="text/javascript" src="../syntaxhighlighter/scripts/shBrushJScript.js"></script>
            	<link type="text/css" rel="stylesheet" href="../syntaxhighlighter/styles/shCoreDefault.css"/>
            	<script type="text/javascript">SyntaxHighlighter.all();</script>
            </head>
            <body>
            	<div id="top"><div>EasyTables</div></div>
            	<ul id="navigation">
                    <li><a href="../index.php">Main</a></li>
                    <li class="sub">
                     
                        <a href="#">Examples</a>
                        <ul>
                            <li><a href="examplePage1.php">Example 1</a></li>
                            <li><a href="examplePage2.php">Example 2</a></li>
                            <li><a href="examplePage3.php">Example 3</a></li>
                            <li><a href="examplePage4.php">Example 4</a></li>
                            <li><a href="example5/examplePage5.php">Example 5</a></li>
                        </ul>
                     
                    </li>
                     
                    <li class="sub">
                        <a href="#">Styles</a>
                        <ul>
                            <li><a href="dark.php">Dark</a></li>
                            <li><a href="white-gray.php">White-Gray</a></li>
                            <li><a href="minimalist.php">Minimalist</a></li>
                        </ul>
                    </li>
                    
                    <li class="sub">
                        <a href="#">Documentation</a>
                        <ul>
                            <li><a href="../Documentation/configuration.html">Configuration</a></li>
                            <li><a href="../Documentation/jsParameters.html">JS Parameters</a></li>
                            <li><a href="../Documentation/phpParameters.html">PHP Parameters</a></li>
                        </ul>
                    </li>
                </ul>';
    }
?>