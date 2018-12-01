$(function(){
	var editTemplate = '<td>#id</td><td>#date</td><td><input name="name" value="#name" style="width:100%" type="text" /></td><td><textarea name="comment" style="height:22px;width:100%">#comment</textarea></td><td><a class="#method" href="#"><img src="images/save.png" alt="save" /></a>#deleteButton</td>';
	
	function getEditTemplate(method, id, date, name, comment) {
		if(id.length == 0) {
			id = "&nbsp;";
		}
		if(date.length == 0) {
			date = "&nbsp;";
		}
	
		var temp = editTemplate;
		
		temp = temp.replace("#id", id);
		temp = temp.replace("#date", date);
		temp = temp.replace("#name", name);
		temp = temp.replace("#comment", comment);
		if(method == "add") {
			temp = temp.replace("#deleteButton", '<a class="delete_new" href="#"><img src="images/icon_del_light.png" alt="delete" /></a>');
		} else {
			temp = temp.replace("#deleteButton", '');
		}
		temp = temp.replace("#method", method);
		
		
		return temp;
	}
	
	function save(method, context) {
		
		$('#loader').show(); // Zeige Ajax Loader
		var name = $(context).parent().parent().find('input[name="name"]').val(); // Holt den Namen aus dem Input Feld der Aktuellen Zeile
		var comment = $(context).parent().parent().find('textarea[name="comment"]').val(); // Holt Kommentar
		var id = $(context).parent().parent().find("td:first").html();
		var currentItem = context;


		if(name!='' && comment!='') {
			// Schicke speicher Anfrage an PHP
			$.post("index.php", { "comment": comment, "name": name, "action": method, "id":id },
			function(data){
				$(currentItem).parent().parent().replaceWith(data.row); // Ersetzt die Aktuelle Zeile mit der gespeicherten
				addEvents();
				$('#loader').hide(); // Verstecke Ajax Loader
			}, "json");
		} else {
			$('#loader').hide(); // Verstecke Ajax Loader
			alert('Bitte trage Namen und Kommentar ein');
		}
	}
	

	// Neue Zeile an erste Stelle der Tabelle hinzufügen zum Hinzufügen von Daten
	$(".add_row").click(function() {
		$("table tr").first().after("<tr>" + getEditTemplate("add", "", "", "", "") + "</tr>");
		addEvents();
	});
	
	function addEvents() {
		// Sorgt dafür, dass beim mehrfachen hinzufügen von zeilen, der Eventhandler nicht mehrfach ausgeführt wird.
		$(".add").unbind();
		$(".update").unbind();
		$(".delete_new").unbind();
		$(".edit_row").unbind();
	
		// Speichern eines neuen Eintrags
		$(".add").click(function() {
			save("add_new", $(this));
		});
		
		// Updaten eines Eintrags
		$(".update").click(function() {
			save("update", $(this));
		});
		
		
		// Entfernt die neue Row, da sie noch nicht gespeichert wurde wird nur der HTML Code entfernt
		$(".delete_new").click(function() {
			$(this).parent().parent().remove(); // Mit parent wird 2 Objekte höher gegangen um die komplette Zeile zu löschen nicht nur den löschen Button
		});
		
		// Entfernt eine Zeile
		$(".delete_row").click(function() {
			$('#loader').show();
			var id = $(this).parent().parent().find("td:first").html();
			$(this).parent().parent().remove(); // Mit parent wird 2 Objekte höher gegangen um die komplette Zeile zu löschen nicht nur den löschen Button
			$.post("index.php", { "action": "delete_row", "id": id }, function(){
				$('#loader').hide();
			});
		});
		
		// Edit Row anzeigen beim OnClick
		$(".edit_row").click(function() {
			var id = $(this).parent().parent().find("td:first").html();
			var date = $(this).parent().parent().children().eq(1).html();
			var name = $(this).parent().parent().children().eq(2).html();
			var comment = $(this).parent().parent().children().eq(3).html();
			
			$(this).parent().parent().html(getEditTemplate("update", id, date, name , comment));
			addEvents();
		});
	}
	
	addEvents();
});