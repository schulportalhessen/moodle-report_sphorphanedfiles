## Beginn Meta-Informationen der Datei

#
# @author  Markus Heizenreder-Reitz
# @version 1.0
# @date    21.09.2021
#

## Anmerkungen zur Debugging-Konfiguration

Die im **Haupt-Repository** vorhandenen Skripte, welche sich im Verzeichnis

  SimplifyContainerControl

befinden, sind prinzipiell für die Vorbereitung einer funktionierenden XDebug-Session
zuständig. Der variable Teil ist dabei das -- je nach Entwicklungsumgebung -- 
unterschiedlich einzustellende PathMapping, welches eine Verbindung zwischen der lokalen
Verzeichnisstruktur auf dem Entwicklerrechner und der Verzeichnisstruktur des
Docker-Containers herstellt.

Im Fall, dass mehrere Repositories verwendet werden, ergeben sich auch **mehrere**
PathMappings.

Für den Fall der Entwicklungsumgebung Visual Studio Code, welche die betreffenden
Informationen lokal in der Datei

  launch.json

ablegt. In Kombination mit dem in einem **separaten Repository** abgelegten Modul für
verwaiste Dateien ergibt sich dann beispielsweise

{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "XDebug Session",
            "type": "php",
            "request": "launch",
            "port": 9003,

            // FIXME: Der absolute Pfad muss noch weg!
            //          --> spezifisch für den jeweiligen Entwicklerrechner
            //
            // WICHTIG: Zusammenhang des Mappings mit Docker-Konfiguration beachten
            //            --> siehe zugehöriges README!
            
            "pathMappings": {
                "/var/www/html/report/sphorphanedfiles": "/Users/schulportal/Desktop/Softwareentwicklung/Moodle/moodle-report_sphorphanedfiles",
                "/var/www/html": "/Users/schulportal/Desktop/Softwareentwicklung/Moodle/moodle-composer/moodle-build/vhost-verwaiste/moodle/httpdocs"
            }       
        }
    ]
}

**Achtung**: Das für das Modul verwendete PathMapping ist quasi die **umgekehrte Version**
der in der Docker-Datei 

  base.yml

gemachten Einstellung. Dies ergibt sich durch die Tatsache, dass die eine Konfiguration
„von innen nach außen“ (Sichtweise von XDebug innerhalb des Containers nach draußen zum
XDebug-Client, d.h. der IDE des Entwicklers) und die andere Konfiguration „von außen
nach innen“ (Die Verzeichnisstruktur des Entwicklungsrechners (außen) wird in den Container (innen) abgebildet) zu betrachten ist.

Für eine funktionsfähige Debug-Konfiguration mit Visual Studio Code müssen somit die
Einstellungen in der Datei

  launch.json

**kompatibel** mit den Einstellungen in der Datei

  base.yml

sein. **Ansonsten lassen sich Breakpoints innerhalb des Moduls nicht korrekt setzen**.