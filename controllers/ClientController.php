<?php
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/Contact.php';

class ClientController {
    private $clientModel;
    private $contactModel;
    
    public function __construct() {
        $this->clientModel = new Client();
        $this->contactModel = new Contact();
    }
    
    public function index() {
        $clients = $this->clientModel->findAll('name', 'ASC');
        $contactCounts = $this->clientModel->getContactCount();
        
        // Create a map of client id to contact count
        $countMap = [];
        foreach ($contactCounts as $count) {
            $countMap[$count['id']] = $count['contact_count'];
        }
        
        include __DIR__ . '/../views/clients/index.php';
    }
    
    public function create() {
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->clientModel->create($_POST);
            
            if ($result['success']) {
                $_SESSION['success_message'] = 'Client created successfully';
                header('Location: index.php?action=clients&page=index');
                exit;
            } else {
                $errors = $result['errors'];
            }
        }
        
        include __DIR__ . '/../views/clients/form.php';
    }
    
    public function view($id) {
        $client = $this->clientModel->findById($id);
        if (!$client) {
            $_SESSION['error_message'] = 'Client not found';
            header('Location: index.php?action=clients&page=index');
            exit;
        }
        
        $linkedContacts = $this->clientModel->getLinkedContacts($id);
        $allContacts = $this->contactModel->findAll('surname', 'ASC');
        
        include __DIR__ . '/../views/clients/view.php';
    }
    
    public function linkContact() {
        // Clear any output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: application/json');
        
        $response = ['success' => false, 'message' => 'Unknown error'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['client_id']) && isset($_POST['contact_id'])) {
            $clientId = $_POST['client_id'];
            $contactId = $_POST['contact_id'];
            
            try {
                $success = $this->clientModel->linkContact($clientId, $contactId);
                
                if ($success) {
                    $response = ['success' => true, 'message' => 'Contact linked successfully'];
                } else {
                    $response = ['success' => false, 'message' => 'Contact already linked or failed to link'];
                }
            } catch (Exception $e) {
                $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
            }
        } else {
            $response = ['success' => false, 'message' => 'Invalid request parameters'];
        }
        
        echo json_encode($response);
        exit;
    }
    
    public function unlinkContact() {
        // Clear any output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: application/json');
        
        $response = ['success' => false, 'message' => 'Unknown error'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['client_id']) && isset($_POST['contact_id'])) {
            $clientId = $_POST['client_id'];
            $contactId = $_POST['contact_id'];
            
            try {
                $success = $this->clientModel->unlinkContact($clientId, $contactId);
                
                if ($success) {
                    $response = ['success' => true, 'message' => 'Contact unlinked successfully'];
                } else {
                    $response = ['success' => false, 'message' => 'Link not found or failed to unlink'];
                }
            } catch (Exception $e) {
                $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
            }
        } else {
            $response = ['success' => false, 'message' => 'Invalid request parameters'];
        }
        
        echo json_encode($response);
        exit;
    }
}
?>