<?php
require_once __DIR__ . '/../models/Contact.php';
require_once __DIR__ . '/../models/Client.php';

class ContactController {
    private $contactModel;
    private $clientModel;
    
    public function __construct() {
        $this->contactModel = new Contact();
        $this->clientModel = new Client();
    }
    
    public function index() {
        $contacts = $this->contactModel->getClientCount();
        include __DIR__ . '/../views/contacts/index.php';
    }
    
    public function create() {
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->contactModel->create($_POST);
            
            if ($result['success']) {
                $_SESSION['success_message'] = 'Contact created successfully';
                header('Location: index.php?action=contacts&page=index');
                exit;
            } else {
                $errors = $result['errors'];
            }
        }
        
        include __DIR__ . '/../views/contacts/form.php';
    }
    
    public function view($id) {
        $contact = $this->contactModel->findById($id);
        if (!$contact) {
            $_SESSION['error_message'] = 'Contact not found';
            header('Location: index.php?action=contacts&page=index');
            exit;
        }
        
        $linkedClients = $this->contactModel->getLinkedClients($id);
        $allClients = $this->clientModel->findAll('name', 'ASC');
        
        include __DIR__ . '/../views/contacts/view.php';
    }
    
    public function linkClient() {
        // Clear any output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: application/json');
        
        $response = ['success' => false, 'message' => 'Unknown error'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_id']) && isset($_POST['client_id'])) {
            $contactId = $_POST['contact_id'];
            $clientId = $_POST['client_id'];
            
            try {
                $success = $this->contactModel->linkClient($contactId, $clientId);
                
                if ($success) {
                    $response = ['success' => true, 'message' => 'Client linked successfully'];
                } else {
                    $response = ['success' => false, 'message' => 'Client already linked or failed to link'];
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
    
    // ADD THIS MISSING METHOD
    public function unlinkClient() {
        // Clear any output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: application/json');
        
        $response = ['success' => false, 'message' => 'Unknown error'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_id']) && isset($_POST['client_id'])) {
            $contactId = $_POST['contact_id'];
            $clientId = $_POST['client_id'];
            
            try {
                $success = $this->contactModel->unlinkClient($contactId, $clientId);
                
                if ($success) {
                    $response = ['success' => true, 'message' => 'Client unlinked successfully'];
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