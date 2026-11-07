<?php
class DashboardController extends Controller
{
    // Every role sees ONLY the modules that it needs
    private const MODULES = [
        'Patient' => [
            ['📅', 'My Appointments', 'View, reschedule or cancel your appointments.', '/appointments', 'Open'],
            ['➕', 'Book Appointment', 'Choose a doctor, date and time.', '/appointments/new', 'Book Now'],
            ['💊', 'My Prescriptions', 'See the prescriptions your doctors issued.', '/prescriptions', 'View'],
            ['🧾', 'My Bills', 'Check your bills and payment status.', '/billing', 'View'],
        ],
        'Doctor' => [
            ['📅', 'My Appointments', 'See your patients and update appointment status.', '/appointments', 'Open'],
            ['💊', 'Prescriptions', 'Create, edit and manage digital prescriptions.', '/prescriptions', 'Open'],
            ['➕', 'New Prescription', 'Write a prescription for a patient.', '/prescriptions/new', 'Create'],
            ['📦', 'Pharmacy Stock', 'Check medicine availability and low stock alerts.', '/pharmacy', 'View'],
        ],
        'Receptionist' => [
            ['📅', 'Appointments', 'Manage all appointments (add, edit, cancel, delete).', '/appointments', 'Manage'],
            ['➕', 'Book Appointment', 'Book an appointment for a patient.', '/appointments/new', 'Book'],
            ['💰', 'Billing', 'Create bills and mark payments.', '/billing', 'Open'],
            ['📦', 'Pharmacy Inventory', 'Add, update and remove medicines and stock.', '/pharmacy', 'Manage'],
        ],
    ];

    public function index()
    {
        $user = currentUser();
        $this->render('dashboard', [
            'title' => 'Dashboard',
            'modules' => self::MODULES[$user['role']] ?? [],
            'stats' => Stats::forUser($user),
            'chart' => Stats::appointmentChart($user),
        ]);
    }
}
