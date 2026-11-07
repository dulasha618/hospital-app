<?php
class ProfileController extends Controller
{
    public function show()
    {
        $account = User::findById(currentUser()['id']);
        $this->render('profile', ['title' => 'My Profile', 'account' => $account, 'error' => null], 200, 'container narrow');
    }

    // UPDATE own name / password
    public function update()
    {
        $account = User::findById(currentUser()['id']);
        $fullName = trim($this->post('fullName'));
        $current = $this->post('currentPassword');
        $new = $this->post('newPassword');
        $confirm = $this->post('confirmPassword');

        $fail = function (string $msg) use ($account, $fullName) {
            $this->render('profile', ['title' => 'My Profile', 'account' => array_merge($account, ['fullName' => $fullName]), 'error' => $msg], 400, 'container narrow');
        };

        if ($fullName === '') return $fail('Full name is required.');

        if ($new !== '' || $current !== '' || $confirm !== '') {
            if ($current === '' || !User::verifyPassword($current, $account['password'])) return $fail('Current password is incorrect.');
            if (strlen($new) < 6) return $fail('New password must be at least 6 characters.');
            if ($new !== $confirm) return $fail('New passwords do not match.');
            User::updatePassword((int) $account['id'], $new);
        }

        User::updateName((int) $account['id'], $fullName);
        $_SESSION['user']['fullName'] = $fullName;
        flash('success', 'Profile updated successfully.');
        $this->redirect('/profile');
    }
}
