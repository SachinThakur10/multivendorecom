<?php
require_once 'models/User.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = sanitize($_POST['email']);
            $password = $_POST['password'];
            
            if (empty($email) || empty($password)) {
                setFlashMessage('error', 'Please fill in all fields');
                redirect('?page=auth&action=login');
            }
            
            $user = $this->userModel->verifyLogin($email, $password);
            
            if ($user) {
                if ($user['status'] !== 'active') {
                    setFlashMessage('error', 'Your account is not active');
                    redirect('?page=auth&action=login');
                }
                
                // Set session data
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                setFlashMessage('success', 'Login successful');
                
                // Redirect based on role
                switch ($user['role']) {
                    case 'admin':
                        redirect('?page=admin');
                        break;
                    case 'vendor':
                        redirect('?page=vendor');
                        break;
                    default:
                        redirect('?page=home');
                        break;
                }
            } else {
                setFlashMessage('error', 'Invalid email or password');
                redirect('?page=auth&action=login');
            }
        }
        
        $this->render('auth/login', ['title' => 'Login - ' . SITE_NAME]);
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = sanitize($_POST['name']);
            $email = sanitize($_POST['email']);
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];
            $role = sanitize($_POST['role']);
            $phone = sanitize($_POST['phone']);
            
            // Validation
            $errors = [];
            
            if (empty($name)) $errors[] = 'Name is required';
            if (empty($email)) $errors[] = 'Email is required';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format';
            if (empty($password)) $errors[] = 'Password is required';
            if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters';
            if ($password !== $confirmPassword) $errors[] = 'Passwords do not match';
            if (!in_array($role, ['customer', 'vendor'])) $errors[] = 'Invalid role selected';
            
            // Check if email already exists
            if ($this->userModel->findByEmail($email)) {
                $errors[] = 'Email already exists';
            }
            
            if (!empty($errors)) {
                setFlashMessage('error', implode('<br>', $errors));
                redirect('?page=auth&action=register');
            }
            
            // Create user
            $userData = [
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'phone' => $phone,
                'role' => $role,
                'status' => 'active'
            ];
            
            $userId = $this->userModel->createUser($userData);
            
            if ($userId) {
                // If vendor, create vendor record
                if ($role === 'vendor') {
                    $this->createVendorRecord($userId, $_POST);
                }
                
                setFlashMessage('success', 'Registration successful. Please login.');
                redirect('?page=auth&action=login');
            } else {
                setFlashMessage('error', 'Registration failed. Please try again.');
                redirect('?page=auth&action=register');
            }
        }
        
        $this->render('auth/register', ['title' => 'Register - ' . SITE_NAME]);
    }
    
    private function createVendorRecord($userId, $postData) {
        global $pdo;
        
        $vendorData = [
            'user_id' => $userId,
            'shop_name' => sanitize($postData['shop_name']),
            'description' => sanitize($postData['description']),
            'address' => sanitize($postData['address']),
            'city' => sanitize($postData['city']),
            'state' => sanitize($postData['state']),
            'pincode' => sanitize($postData['pincode']),
            'gst_number' => sanitize($postData['gst_number']),
            'approval_status' => 'pending'
        ];
        
        $stmt = $pdo->prepare("INSERT INTO vendors (user_id, shop_name, description, address, city, state, pincode, gst_number, approval_status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        
        $stmt->execute([
            $vendorData['user_id'],
            $vendorData['shop_name'],
            $vendorData['description'],
            $vendorData['address'],
            $vendorData['city'],
            $vendorData['state'],
            $vendorData['pincode'],
            $vendorData['gst_number'],
            $vendorData['approval_status']
        ]);
    }
    
    public function logout() {
        session_destroy();
        setFlashMessage('success', 'Logged out successfully');
        redirect('?page=home');
    }
    
    public function profile() {
        if (!isLoggedIn()) {
            redirect('?page=auth&action=login');
        }
        
        $user = getCurrentUser();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = sanitize($_POST['name']);
            $phone = sanitize($_POST['phone']);
            
            $updateData = [
                'name' => $name,
                'phone' => $phone
            ];
            
            if ($this->userModel->update($user['id'], $updateData)) {
                $_SESSION['user_name'] = $name;
                setFlashMessage('success', 'Profile updated successfully');
            } else {
                setFlashMessage('error', 'Failed to update profile');
            }
            
            redirect('?page=auth&action=profile');
        }
        
        $this->render('auth/profile', [
            'title' => 'Profile - ' . SITE_NAME,
            'user' => $user
        ]);
    }
    
    public function changePassword() {
        if (!isLoggedIn()) {
            redirect('?page=auth&action=login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentPassword = $_POST['current_password'];
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];
            
            $user = getCurrentUser();
            
            // Validation
            $errors = [];
            
            if (!verifyPassword($currentPassword, $user['password'])) {
                $errors[] = 'Current password is incorrect';
            }
            
            if (strlen($newPassword) < 6) {
                $errors[] = 'New password must be at least 6 characters';
            }
            
            if ($newPassword !== $confirmPassword) {
                $errors[] = 'New passwords do not match';
            }
            
            if (!empty($errors)) {
                setFlashMessage('error', implode('<br>', $errors));
                redirect('?page=auth&action=changePassword');
            }
            
            if ($this->userModel->updatePassword($user['id'], $newPassword)) {
                setFlashMessage('success', 'Password changed successfully');
            } else {
                setFlashMessage('error', 'Failed to change password');
            }
            
            redirect('?page=auth&action=profile');
        }
        
        $this->render('auth/change-password', ['title' => 'Change Password - ' . SITE_NAME]);
    }
    
    private function render($view, $data = []) {
        extract($data);
        include "views/layout/header.php";
        include "views/{$view}.php";
        include "views/layout/footer.php";
    }
}
?>
