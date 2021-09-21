## Beginn Meta-Informationen der Datei

#
# @author  Markus Heizenreder-Reitz
# @version 1.0
# @date    21.09.2021
#

## Hinweise und Anmerkungen für die Nutzung von Visual Studio Code

Die Nutzung von Microsoft Visual Studio Code -- eine frei verfügbare IDE-Plattform -- 
stellt nach derzeitigem Erfahrungsstand eine sehr gute Basis im Rahmen der PHP-basierten
Moodle-Modulentwicklung dar.

Die Software ist für die drei gängigen Plattformen unter

  https://code.visualstudio.com

kostenfrei verfügbar.

## Sinnvolle Plugins für PHP- / Moodle-Entwicklung

Visual Studio Code lässt sich über Plugins (-> Extensions), welche über den app-internen
Marketplace bezogen werden können, mit zusätzlicher Funktionalität ausstatten. Nützlich 
sind insbesondere die Extensions

  - PHP Intelephense
  - PHP Extension Pack
  - PHP Debug

und ganz allgemein im Kontext von Docker und Git darüber hinausgehend die beiden 
Extensions

  - GitLens
  - Docker

## Konsequenzen der Aufspaltung in Haupt-Repository und (separate) Modul-Repositories

Zur (mittelfristigen) Entkopplung der Modulentwicklung im Bereich des Moodle-Systems
erfolgt eine Aufspaltung in 

  - Haupt-Repository: Dieses stellt die für das jeweilige (Teil-) Projekt zu nutzende
                      Container-Konfiguration zur Verfügung und beinhaltet insbesondere
                      auch alle notwendigen Dateien für ein lauffähiges Moodle-System.

  - eigenständige Modulrepositories: Diese beinhalten jeweils alle notwendigen Dateien 
                                     (Quelltexte, Konfigurationen etc.) für die 
                                     Entwicklung des betreffenden Moduls.
  
  Durch die zuvor beschriebene Aufspaltung ergeben sich aus der Perspektive der 
  Softwaretechnik zwei relevante Aspekte:

    1. Da Quelltexte etc. eines eigenständig entwickelten Moduls **nicht** im 
       Haupt-Repository vorhanden sind, muss die entsprechende Funktionalität in
       den Docker-Container gebracht werden. Das dazu **notwendige Vorgehen** ist in
       der Datei

         Docker.md

       im Detail beschrieben.

    2. Durch die **Trennung** zwischen Haupt-Repository und separatem Modul-Repository
       muss

         a) die Debugging-Konfiguration so gewählt werden, dass ein transparentes
            Debugging über die beiden Repository-Grenzen hinweg, d.h. insbesondere ein
            problemloses Setzen von Breakpoints, möglich wird. Die dafür zu beachtenden
            Schritte sind in der Datei

              Debugging.md

            näher ausgeführt.

        b) auf der Ebene der Entwicklungsumgebung Visual Studio Code in Kombination mit
           den Extensions für die Quelltextbearbeitung der Notwendigkeit Rechnung 
           getragen werden, im Arbeitsbereich für das Modul die Quellen des 
           Haupt-Repositories hinzufügen zu können.

           Diese **„externen Quellen“** werden im Fall von Visual Studio Code und PHP in 
           der Datei settings.json abgelegt. Diese muss sich im Verzeichnis .vscode des
           Arbeitsbereichs für die Modulentwicklung befinden. Schlussendlich wird 
           hierdurch der IncludePath auf das Haupt-Repository ausgedehnt:

             {
               "intelephense.environment.includePaths": [
                 "/Users/schulportal/Desktop/Softwareentwicklung/Moodle/moodle-composer/"
               ]
             }

Die beiden für Visual Studio Code spezifischen und von der jeweiligen Verzeichnisstruktur 
auf dem eingesetzten Entwicklerrechner abhängigen Dateien 

  launch.json
  settings.json

sind im README-Verzeichnis, in welchem auch diese Datei liegt, abgelegt. Im 
Unterverzeichnis „examples“ ist eine beispielhafte Konfiguration zu finden.
