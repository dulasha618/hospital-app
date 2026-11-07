<?php
// Application bootstrap + routes (loaded by ../index.php)


$app = __DIR__;
require "$app/core/App.php";
require "$app/core/helpers.php";
foreach (['Database', 'Controller', 'Auth', 'Router'] as $f) require "$app/core/$f.php";
foreach (['User', 'Appointment', 'Prescription', 'Bill', 'Medicine', 'Stats'] as $f) require "$app/models/$f.php";
foreach (['Auth', 'Dashboard', 'Profile', 'Appointment', 'Prescription', 'Billing', 'Pharmacy'] as $f) require "$app/controllers/{$f}Controller.php";

App::$base = App::detectBase();

// Session (keeps the user logged in)
ini_set('session.cookie_samesite', 'Lax');
session_set_cookie_params(60 * 60 * 8, '/', '', false, true);
session_name('MEDICORESESSID');
session_start();

// One-time flash message for this request
$GLOBALS['__flash'] = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Work out the route path (strip the project folder and query string)
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if (App::$base !== '' && strpos($path, App::$base) === 0) $path = substr($path, strlen(App::$base));
$path = '/' . trim($path, '/');
if ($path === '/index.php') $path = '/';

$login = [function () { return Auth::requireLogin(); }];
$guest = [function () { return Auth::guest(); }];
$role = function (...$r) { return [Auth::requireRole(...$r)]; };

$r = new Router();

// Entry point: login page first (or dashboard if already logged in)
$r->get('/', 'AuthController@home');

// ---------- Auth (public) ----------
$r->get('/login', 'AuthController@showLogin', $guest);
$r->post('/login', 'AuthController@login', $guest);
$r->get('/register', 'AuthController@showRegister', $guest);
$r->post('/register', 'AuthController@register', $guest);
$r->post('/logout', 'AuthController@logout');

// ---------- Dashboard & Profile (all roles) ----------
$r->get('/dashboard', 'DashboardController@index', $login);
$r->get('/profile', 'ProfileController@show', $login);
$r->post('/profile', 'ProfileController@update', $login);

// ---------- Appointments ----------
// Patient: own | Doctor: own patients | Receptionist: everything
$bookers = $role('Patient', 'Receptionist');
$r->get('/appointments', 'AppointmentController@index', $login);
$r->get('/appointments/new', 'AppointmentController@create', $bookers);
$r->post('/appointments', 'AppointmentController@store', $bookers);
$r->get('/appointments/{id}/edit', 'AppointmentController@edit', $bookers);
$r->post('/appointments/{id}/update', 'AppointmentController@update', $bookers);
$r->post('/appointments/{id}/status', 'AppointmentController@setStatus', $role('Doctor', 'Receptionist'));
$r->post('/appointments/{id}/delete', 'AppointmentController@destroy', $bookers);

// ---------- Prescriptions ----------
// Doctor: full CRUD | Patient: read own
$doctorOnly = $role('Doctor');
$r->get('/prescriptions', 'PrescriptionController@index', $role('Doctor', 'Patient'));
$r->get('/prescriptions/new', 'PrescriptionController@create', $doctorOnly);
$r->post('/prescriptions', 'PrescriptionController@store', $doctorOnly);
$r->get('/prescriptions/{id}/edit', 'PrescriptionController@edit', $doctorOnly);
$r->post('/prescriptions/{id}/update', 'PrescriptionController@update', $doctorOnly);
$r->post('/prescriptions/{id}/delete', 'PrescriptionController@destroy', $doctorOnly);

// ---------- Billing ----------
// Receptionist: full CRUD | Patient: read own
$receptionOnly = $role('Receptionist');
$r->get('/billing', 'BillingController@index', $role('Receptionist', 'Patient'));
$r->get('/billing/new', 'BillingController@create', $receptionOnly);
$r->post('/billing', 'BillingController@store', $receptionOnly);
$r->get('/billing/{id}/edit', 'BillingController@edit', $receptionOnly);
$r->post('/billing/{id}/update', 'BillingController@update', $receptionOnly);
$r->post('/billing/{id}/status', 'BillingController@setStatus', $receptionOnly);
$r->post('/billing/{id}/delete', 'BillingController@destroy', $receptionOnly);

// ---------- Pharmacy ----------
// Receptionist: full CRUD | Doctor: view only
$r->get('/pharmacy', 'PharmacyController@index', $role('Doctor', 'Receptionist'));
$r->get('/pharmacy/new', 'PharmacyController@create', $receptionOnly);
$r->post('/pharmacy', 'PharmacyController@store', $receptionOnly);
$r->get('/pharmacy/{id}/edit', 'PharmacyController@edit', $receptionOnly);
$r->post('/pharmacy/{id}/update', 'PharmacyController@update', $receptionOnly);
$r->post('/pharmacy/{id}/delete', 'PharmacyController@destroy', $receptionOnly);

try {
    $r->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $path);
} catch (Throwable $ex) {
    error_log((string) $ex);
    (new Controller())->renderServerError();
}
