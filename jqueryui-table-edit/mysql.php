<?
// Verbindung aufbauen, auswählen einer Datenbank
$link = mysql_connect("localhost", "username", "passwort")
    or die("Keine Verbindung möglich: " . mysql_error());
 
// Datenbank auswählen
mysql_select_db("database") or die("Auswahl der Datenbank fehlgeschlagen\n");