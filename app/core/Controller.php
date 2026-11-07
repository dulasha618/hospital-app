<?php
class Controller
{
    // Render app/views/<view>.php inside the shared layout
    protected function render(string $view, array $data = [], int $code = 200, string $mainClass = 'container'): void
    {
        http_response_code($code);
        $data['user'] = currentUser();
        extract($data, EXTR_SKIP);
        if (!isset($title)) $title = 'MediCore';
        ob_start();
        require dirname(__DIR__) . "/views/{$view}.php";
        $content = ob_get_clean();
        require dirname(__DIR__) . '/views/layout.php';
    }

    protected function redirect(string $path)
    {
        header('Location: ' . url($path));
        exit;
    }

    protected function error(int $code, string $title, string $heading, string $message, string $back = '/dashboard', string $backLabel = 'Dashboard'): void
    {
        $this->render('error', compact('title', 'heading', 'message', 'back', 'backLabel'), $code, 'container narrow');
    }

    protected function forbidden(): void
    {
        $this->error(403, 'Access Denied', '🚫 Access Denied', 'You do not have permission to do that.');
    }

    protected function notFound(string $back = '/dashboard', string $backLabel = 'Dashboard'): void
    {
        $this->error(404, 'Not Found', '🔍 Not Found', 'That record could not be found.', $back, $backLabel);
    }

    protected function post(string $key, string $default = ''): string
    {
        $v = $_POST[$key] ?? $default;
        return is_string($v) ? $v : $default;
    }

    // Public helpers used by the router / middleware
    public function renderDenied(string $role): void
    {
        $this->error(403, 'Access Denied', '🚫 Access Denied', "Your role ($role) does not have permission to open this page.");
    }

    public function renderCsrf(): void
    {
        $back = currentUser() ? '/dashboard' : '/login';
        $this->error(419, 'Session Expired', '⏱️ Session expired', 'Your form expired or was invalid. Please go back, refresh the page and try again.', $back, currentUser() ? 'Dashboard' : 'Login');
    }

    public function renderNotFoundPage(): void
    {
        $back = currentUser() ? '/dashboard' : '/login';
        $this->error(404, 'Page Not Found', '🔍 Page Not Found', 'The page you are looking for does not exist.', $back, currentUser() ? 'Dashboard' : 'Login');
    }

    public function renderServerError(): void
    {
        while (ob_get_level() > 0) { ob_end_clean(); }
        $back = currentUser() ? '/dashboard' : '/login';
        $this->error(500, 'Server Error', '⚠️ Something went wrong', 'A server or database error occurred. Check that MySQL is running and that database.sql was imported (see the PHP / Apache error log for details).', $back, currentUser() ? 'Dashboard' : 'Login');
    }
}
