# MediCore Hospital System (PHP)

PHP + MySQL hospital management system built with a hand-written MVC framework.
Roles: Doctor, Patient, Receptionist. Modules: Appointments, Prescriptions, Billing, Pharmacy. The dashboard shows an appointments-by-status chart.

## Run it on XAMPP
0. Needs PHP 7.1 or newer (works on old and new XAMPP).
1. Copy this folder to `C:\xampp\htdocs\hospital-app`.
2. Start **Apache** and **MySQL** in the XAMPP control panel.
3. Open phpMyAdmin (http://localhost/phpmyadmin), create a database called `hospital_db`,
   open it -> **SQL** tab -> paste the content of `database.sql` -> **Go**.
4. Open http://localhost/hospital-app/ and register a Doctor, a Patient and a Receptionist account.

If your MySQL has a password or a different user, change it in `config/config.php`.
`mod_rewrite` must be on (it is by default in XAMPP).

## Without Apache (quick test)
    php -S localhost:8000 router.php
then open http://localhost:8000/

## Structure
    index.php            front controller + all routes
    config/config.php    database settings
    app/core/            Router, Controller, Database (PDO), Auth middleware, helpers
    app/models/          User, Appointment, Prescription, Bill, Medicine, Stats
    app/controllers/     one controller per module
    app/views/           PHP templates (layout.php wraps every page)
    public/css/          stylesheet
    database.sql         tables + sample medicines
