Running the Application & Tests (PHP Manual Setup)
Prerequisites
Before you run the app, ensure you have:
PHP 8.2 or newer


Composer installed globally

Laravel CLI (or install via Composer)

Installation & Environment Setup

Clone the repository:
```
git clone https://github.com/ashanwithana/0a5de366-6a38-452c-a2aa-db7ba1fdabd6.git
```

Open the Project From cmd or terminal

Install PHP dependencies with Composer:

```
composer install
```

Create the .env file:

```
cp .env.example .env
```

Generate the application key:

```
php artisan key:generate
```

JSON data files placed in storage/app/data/

Running the CLI Application
To run the reporting system:
```
php artisan report:generate
```

You will be prompted in the console:
```
Please enter the Student ID:
> ( Type the StudentId )
```
```
Report to generate (1 for Diagnostic, 2 for Progress, 3 for Feedback):
> ( Type the Report ID )
```

Running Tests
You can run automated tests to verify the report services:
```
php artisan test
```
Or using PHPUnit directly:
```
vendor/bin/phpunit
```

Tests include:
- Correct report output
- Handling of invalid student IDs
- Score improvement tracking
- Feedback with hints for wrong answers

Folder Structure 
```
app/
├── Console/
│   └── Commands/GenerateReport.php
├── Services/
│   ├── DiagnosticReportService.php
│   ├── FeedbackReportService.php
│   └── ProgressReportService.php

tests/
└── Feature/
    ├── DiagnosticReportTest.php
    ├── FeedbackReportTest.php
    └── ProgressReportTest.php

app/
    └── data/
        ├── assessments.json
        ├── questions.json
        ├── students.json
        └── student-responses.json

```
