## Beginn Meta-Informationen der Datei

#
# @author  Markus Heizenreder-Reitz
# @version 1.1
# @date    05.10.2021
#

## Aktueller Stand der Modulentwicklung

Die Quelltexte wurden / werden schrittweise einem Refactoring unterzogen, um
ausgehend von einem prototypischen quasi-prozeduralen Design zu einer
OO-Modellierung zu gelangen. Dabei werden insbesondere auch geeignete
**Design-Pattern** verwendet, u.a.

  - Chain of Responsibility
  - Decorator
  - Singleton
  - Factory

Im Hinblick auf eine optimierte (zukünftige) **Wartbarkeit** der Software-
komponente sollte dieses Vorgehen auf jeden Fall beibehalten werden.

## (Teilweise) offene Punkte

  1. Vervollständigung der Systemdokumentation
  2. Ausführliches Testen
  2. Überlegungen zu weiteren Refactoring-Operationen

## Zukünftige Features (Version 1.1)

1. Bestätigungsdialog

   Der Benutzer kann zu jedem vom System ermittelten Datei-Waisen bestimmen, ob
   dieser gelöscht werden soll. Da dieser Vorgang endgültig ist, sollte ein
   **Zwischendialog** vorhanden sein, welcher eine Sicherheitsabfrage vor der
   endgültigen Löschausführung stellt.

   Technische Bemerkungen:

   Vom Prinzip her wird dies dadurch realisiert, dass basierend auf dem FileInfo-
   Objekt, welches durch die POST-Anfrage empfangen wird, ein Zwischendialog
   angezeigt wird. Das Objekt wird zwischen den Request in den serialisierten
   Zustand überführt (-> toString) und dann im nächsten Schritt wieder
   deserialisiert (-> Konstruktor).

     --> Eine **ansprechende** textuelle Repräsentation von FileInfo bzw.
         FileInfoList erstellen: die technische Darstellung ist bereits
         voll funktionsfähig enthalten

     --> Explizite Löschfrage für den Benutzer präsentieren: optische Design-
         aspekte

     --> Bestätigungsbutton der Löschoperation: Verbindung, d.h. Aufruf, mit
         der entsprechenden (bereits vorhandenen) Methode

2. Mehrfachauswahl

   Prinzipiell sollten sich Löschaktionen (jeweils abgesichert durch einen
   Zwischendialog auf drei Wegen einleiten lassen:

     a) Löschen eines **einzelnen** Items
     b) Auswahl mehrerer Dateien zum „Löschen in einem Rutsch“ für eine **Section**
     c) Auswahl mehrerer Dateien zum „Löschen in einem Rutsch“ für den **gesamten Kurs**

   Dementsprechend sollte die Button-Positionierung folgendermaßen in Erwägung gezogen
   werden:

     - Lösch-Button am Item für direkte Löschausführung
     - Lösch-Button entweder am Anfang oder am Ende der Section, sodass die
       innerhalb der Section ausgewählten Elemente entfernt werden
     - Lösch-Button entweder am Anfang oder am Ende der Gesamtübersicht, sodass
       die innerhalb des gesamten Kurses ausgewählten Elemente entfernt werden

   Technische Bemerkungen:

   Der Übergang auf ein **CheckBox-Element**, welches bei jedem zu löschenden
   Moodle-Objekt eingeblendet wird, ermöglicht das Löschen von mehreren
   Elementen „in einem Rutsch“.

   Zu beachten ist dabei:

     - Die konventionelle (-> bisherige) Funktionalität ist durch das Mustache-
       Template

          sectionTable.mustache

        verfügbar.

     - Die erweiterte Funktionalität ist durch das Mustache-Template

          sectionTableMultipleSelection.mustache

       verfügbar.

   Die Entscheidung, welches Template verwendet werden soll, lässt sich durch
   entsprechendes Setzen des Methoden-Parameters während des Seiten-Renderings
   fällen.
