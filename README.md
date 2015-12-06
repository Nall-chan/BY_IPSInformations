IP-Symcon Modul -- IPSInformations

Dieses Modul liest im eingestellten Intervall die Anzahl verschiedener IPS Objekte aus
und speichert diese in Variablen.

Es werden folgende Informationen von IPS ausgelesen:
Anzahl von
- Events
- Instanzen
- Kategorien
- Links
- Modulen
- Objekten
- Profilen
- Skripten
- Variablen
- Bibliotheken 
- Medien

Außerdem gibt es:
- Zeitpunkt vom letzter Start des IPS-Dienst
- Größe der Datenbank
- Größe des Log und des Script-Verzeichnisses

Die Variablen werden automatisch mit einem Timer aktualisiert. Können aber auch
manuell mit dem Befehl "IPSInfo_Update($InstanzID)" aktualisiert werden.