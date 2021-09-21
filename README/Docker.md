## Beginn Meta-Informationen der Datei

#
# @author  Markus Heizenreder-Reitz
# @version 1.0
# @date    21.09.2021
#

## Anmerkungen zur Einbindung des Moduls „Verwaiste Dateien“

Für dieses Modul ist bereits ein **eigenes Repository** eingerichtet. Somit kann
die Projektentwicklung parallel verlaufen und die jeweiligen Fortschritte sind 
jeweils separat im Versionsverwaltungssystem fokussiert überblickbar.

Daraus folgt, dass beim Auschecken des „Haupt-Repositories“ (moodle-composer) im
Verzeichnis 

  moodle-composer/moodle-build/vhost-verwaiste/moodle/httpdocs/report

**kein** Unterverzeichnis mehr für dieses Modul existieren muss. Stattdessen erfolgt
die Einbindung in den laufenden Container durch Festlegung eines **zusätzlichen** 
Volumes -- am einfachsten in der Datei 

  base.yml
  
im Verzeichnis „docker“ des Haupt-Repositories. Da die nachfolgend beschriebenen 
Anpassungen von der auf dem jeweiligen Entwicklerrechner gewählten Verzeichnisstruktur 
abhängen, sollte eine ausschliesslich für den persönlichen Gebrauch angepasste
base.yml-Datei **nicht** in das Repository zurückgeschrieben werden.

## Einbindung des zweiten Repositories als Docker-Volume

Für den Container „webserver“ (siehe entsprechender Abschnitt in der YAML-Datei) ergibt
sich beispielhaft folgendes **unvollständiges** Fragment:

  webserver:
    image: "moodlehq/moodle-php-apache:${MOODLE_DOCKER_PHP_VERSION}"
    depends_on:
      - db
    volumes:
      - "${MOODLE_DOCKER_WWWROOT}:/var/www/html"

       *
       * Hier sind weitere Festlegungen zu finden (siehe Originaldatei).
       *

      - "/Users/schulportal/Desktop/Softwareentwicklung/Moodle/moodle-report_sphorphanedfiles:/var/www/html/report/sphorphanedfiles"
                                                                                           
Die **Doppelpunkt-Notation** trennt die **Außensicht** (-> Verzeichnisse und deren Struktur außerhalb des Containers), die **links** von diesem aufgeführt wird, von der 
im Container für das betreffende Verzeichnis herzustellenden Position und
Benennung -- die Innensicht.

Wird die betreffende Container-Konfiguration gestartet, wird das betreffende Modul
in die Umgebung durch Rückgriff auf das externe Verzeichnis in das System integriert
und steht regulär zur Nutzung zur Verfügung.

