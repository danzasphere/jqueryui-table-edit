<?php
require('mysql.php');
require('functions.php');


// Wenn kein AJAX, dann HTML Page ausgeben, sonst ajax actions ausführen
if(!IS_AJAX) {
	// KEIN AJAX Request
	// Erstellt die "normale" HTML Page

	// Daten aus Datenbank besorgen
	$query = "SELECT `dev_jqueryui-table-edit_id`, timestamp, name, comment FROM `dev_jqueryui-table-edit` ORDER BY timestamp DESC"; // Ausführen einer SQL-Anfrage
	$result = sql_query($query);

	$i = 0;
	while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$i++;
		
		// Alle Spalten
		foreach ($line as $key=>$col_value) {
			if($key=='timestamp') {
				$col_value = date('d.m.Y H:i', $col_value);
			}
			$data[$i][] = $col_value;
		}
		
		$data[$i][] = '<a class="edit_row" href="#"><img src="images/icon_edit.png" alt="edit" /></a><a class="delete_row" href="#"><img src="images/icon_del_light.png" alt="delete" /></a>'; // Aktions Links
	}
	$return = "<table border=\"1\">\n";
	
	//Tabellen Kopf
	$return .= "\t<tr>\n";
	$return .= "\t\t<th>ID</th>\n";
	$return .= "\t\t<th>Erstell Datum</th>\n";
	$return .= "\t\t<th>Name</th>\n";
	$return .= "\t\t<th>Kommentar</th>\n";
	$return .= "\t\t".'<th style="width:90px">Aktionen <a class="add_row" href="#"><img src="images/icon_add_light.png" alt="add" /></a></th>'."\n";
	
	$return .= "\t</tr>\n";
	///////
	
	//Tabellen Daten
	if(count($data)>0) {
		foreach($data as $line) 
		{
			$return .= MakeRow($line);
		}
	}
	$return .= "</table>\n";
	///////


	//HTML Template
$template = 
'<html>
	<head>
		<style>
			table {
				width:800px;
			}
			
			input, textarea {
				background-color: #66FF99;
			}
			#loader { position: fixed; left:50%; top:30px; display:none }
		</style>	
		
		<!-- JQuery -->
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script type="text/javascript" src="./functions.js"></script>
	</head>

	<body>
		<div id="loader"><img src="images/ajax-loader.gif"></div> <!-- Ajax Loader -->
		'.$return.'
		<a href="http://d4nza.de/blog/tutorials/jquery-ajax-mysql-tabellen-editieren-tutorial">Zurück zum Tutorial</a>
	</body>
</html>';

	echo $template; // Alles ausgeben
	///////	

} else {
	switch ($_POST['action']) {
		case "add_new":
			// Speicher neuen Eintrag und Tabelle Zeile zurückgeben
			
			// Daten Speichen
			$query = "INSERT INTO `dev_jqueryui-table-edit` (`name` ,`comment` ,`timestamp`)VALUES ('".saveColumn($_POST['name'])."', '".saveColumn($_POST['comment'])."', ".time().");"; 
			$result = sql_query($query);
			$id = mysql_insert_id();
				
			// Die neue Zeile erstellen
			$query = "SELECT `dev_jqueryui-table-edit_id`, timestamp, name, comment FROM `dev_jqueryui-table-edit` WHERE `dev_jqueryui-table-edit_id`=".$id;
			$result = sql_query($query);

			$i = 0;
			while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$i++;
				
				// Alle Spalten
				foreach ($line as $key=>$col_value) {
					if($key=='timestamp') {
						$col_value = date('d.m.Y H:i', $col_value);
					}
					$data[$i][] = $col_value;
				}
				
				$data[$i][] = '<a class="edit_row" href="#"><img src="images/icon_edit.png" alt="edit" /></a><a class="delete_row" href="#"><img src="images/icon_del_light.png" alt="delete" /></a>'; // Aktions Links
			}
		
			foreach($data as $line) 
			{
				$json['row'] = MakeRow($line);
			}
			
			$json['success'] = true;
			echo json_encode($json);
			break;    
		
		case "delete_row":
			$query = "DELETE FROM `dev_jqueryui-table-edit` WHERE`dev_jqueryui-table-edit_id`=".saveColumn($_POST['id']); 
			//echo $query;
			sql_query($query);
			break; 
		case "update":
			$id = saveColumn($_POST['id']);
			$query = "UPDATE `dev_jqueryui-table-edit` SET `comment`='".saveColumn($_POST['comment'])."', `name`='".saveColumn($_POST['name'])."' WHERE`dev_jqueryui-table-edit_id`=".$id; 

			sql_query($query);
			
						// Die neue Zeile erstellen
			$query = "SELECT `dev_jqueryui-table-edit_id`, timestamp, name, comment FROM `dev_jqueryui-table-edit` WHERE `dev_jqueryui-table-edit_id`=".$id;
			$result = sql_query($query);

			$i = 0;
			while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$i++;
				
				// Alle Spalten
				foreach ($line as $key=>$col_value) {
					if($key=='timestamp') {
						$col_value = date('d.m.Y H:i', $col_value);
					}
					$data[$i][] = $col_value;
				}
				
				$data[$i][] = '<a class="edit_row" href="#"><img src="images/icon_edit.png" alt="edit" /></a><a class="delete_row" href="#"><img src="images/icon_del_light.png" alt="delete" /></a>'; // Aktions Links
			}
		
			foreach($data as $line) 
			{
				$json['row'] = MakeRow($line);
			}
			
			$json['success'] = true;
			echo json_encode($json);
			
			break; 
	}
}

// Schließen der Verbinung
mysql_close($link);