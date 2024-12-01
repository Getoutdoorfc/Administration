MyRectorFiles - README

Dette projekt indeholder brugerdefinerede Rector-regler og handlers til refaktorering, backup, logging og rollback-processer.

Indhold
- Mappestruktur
- Filer og Deres Funktioner
- Brug af Filerne
  - Refaktorering med Backup og Logging
  - Rollback med Logging
  - Kun Logging
- Kørsel af Rector

Mappestruktur
MyRectorFiles
 ┣ MyRectorLogsAndBackupFiles
 ┃ ┣ Backups
 ┃ ┃ ┗ Includes
 ┃ ┃ ┃ ┗ SampleTest.php
 ┃ ┗ Logs
 ┃ ┃ ┣ rector_log_YYYYMMDD_HHMMSS.log
 ┃ ┃ ┗ rector_log_YYYYMMDD_HHMMSS.log
 ┣ MyRectorTailorMadeFuncs
 ┃ ┣ MyRectorBackupsHandler.php
 ┃ ┣ MyRectorLoggingHandler.php
 ┃ ┣ MyRefactoringAndRenameAllFilesReferences.php
 ┃ ┗ MyRollbackHandler.php
 ┗ rector.php

Filer og Deres Funktioner
rector.php
- Central konfigurationsfil for Rector.
- Initialiserer backup- og logging-handlers.
- Tilføjer brugerdefinerede regler.
- Tillader nem aktivering/deaktivering af funktioner.

MyRectorLoggingHandler.php
- Håndterer logning for Rector-processer.
- Implementerer singleton-mønster for centraliseret logging.
- Understøtter logniveauer: INFO, WARNING, ERROR.

MyRectorBackupsHandler.php
- Håndterer backup-processen for Rector.
- Opretter backups af angivne stier.

MyRefactoringAndRenameAllFilesReferences.php
- Brugerdefineret Rector-regel, der opdaterer namespaces, klassenavne, PHPDoc-kommentarer, use-statements og alle referencer.

MyRollbackHandler.php
- Håndterer rollback-processen ved at gendanne fra den seneste backup.

Brug af Filerne
1. Refaktorering med Backup og Logging
- Formål: Udfør refaktorering, tag backup og log handlinger.
- Krævede filer: rector.php, MyRectorBackupsHandler.php, MyRectorLoggingHandler.php, MyRefactoringAndRenameAllFilesReferences.php.

2. Rollback med Logging
- Formål: Rul ændringer tilbage og log handlinger.
- Krævede filer: rector.php, MyRectorLoggingHandler.php, MyRollbackHandler.php.

3. Kun Logging
- Formål: Log handlinger uden andre processer.
- Krævede filer: rector.php, MyRectorLoggingHandler.php.

Kørsel af Rector
For at køre Rector med den ønskede konfiguration, brug:
vendor/bin/rector process --config=MyRectorFiles/rector.php

Forenkling af Kommandoen
Tilføj dette script i composer.json:
"scripts": {
    "rector": "vendor/bin/rector process --config=MyRectorFiles/rector.php"
}

Nu kan Rector køres med:
composer rector
