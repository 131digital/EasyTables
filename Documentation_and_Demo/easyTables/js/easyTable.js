


$.prototype.easyTable=function (opciones){
	
	//=========================DEFAULT VALUES============================
	
	var phpRute="../easyTables/php/"; //Path of the directory where the php files are. This path must be relative from the php file where you are using this script.
	var nResults=10; //Number of rows to be displayed in the table
	var configFile="../configs/"; //Path of the php file that has the database connections options
	var searchFields=true; //Array wth the Fields where the search will be allowed
	var nPResults=new Array(10,20,30); //Possible number of results to be showns
	var autoSearch=true; //Enable-Disable the autosearch
	var useModal=false; //Enable a new windows for add a create or update a row
	var tableWidth="800px"; //Width of the table (It can be a number in px or a percent)
	var textInSingleRow=true; //Force to the table to shown only a line for each row
	var newRow=true; //Enable the creation of new rows
	var updateRow=true; //Enable the option to save changes in one or several rows
	var deleteRow=true; //Enable the option to delete rows
	var onDBClick="default"; //Option that will be executed when double click in a row (By default it will activate the option to update a row if update is enable or to show detaills of a row)
	var exportOptions=new Array("csv","excel","pdf","xml");	//All the download options that will be shown
	var multipleTableQuery=false;	//Allows to do a multiple table join query
	
	var pageNumber=0;
	var orderField="";
	var sense="";
	var less=4;
	var tr=new Array();
	var showCheck=0;
	var firstFlagMode=true;
	
	option=opciones;
	container=$(this);
	
	$(document).ready(init);
	
	function init(){
	    //In this function the default values of several parameters are changed according to the values settled by the user
		configFile=configFile+option.configFile;
		if(typeof(option.phpRute)!="undefined") phpRute=option.phpRute;
		if(typeof(option.nPResults)!="undefined"){ 
			nResults=option.nPResults[0];
			nPResults=option.nPResults;
		}
		if(typeof(option.searchFields)!="undefined") searchFields=option.searchFields;
		if(typeof(option.autoSearch)!="undefined") autoSearch=option.autoSearch;
		if(typeof(option.useModal)!="undefined") useModal=option.useModal;
		if(typeof(option.tableWidth)!="undefined") tableWidth=option.tableWidth;
		if(typeof(option.textInSingleRow)!="undefined") textInSingleRow=option.textInSingleRow;
		if(typeof(option.newRow)!="undefined") newRow=option.newRow;
		if(typeof(option.updateRow)!="undefined") updateRow=option.updateRow;
		if(typeof(option.deleteRow)!="undefined") deleteRow=option.deleteRow;
		if(typeof(option.multipleTableQuery)!="undefined"){
			multipleTableQuery=option.multipleTableQuery;
			if(multipleTableQuery){
				newRow=false;
				updateRow=false;
				deleteRow=false;
			}
		}
		if(typeof(option.onDBClick)!="undefined") onDBClick=option.onDBClick;
		if(typeof(option.exportOptions)!="undefined") exportOptions=option.exportOptions;
		if(deleteRow)	showCheck=1;
		if(updateRow)	showCheck=2;
		if((!updateRow)&&(!deleteRow)){
			firstFlagMode=false;
		}
		//The form is created and placed in the container in the web page
		container.html('<form class="easyTableForm" action="'+phpRute+'exportFile.php" method="post"><div class="updateModal modal hide fade"></div><table><tr class="search"><tr><tr><td colspan="2" class="result"></td></tr></table>'+
			((exportOptions!=false)?'<input type="hidden" name="orderField"><input type="hidden" name="sense"><input type="hidden" name="configFile" value="'+configFile+'"><input type="hidden" name="actual">':'')+'</form>');
		showSearch(); 
		showTable();
	}
	
	function showTable(){
	    //This function do an ajax call with several parameters. Then it send the result of the ajax call to the function "showData"
	    $.ajax({
			type:"GET",
			url:phpRute+"generateTable.php", 
			data:{"configFile":configFile, "search":((searchFields!=false)?container.find(".searchValue").val():""), "searchField":((searchFields!=false)?container.find(".searchField").val():""), "orderField":orderField, "sense":sense, "nResults":nResults, "showCheck":showCheck}, 
			success:function(data){showData(data); container.find(".search").show()},
			async:false,
			error:function(){alert("There is a problem in the Ajax call!! \nMaybe the relative path to the php directory of easyTables is not correc. Use or change the phpRute JS Parameter");}
		});
	}
	
	function paintRow(){
	    //This function add the class "painted" to a row over which the pointer is.
		classNumber=$(this).attr("class");
		classNumber=classNumber.replace(' updt','');
		container.find("tr."+classNumber+" td[style]").addClass("painted");
		container.find("tr."+classNumber+" td i").addClass("icon-white");
	}
	
	function unpaintRow(){
	    //This function remove the class "painted" classes when the pointer moves from over a row
		classNumber=$(this).attr("class");
		classNumber=classNumber.replace(' updt','');
		if(container.find("input[type='checkbox']."+classNumber).prop('checked')!=true){
			container.find("tr."+classNumber+" td[style]").removeClass("painted");
			container.find("tr."+classNumber+" td i").removeClass("icon-white");
		}
	}
	
	function paintRowChecked(){
	    //This function add the class "painted" to a checked row 
		classNumber=$(this).attr("class");
		classNumber=classNumber.replace(' painted','');
		if(container.find("input[type='checkbox']."+classNumber).prop('checked')==true){
			container.find("tr."+classNumber).addClass("painted");
			container.find("tr."+classNumber+" td[style]").addClass("painted");
			container.find("tr."+classNumber+" i").addClass("icon-white");
		}else{
			container.find("tr."+classNumber).removeClass("painted");
			container.find("tr."+classNumber+" td[style]").removeClass("painted");
		}
	}
	
	function selectAll(){
	    //This function add the property checked to all the rows. If the rows are already checked it removes the property
		if(container.find("th.control input[type='checkbox']").prop('checked')==true){
			container.find("td.control input[type='checkbox']").prop('checked',true);
			for(i=1;i<=nResults;i++){
				container.find("tr."+i).addClass("painted");
				container.find("tr."+i+" td[style]").addClass("painted");
				container.find("tr."+i+" i").addClass("icon-white");
			}
		}else{
			container.find("td.control input[type='checkbox']").removeAttr('checked');
			for(i=1;i<=nResults;i++){
				container.find("tr."+i).removeClass("painted");
				container.find("tr."+i+" td[style]").removeClass("painted");
				container.find("tr."+i+" td i").removeClass("icon-white");
			}
		}
	}
	
	function sortTable(field){
	    //This function take place when you click a table header and sort the table according to the column clicked.
		if(orderField==field){
			if((sense=="desc")||(sense=="")) 
				sense="asc";
			else
				sense="desc";
		}else
			sense="asc";
		orderField=field;
		if(exportOptions!=false){
			container.find("input[name='orderField']").val(orderField);
			container.find("input[name='sense']").val(sense);
		}
		changePager(pageNumber, pageNumber);
	}
	
	function showDetails(classNumber){
	    //This function show a modal windows to show the detaills of a selected row
		objects=container.find("tr."+classNumber+" td");
		var values=new Array(objects.size()-1);
		i=0;
		firstFlag=firstFlagMode;
		objects.each(function(e) {
			if(firstFlag==false){
				values[i]=$(this).html();
				i++;
			}else
				firstFlag=false;
		});
		i=0;
		objects=container.find("tr[class='header'] th");
		firstFlag=firstFlagMode;
		var titles=new Array(objects.size()-1);
		var sizes=new Array(objects.size()-1);
		objects.each(function(e) {
			if(firstFlag==false){
				titles[i]=$(this).attr("name");
				sizes[i]=$(this).width();
				i++;
			}else
				firstFlag=false;
		});
		showModal("Details",titles,values);
	}
	
	function showUpdate(classNumber){
	    //This funtion allows you edit a selected row
		classNumber=classNumber.replace(' painted','');
		tr[classNumber]=container.find("tr."+classNumber).html();
		objects=container.find("tr."+classNumber+" td");
		var values=new Array(objects.size()-1);
		i=0;
		firstFlag=firstFlagMode;
		objects.each(function(e) {
			if(firstFlag==false){
				values[i]=((typeof($(this).attr('value'))!="undefined")?$(this).attr('value'):$(this).html());
				i++;
			}else
				firstFlag=false;
		});
		i=0;
		objects=container.find("tr[class='header'] th");
		firstFlag=firstFlagMode;
		var titles=new Array(objects.size()-1);
		var sizes=new Array(objects.size()-1);
		objects.each(function(e) {
			if(firstFlag==false){
				titles[i]=$(this).attr("name");
				sizes[i]=$(this).width();
				i++;
			}else
				firstFlag=false;
		});
		//If you have set useModal=true the a windows will be open to edit the row
		if(useModal){
			showModal("Update", titles, values);
		}else{//If you don't then you'll edit the row inline
			container.find("tr."+classNumber).removeClass('painted');
			container.find("tr."+classNumber).addClass("updt");
			container.find("tr."+classNumber).prop("title","You can edit this row");
			div="<td class='control'><span title='cancel' class='"+classNumber+"'><i class='icon-remove icon-white'></i></span></td>";
			for(i=0;i<values.length-1;i++){
				div+="<td><input type='text' value='"+values[i].replace(/'/g,"&#39;")+"' id='"+titles[i].replace(/'/g,"&#39;")+"' style='width:"+(sizes[i]-less)+"px'></td>";
			}
			div+="<td class='control2'><span title='save' class='"+classNumber+"'><i class='icon-ok icon-white'></i></span></td>";
			container.find("tr."+classNumber).html(div);
			container.find("tr."+classNumber+" span[title='save']").click(function(e){updRow(values, titles, classNumber)});
			container.find("tr."+classNumber+" span[title='cancel']").click(function(e){
				container.find("tr."+classNumber).html(tr[classNumber]);
				container.find("tr."+classNumber+" td span[title='edit']").click(function(e){showUpdate($(this).attr("class"));});
				container.find("tr."+classNumber+" td span[title='see']").click(function(e){showDetails($(this).attr("class"));});
				container.find("tr."+classNumber).removeClass("updt");
				container.find("tr."+classNumber).removeProp("title");
				container.find("td.control input[type='checkbox']").change(paintRowChecked);
			});
		}
	}
	
	function showMultipleUpdate(){
	    //This function allows you edit one or several rows at once
		objects=container.find("td.control input[type='checkbox']");
		var rows=new Array();
		i=0;
		objects.each(function(e) {
			if($(this).prop("checked")==true){
				rows[i]=$(this).attr("class");
				rows[i]=rows[i].replace(' painted','');
				i++;
			}
		});
		if(i!=0){
			i=0;
			objects=container.find("tr[class='header'] th");
			var titles=new Array(objects.size()-1);
			firstFlag=firstFlagMode;
			objects.each(function(e) {
				if(firstFlag==false){
					titles[i]=$(this).attr("name");
					i++;
				}else
					firstFlag=false;
			});
			var allRows=new Array(rows.length);
			for(i=0;i<rows.length;i++){
				objects=container.find("tr."+rows[i]+" td");
				firstFlag=firstFlagMode;
				allRowsJ=new Array(objects.length-1);
				j=0;
				objects.each(function(e) {
					if(firstFlag==false){
						allRowsJ[j]=$(this).html();
						j++;
					}else
						firstFlag=false;
				});
				allRows[i]=allRowsJ;
			}
			var equal=Array();
			for(i=0;i<allRows.length;i++){
				for(j=0;j<allRows[0].length;j++){
					if(i!=0){
						if(equal[j]!=allRows[i][j])
							equal[j]="";
					}else{
						equal[j]=allRows[i][j];
					}
				}
			}
			showModal('Update Selected Rows',titles,equal);
		}else{
			alert("You have to select at least a row!");
		}
		
	}
	
	function showModal(){
	    //This function construct a modal windows for show details, insert and update one or several rows
		div='<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h3>'+arguments[0]+'</h3></div><div class="modal-body"><div class="problem"></div><table'+((arguments[0]=="Details")?' class="dt"':'')+'>\n';
		titles=arguments[1];
		if(arguments.length==3){
			values=arguments[2];
			for(i=0;i<values.length-1;i++){
				if(arguments[0]!="Details")
					content="<input type='text' value='"+values[i].replace(/'/g,"&#39;")+"' class='"+titles[i].replace(/'/g,"&#39;")+"'>";
				else
					content=values[i].replace(/'/g,"&#39;");
				div=div+"<tr><td><strong>"+titles[i]+":</strong></td><td>"+content+"</td></tr>\n";
			}
		}else{
			for(i=0;i<titles.length-1;i++)
				div=div+"<tr><td>"+titles[i]+"</td><td><input type='text' id='"+titles[i].replace(/'/g,"&#39;")+"'></td></tr>\n";
		}
		div=div+"</table></div><div class='modal-footer'>"+
			((arguments[0]!="Details")?"<a class='updButton button icon approve'>Save</a>":"")+
			" <a class='cancButton button icon remove'>Cancel</a></div>";
		container.find(".updateModal").html(div);
		container.find(".updateModal").modal('show');
		if(arguments.length==3){
			if(arguments[0]=="Update"){
				container.find(".updButton").click(function(e){updRow(values, titles)});
			}else if(arguments[0]!="Details")
				container.find(".updButton").click(function(e){updateSelectedRows(values, titles)});
		}else
			container.find(".updButton").click(function(e){insertRow(titles)});
		container.find(".cancButton").click(function(e){
			container.find(".updateModal").modal('hide'); 
			container.find(".updateModal").html("");
		});
	}
	
	function updateSelectedRows(values,titles){
	    //This function save the changes made in one or several rows selected
		objects=container.find("td.control input[type='checkbox']");
		var rows=new Array();
		i=0;
		objects.each(function(e) {
			if($(this).prop("checked")==true){
				rows[i]=$(this).attr("class");
				i++;
			}
		});
		i=0;
		objects=container.find(".updateModal input[type='text']");
		newValues=new Array(objects.length);
		objects.each(function(e) {
			newValues[i]=$(this).val();
			i++;
		});
		next=false;
		j=0;
		//Compares if there are some changes
		for(i=0;i<newValues.length;i++){
			if(values[i]!=newValues[i]){
				next=true;
				j++;
			}
		}
		if(next){//If there are changes
			if(confirm("Do you really want to change these values?")){
				changes=new Array(j);
				j=0;
				for(i=0;i<newValues.length;i++){
					if(values[i]!=newValues[i]){
						changes[j]=i;
						j++;
					}
				}
				problems="";
				success=0;
				for(i=0;i<rows.length;i++){
					objects=container.find("tr[class='"+rows[i]+" painted'] td");
					var values=new Array(objects.size()-1);
					j=0;
					firstFlag=firstFlagMode;
					objects.each(function(e) {
						if(firstFlag==false){
							values[j]=$(this).html();
							j++;
						}else
							firstFlag=false;
					});
					newValues2=new Array(newValues.length);
					for(j=0;j<newValues.length;j++){newValues2[j]=values[j];}
					for(j=0;j<changes.length;j++){newValues2[changes[j]]=newValues[changes[j]];}
					//Ajax call to update the data in a row
					$.ajax({type:"GET", url:phpRute+"CUD.php", data:{"action":"Upd", "configFile":configFile, "fields[]":titles, "values[]":values, "newValues[]":newValues2}, success:function(e){
						if(!String.prototype.trim){
						  String.prototype.trim = function(){
							return replace(/^\s+|\s+$/g, ''); 
						  } 
						}
						if((e.trim().toLowerCase()!="nothing")&&(e.trim().toLowerCase()!="true")){
							problems+="At row "+rows[i]+": "+e.replace(/(?:<[^>]+>)/gi, '')+"\n";
						}else	success++;
					}, async:false, error:function(){alert("There is a problem in the Ajax call!!. \nMaybe the relative path to the php directory of easyTables is not correct");}});
				}
				if(problems!=""){
					alert(problems+"\n"+success+"rows changed!");
				}else{
					container.find(".updateModal").modal('hide');
					container.find(".updateModal").html("");
					changePager(container.find(".actual").val(), container.find(".actual").val());
				}
			}
		}else{
			alert("You haven't changed anything!");
		}
	}
	
	function showNewRow(){
	    //This function shows the option to create a new registry in the table
		objects=container.find("tr[class='header'] th");
		var titles=new Array(objects.size()-1);
		var sizes=new Array(objects.size()-1);
		i=0;
		firstFlag=firstFlagMode;
		objects.each(function(e) {
			if(firstFlag==false){
				titles[i]=$(this).attr("name");
				sizes[i]=$(this).width();
				i++;
			}else
				firstFlag=false;
		});
		if(useModal){//If useModal==true then the input field will be shown in a modal windows
			showModal("Insert", titles);
		}else{//If doesn't the inputs will be shown inline
			container.find("tr.0").addClass("updt");
			container.find("tr.0").prop("title", "You can edit this row");
			div=(((updateRow)||(deleteRow))?"<td class='control'><span title='cancel' class='0'><i class='icon-remove'></i></span></td>":"");
			for(i=0;i<titles.length-1;i++)
				div+="<td><input type='text' class='"+titles[i].replace(/'/g,"&#39;")+"' style='width:"+(sizes[i]-less)+"px'></td>";
			div+="<td class='control2'><span title='save' class='0'><i class='icon-ok'></i></span>"+(((!updateRow)&&(!deleteRow))?" <span title='cancel' class='0'><i class='icon-remove'></i></span>":"")+"</td>";
			container.find("tr.0").html(div);
			container.find("tr.0").slideDown();
			container.find("tr.0 span[title='save']").click(function(e){insertRow(titles)});
			container.find("tr.0 span[title='cancel']").click(function(e){container.find("tr.0").slideUp();});
		}
	}
	
	function insertRow(titles){
	    //This function inserts a new row in the table
		var objects;
		if(useModal)
			objects=container.find(".updateModal input[type='text']");
		else
			objects=container.find("tr.0 input[type='text']");
		var newValues=new Array(objects.size());
		i=0;
		objects.each(function(e) { newValues[i]=$(this).val(); i++;  });
		$.get(phpRute+"CUD.php", {"action":"Ins", "configFile":configFile, "fields[]":titles, "values[]":newValues}, function(e){
				if(!String.prototype.trim){
				  String.prototype.trim = function(){
					return replace(/^\s+|\s+$/g, ''); 
				  } 
				}
				if(e.trim().toLowerCase()=="true"){
					if(useModal){
						container.find(".updateModal").modal('hide');
						container.find(".updateModal").html("");
					}else{
						container.find("tr.0").slideUp();
					}
					changePager(pageNumber,pageNumber);
				}else{
					alert(e.replace(/^\s+|\s+$/g, ''));
				}
		});
	}
	
	function deleteRows(){
	    //This function delete the checked rows
		objects=container.find("td.control input[type='checkbox']");
		var rows=new Array();
		i=0;
		objects.each(function(e) {
			if($(this).prop("checked")==true){
				rows[i]=$(this).attr("class");
				i++;
			}
		});
		if(i!=0){
			i=0;
			objects=container.find("tr[class='header'] th");
			var titles=new Array(objects.size()-1);
			firstFlag=firstFlagMode;
			objects.each(function(e) {
				if(firstFlag==false){
					titles[i]=$(this).attr("name");
					i++;
				}else
					firstFlag=false;
			});
			if(confirm("Do you really want to delete this rows?")){
				deleted=0;
				for(j=0;j<rows.length;j++){
					//alert(rows[j]);
					objects=container.find("tr[class='"+rows[j]+" painted'] td");
					var values=new Array(objects.size()-1);
					i=0;
					firstFlag=firstFlagMode;
					objects.each(function(e) {
						if(firstFlag==false){
							values[i]=$(this).html();
							i++;
						}else
							firstFlag=false;
					});
					$.ajax({type:"GET", url:phpRute+"CUD.php", data:{"action":"Del", "configFile":configFile, "values[]":values, "fields[]":titles}, success:function(e){
							if(!String.prototype.trim){
							  String.prototype.trim = function(){
								return replace(/^\s+|\s+$/g, ''); 
							  } 
							}
							if(e.trim().toLowerCase()!="true"){
								alert(e.replace(/(?:<[^>]+>)/gi, ''));
							}else{
								deleted++;
							}
					}, async:false, error:function(){alert("There is a problem in the Ajax call!!. \nMaybe the relative path to the php directory of easyTables is not correct");}});
				}
				changePager(container.find(".actual").val(), container.find(".actual").val());
				alert(deleted+" rows deleted successfully");
			}
		}else{
			alert("You have to select at least a row!");
		}
	}
	
	function updRow(){
	    //Execute the update of a row
		values=arguments[0];
		titles=arguments[1];
		var objects=(useModal ? container.find(".updateModal input[type='text']"):container.find("tr."+arguments[2]+" input[type='text']"));
		var newValues=new Array(objects.size());
		i=0;
		objects.each(function(e) {
			newValues[i]=$(this).val();
			i++;
		});
		$.get(phpRute+"CUD.php", {"action":"Upd", "configFile":configFile, "fields[]":titles, "values[]":values, "newValues[]":newValues}, function(e){
				if(!String.prototype.trim){
				  String.prototype.trim = function(){
					return replace(/^\s+|\s+$/g, ''); 
				  } 
				}
				if(e.trim().toLowerCase()=="true"){
					changePager(container.find(".actual").val(), container.find(".actual").val());
					if(useModal){
						container.find(".updateModal").modal('hide');
						container.find(".updateModal").html("");
					}
				}else if(e.trim().toLowerCase()=="nothing"){
					alert("You haven't changed anything!");
				}else{
					alert(e.replace(/(?:<[^>]+>)/gi, ''));
				}
			});
	}
	
	function showSearch(){
	    //This function shows the search fields
		stringHtml="";
		if((searchFields!=false)&&(searchFields!=true)){
			stringHtml+="<td><input type='text' class='searchValue' name='searchValue' placeholder='search'> ";
			$.ajax({
				type:"GET",
				url:phpRute+"getColumns.php", 
				data:{"configFile":configFile, "searchFields":searchFields}, 
				success:function(data){stringHtml+=data;},
				async:false,
				//error:function(){alert("There is a problem in the Ajax call!! \nMaybe the relative path to the php directory of easyTables is not correct");}
			});
			stringHtml+=((autoSearch)?"":" <a class='searchButton button icon search'>search</a>")+"</td>";
		}else if(searchFields==true){
			stringHtml+="<td><input type='text' class='searchValue' name='searchValue' placeholder='search'> ";
			$.ajax({
				type:"GET",
				url:phpRute+"getColumns.php", 
				data:{"configFile":configFile}, 
				success:function(data){stringHtml+=data;},
				async:false,
				//error:function(){alert("There is a problem in the Ajax call!! \nMaybe the relative path to the php directory of easyTables is not correct");}
			});
			stringHtml+=((autoSearch)?"":" <a class='searchButton button icon search'>search</a>")+"</td>";
		}
		stringHtml+="<td class='right'>Show <select class='nResults' name='nResults'>";
		for(i=0;i<nPResults.length;i++){
			stringHtml+="<option value='"+nPResults[i]+"'>"+nPResults[i]+"</option>";
		}
		stringHtml+="</select> "+
			((newRow)?"<a class='newButton button icon add'>new</a> ":"")+
			((updateRow)?"<a class='mUpdateButton button icon edit'>update</a> ":"")+
			((deleteRow)?"<a class='delButton button icon trash'>delete</a> ":"")+
			"</td>";
		container.find(".search").html(stringHtml);
		container.find(".searchButton").click(showTable);
		container.find(".delButton").click(deleteRows);
		container.find(".newButton").click(showNewRow);
		container.find(".mUpdateButton").click(showMultipleUpdate);
		container.find(".nResults").change(function(e){nResults=$(this).val(); changePager(pageNumber,pageNumber);});
		if(autoSearch==true) container.find(".searchValue").keyup(function(e){changePager(pageNumber, pageNumber);});
	}
	
	function changePager(actual, prev){
	    //This function change the number of the pager and the info in the table according to the pager number
		if((actual=="<<")||(actual==">>")){
			$.get(phpRute+"generateTable.php", {"configFile":configFile, "actual":actual, "prev":prev, "search":((searchFields!=false)?container.find(".searchValue").val():""), "searchField":((searchFields!=false)?container.find(".searchField").val():""), "nResults":nResults, "orderField":orderField, "sense":sense, "showCheck":showCheck}, function(data){showData(data);});
		}else{
			$.get(phpRute+"generateTable.php", {"configFile":configFile, "actual":actual, "search":((searchFields!=false)?container.find(".searchValue").val():""), "searchField":((searchFields!=false)?container.find(".searchField").val():""), "nResults":nResults, "orderField":orderField, "sense":sense, "showCheck":showCheck}, function(data){showData(data);});
		}
	}
	
	function showData(data){
	    //This function shows the information in the table
		container.find(".result").html(data);
		if(exportOptions!=false){
			selectOp="";
			for(i=0;i<exportOptions.length;i++){
				selectOp+="<option value='"+exportOptions[i]+"'>"+exportOptions[i]+"</option>";
			}
			container.find("select[name='exportOptions']").html(selectOp);
			container.find("td.exp").show();
			container.find("a.get").click(function(e){$(".easyTableForm").submit();});
		}
		pageNumber=container.find(".actual").val();
		container.find(".pag").click(function(e){changePager($(this).val(),  container.find(".actual").val())});
		container.find("td span[title='edit']").click(function(e){showUpdate($(this).attr("class"));});
		container.find("td span[title='see']").click(function(e){showDetails($(this).attr("class"));});
		container.find(".header th.ord").click(function(e){sortTable($(this).attr("name"));});
		container.find("th.control input[type='checkbox']").change(selectAll)
		container.find(".resultTable tr").hover(paintRow, unpaintRow);
		container.find(".resultTable tr").dblclick(function(e){
			if(onDBClick=="update")	showUpdate($(this).attr("class"));
			else if(onDBClick=="details")	showDetails($(this).attr("class"));
			else{
				if(updateRow)	showUpdate($(this).attr("class"));
				else	showDetails($(this).attr("class"));
			}
		});
		container.find("td.control input[type='checkbox']").change(paintRowChecked);
		container.find("tr.0").hide();
		if(tableWidth!="auto") container.find(".resultTable").attr('width', tableWidth);
		if(!textInSingleRow){
			container.find(".resultTable td").css("white-space", "normal");
			container.find(".resultTable th").css("white-space", "normal");
		}
		/*
			If the content in a cell is bigger than the asigned width the 
			attribute title will be added with the full content of the cell
		*/
		objects=document.getElementsByClassName("resultTable");
		objects=objects.item(0).getElementsByTagName("td");
		for(i=0;i<objects.length;i++){
			if((objects.item(i).scrollWidth!=objects.item(i).clientWidth)||(objects.item(i).scrollHeight!=objects.item(i).clientHeight)){
				objects.item(i).setAttribute("title", objects.item(i).innerHTML);
			}
		}
		objects=document.getElementsByClassName("resultTable");
		objects=objects.item(0).getElementsByTagName("th");
		for(i=0;i<objects.length;i++){
			if((objects.item(i).scrollWidth!=objects.item(i).clientWidth)||(objects.item(i).scrollHeight!=objects.item(i).clientHeight)){
				title=objects.item(i).innerHTML;
				if(objects.item(i).getAttribute("name")==orderField){
					title=title.replace(' <i class="icon-chevron-down icon-white"></i>','');
					title=title.replace(' <i class="icon-chevron-up icon-white"></i>','');
				}
				objects.item(i).setAttribute("title", title);
			}
		}
		container.find(".search").css("margin","auto");
		if(exportOptions!=false)	container.find("input[name='actual']").val(container.find(".actual").val());
	}
}