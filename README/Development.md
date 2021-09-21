## Beginn Meta-Informationen der Datei

#
# @author  Markus Heizenreder-Reitz
# @version 1.0
# @date    17.09.2021
#

## Ende Meta-Informationen der Datei



## Entwicklungstätigkeit im Repository des Moduls „Verwaiste Dateien“

## Zugehöriges Haupt-Repository

Das Haupt-Repository -- inklusive einer konfigurierbaren Dockerumgebung --
findet sich unter

  https://gitea.bildung.hessen.de/Moodlezeug/moodle-composer.git

Der Checkout dieses Bestandteils inklusive Wechsel auf den Entwickler-Branch
erfolgt demnach durch

  git clone https://gitea.bildung.hessen.de/Moodlezeug/moodle-composer.git
  git checkout develop

## Repository für Teilprojekte

Die einzelnen In-House entwickelten / zu entwickelnden Module für Moodle
erhalten (nach und nach) eigene Repositories. Für das Plugin „Verwaiste
Dateien“ ist dies bereits geschehen:

  https://gitea.bildung.hessen.de/Moodlezeug/moodle-report_sphorphanedfiles.git

Der Checkout mit Wechsel auf den Entwickler-Branch erfolgt demnach durch

  git clone https://gitea.bildung.hessen.de/Moodlezeug/moodle-report_sphorphanedfiles.git
  git checkout develop

## Kombination des Teilprojekts ins Hauptprojekt

Die Integration ins Hauptprojekt erfolgt mittels Einbindung eines entsprechenden
Docker-Volumes. Genauere Information sind in der Datei

  Docker.md

zu finden.