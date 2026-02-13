<?php
require_once __DIR__ . '/Model.php';

class Contact extends Model {
    protected $table = 'contacts';
    
    // Add this method to safely access the database connection
    public function getDb() {
        return $this->db;
    }
    
    public function create($data) {
        // Validate required fields
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Check if email already exists
        if ($this->emailExists($data['email'])) {
            return ['success' => false, 'errors' => ['email' => 'Email address already exists']];
        }
        
        $sql = "INSERT INTO {$this->table} (name, surname, email) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        if ($stmt->execute([$data['name'], $data['surname'], $data['email']])) {
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        }
        
        return ['success' => false, 'errors' => ['Failed to create contact']];
    }
    
    public function update($id, $data) {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Check if email exists for other contacts
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM {$this->table} WHERE email = ? AND id != ?");
        $stmt->execute([$data['email'], $id]);
        if ($stmt->fetch()['count'] > 0) {
            return ['success' => false, 'errors' => ['email' => 'Email address already exists']];
        }
        
        $sql = "UPDATE {$this->table} SET name = ?, surname = ?, email = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$data['name'], $data['surname'], $data['email'], $id]);
    }
    
    public function getLinkedClients($contactId) {
        $sql = "SELECT c.* FROM clients c 
                INNER JOIN client_contact cc ON c.id = cc.client_id 
                WHERE cc.contact_id = ? 
                ORDER BY c.name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$contactId]);
        return $stmt->fetchAll();
    }
    
    public function getClientCount($contactId = null) {
        if ($contactId) {
            $sql = "SELECT c.*, COUNT(cc.client_id) as client_count 
                    FROM contacts c 
                    LEFT JOIN client_contact cc ON c.id = cc.contact_id 
                    GROUP BY c.id 
                    ORDER BY c.surname, c.name";  // This is correct$stmt = $this->db->prepare($sql);
            $stmt->execute([$contactId]);
            $result = $stmt->fetch();
            return $result['count'];
        } else {
            $sql = "SELECT c.*, COUNT(cc.client_id) as client_count 
                    FROM contacts c 
                    LEFT JOIN client_contact cc ON c.id = cc.contact_id 
                    GROUP BY c.id 
                    ORDER BY c.surname, c.name";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        }
    }
    
    private function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['count'] > 0;
    }
    
    private function validate($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors['name'] = 'Name is required';
        }
        
        if (empty($data['surname'])) {
            $errors['surname'] = 'Surname is required';
        }
        
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }
        
        return $errors;
    }

    // Add methods for linking/unlinking directly in the model
    public function linkClient($contactId, $clientId) {
        try {
            // Check if link already exists
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM client_contact WHERE client_id = ? AND contact_id = ?");
            $stmt->execute([$clientId, $contactId]);
            $result = $stmt->fetch();
            
            if ($result['count'] == 0) {
                $sql = "INSERT INTO client_contact (client_id, contact_id) VALUES (?, ?)";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$clientId, $contactId]);
            }
            return false; // Already linked
        } catch (PDOException $e) {
            error_log("Error linking client: " . $e->getMessage());
            return false;
        }
    }
    
public function unlinkClient($contactId, $clientId) {
    try {
        $sql = "DELETE FROM client_contact WHERE client_id = ? AND contact_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$clientId, $contactId]);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Error unlinking client: " . $e->getMessage());
        return false;
    }
}
}
?>