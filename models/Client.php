<?php
require_once __DIR__ . '/Model.php';

class Client extends Model {
    protected $table = 'clients';
    
// Add this method to your Client model if it doesn't exist
public function getDb() {
    return $this->db;
}
    
    public function create($data) {
        // Validate required fields
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Generate unique client code
        $clientCode = $this->generateClientCode($data['name']);
        
        $sql = "INSERT INTO {$this->table} (name, client_code) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        
        if ($stmt->execute([$data['name'], $clientCode])) {
            return [
                'success' => true, 
                'id' => $this->db->lastInsertId(),
                'client_code' => $clientCode
            ];
        }
        
        return ['success' => false, 'errors' => ['Failed to create client']];
    }
    
    public function update($id, $data) {
        // For updates, we don't regenerate client code
        $sql = "UPDATE {$this->table} SET name = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$data['name'], $id]);
    }
    
    public function getLinkedContacts($clientId) {
        $sql = "SELECT c.* FROM contacts c 
                INNER JOIN client_contact cc ON c.id = cc.contact_id 
                WHERE cc.client_id = ? 
                ORDER BY c.surname, c.name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$clientId]);
        return $stmt->fetchAll();
    }
    
    public function getContactCount($clientId = null) {
        if ($clientId) {
            $sql = "SELECT COUNT(*) as count FROM client_contact WHERE client_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$clientId]);
            $result = $stmt->fetch();
            return $result['count'];
        } else {
            $sql = "SELECT c.id, c.name, COUNT(cc.contact_id) as contact_count 
                    FROM clients c 
                    LEFT JOIN client_contact cc ON c.id = cc.client_id 
                    GROUP BY c.id";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        }
    }
    
    private function generateClientCode($name) {
        // Extract first 3 letters from name, uppercase
        $name = strtoupper(preg_replace('/[^A-Za-z]/', '', $name));
        $alphaPart = substr($name, 0, 3);
        
        // If name is shorter than 3 chars, pad with letters
        if (strlen($alphaPart) < 3) {
            $alphaPart = str_pad($alphaPart, 3, 'A');
        }
        
        // Find the next available number for this alpha prefix
        $numericPart = 1;
        $clientCode = $alphaPart . str_pad($numericPart, 3, '0', STR_PAD_LEFT);
        
        while ($this->clientCodeExists($clientCode)) {
            $numericPart++;
            $clientCode = $alphaPart . str_pad($numericPart, 3, '0', STR_PAD_LEFT);
        }
        
        return $clientCode;
    }
    
    private function clientCodeExists($clientCode) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM {$this->table} WHERE client_code = ?");
        $stmt->execute([$clientCode]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
    
    private function validate($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors['name'] = 'Client name is required';
        }
        
        return $errors;
    }

    // Add methods for linking/unlinking directly in the model
    public function linkContact($clientId, $contactId) {
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
            error_log("Error linking contact: " . $e->getMessage());
            return false;
        }
    }
    
public function unlinkContact($clientId, $contactId) {
    try {
        $sql = "DELETE FROM client_contact WHERE client_id = ? AND contact_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$clientId, $contactId]);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Error unlinking contact: " . $e->getMessage());
        return false;
    }
}
}
?>