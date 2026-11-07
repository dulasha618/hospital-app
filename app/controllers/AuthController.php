<?php
class AuthController extends Controller
{
    const ALLOWED_ROLES = ['Doctor', 'Patient', 'Receptionist'];

    public function home()
    {
        $this->redirect(currentUser() ? '/dashboard' : '/login');
    }

    public function showLogin()
    {
        $this->render('auth/login', [
            'title' => 'Login', 'error' => null,
            'success' => isset($_GET['registered']) ? 'Registration successful! Please log in.' : null,
        ], 200, 'auth-card');
    }

    public function login()
    {
        $username = trim($this->post('username'));
        $password = $this->post('password');

        $user = User::findByUsername($username);
        if (!$user || !User::verifyPassword($password, $user['password'])) {
            $this->render('auth/login', ['title' => 'Login', 'error' => 'Invalid username or password.', 'success' => null], 401, 'auth-card');
            return;
        }

        session_regenerate_id(true); // prevents session fixation
        $_SESSION['user'] = [
            'id' => (int) $user['id'], 'fullName' => $user['fullName'],
            'username' => $user['username'], 'role' => $user['role'],
        ];
        $this->redirect('/dashboard');
    }

    public function showRegister()
    {
        $this->render('auth/register', ['title' => 'Register', 'error' => null, 'form' => []], 200, 'auth-card');
    }

    public function register()
    {
        $fullName = trim($this->post('fullName'));
        $username = trim($this->post('username'));
        $password = $this->post('password');
        $confirm = $this->post('confirmPassword');
        $role = $this->post('role');
        $form = compact('fullName', 'username', 'role');

        $fail = function (string $msg) use ($form) {
            $this->render('auth/register', ['title' => 'Register', 'error' => $msg, 'form' => $form], 400, 'auth-card');
        };

        if ($fullName === '' || $username === '' || $password === '') return $fail('All fields are required.');
        if (!in_array($role, self::ALLOWED_ROLES, true)) return $fail('Please select a valid role.');
        if (strlen($password) < 6) return $fail('Password must be at least 6 characters.');
        if ($password !== $confirm) return $fail('Passwords do not match.');

        try {
            User::create($fullName, $username, $password, $role);
        } catch (PDOException $ex) {
            if ($ex->getCode() === '23000') return $fail('That username is already taken.');
            throw $ex;
        }
        $this->redirect('/login?registered=1');
    }

    public function logout()
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        header('Location: ' . url('/login'));
        exit;
    }
}
