<?php
require_once 'BaseModel.php';

class User extends BaseModel {
    protected $table = 'users';
    
    // Find user by email
    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    // Create new user
    public function createUser($data) {
        // Hash password before storing
        if (isset($data['password'])) {
            $data['password'] = hashPassword($data['password']);
        }
        
        // Set default values
        $data['created_at'] = date('Y-m-d H:i:s');
        
        return $this->create($data);
    }
    
    // Update user password
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = hashPassword($newPassword);
        return $this->update($userId, ['password' => $hashedPassword]);
    }
    
    // Verify user login
    public function verifyLogin($email, $password) {
        $user = $this->findByEmail($email);
        
        if ($user && verifyPassword($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    // Get user with vendor details
    public function getUserWithVendor($userId) {
        $sql = "SELECT u.*, v.* FROM users u 
                LEFT JOIN vendors v ON u.id = v.user_id 
                WHERE u.id = ?";
        return $this->queryOne($sql, [$userId]);
    }
    
    // Get all vendors
    public function getVendors($status = 'approved') {
        $sql = "SELECT u.name, u.email, u.created_at, v.* 
                FROM users u 
                INNER JOIN vendors v ON u.id = v.user_id 
                WHERE v.approval_status = ? 
                ORDER BY v.created_at DESC";
        return $this->query($sql, [$status]);
    }
    
    // Get customers
    public function getCustomers() {
        return $this->findAll(['role' => 'customer'], 'created_at DESC');
    }
    
    // Update user status
    public function updateStatus($userId, $status) {
        return $this->update($userId, ['status' => $status]);
    }
    
    // Get user statistics
    public function getUserStats() {
        $sql = "SELECT 
                    COUNT(*) as total_users,
                    SUM(CASE WHEN role = 'customer' THEN 1 ELSE 0 END) as customers,
                    SUM(CASE WHEN role = 'vendor' THEN 1 ELSE 0 END) as vendors,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_users
                FROM users";
        return $this->queryOne($sql);
    }
}
?>
