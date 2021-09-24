## Beginn Meta-Informationen der Datei

#
# @author  Markus Heizenreder-Reitz
# @version 1.0
# @date    24.09.2021
#

## Aktueller Stand der Modulentwicklung

Die Quelltexte wurden schrittweise einem Refactoring unterzogen, um ausgehend von
einem prototypischen quasi-prozeduralen Design zu einer OO-Modellierung zu gelangen.
Dabei werden insbesondere auch geeignete **Design-Pattern** verwendet, u.a.

  - Chain of Responsibility
  - Decorator
  - Singleton
  - Factory

Im Hinblick auf eine optimierte (zukünftige) **Wartbarkeit** der Softwarekomponente sollte
dieses Vorgehen beibehalten werden.

## Zukünftige Features

1. Bestätigungsdialog

   Statt des direkten Löschens durch Klick auf den entsprechenden Button soll eine 
   Sicherheitsabfrage erfolgen.

   Vom Prinzip wird dies dadurch realisiert, dass basierend auf dem FileInfo-Objekt, welches
   durch die POST-Anfrage empfangen wird, ein Zwischendialog angezeigt wird:

     --> Textuelle Repräsentation von FileInfo
     --> Explizite Löschfrage an den Benutzer
     --> Bestätigungsbutton der Löschoperation

2. Mehrfachauswahl

   Durch Übergang auf ein CheckBox-Element, welches bei jedem zu löschenden Moodle-Objekt
   eingeblendet wird, ermöglicht das Löschen von mehreren Elementen „in einem Rutsch“. Der
   Bestätigungsdialog ist selbstverständlich auch für diesen Fall umsetzbar.