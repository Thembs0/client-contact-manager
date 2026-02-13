<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Contact Manager</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Client Contact Manager</h1>
            <nav>
                <ul>
                    <li><a href="index.php?action=clients&page=index">Clients</a></li>
                    <li><a href="index.php?action=contacts&page=index">Contacts</a></li>
                </ul>
            </nav>
        </header>
        
        <main>
            <?php
            // Simple routing
            $action = isset($_GET['action']) ? $_GET['action'] : 'clients';
            $page = isset($_GET['page']) ? $_GET['page'] : 'index';
            $id = isset($_GET['id']) ? $_GET['id'] : null;
            
            switch ($action) {
                case 'clients':
                    require_once 'controllers/ClientController.php';
                    $controller = new ClientController();
                    
                    if ($page === 'create') {
                        $controller->create();
                    } elseif ($page === 'view' && $id) {
                        $controller->view($id);
                    } elseif ($page === 'linkContact') {
                        $controller->linkContact();
                    } elseif ($page === 'unlinkContact') {
                        $controller->unlinkContact();
                    } else {
                        $controller->index();
                    }
                    break;
                    
                case 'contacts':
                    require_once 'controllers/ContactController.php';
                    $controller = new ContactController();
                    
                    if ($page === 'create') {
                        $controller->create();
                    } elseif ($page === 'view' && $id) {
                        $controller->view($id);
                    } elseif ($page === 'linkClient') {
                        $controller->linkClient();
                    } elseif ($page === 'unlinkClient') {
                        $controller->unlinkClient();
                    } else {
                        $controller->index();
                    }
                    break;
                    
                default:
                    header('Location: index.php?action=clients&page=index');
                    exit;
            }
            ?>
        </main>
    </div>
    
    <script src="assets/js/main.js"></script>
</body>
</html>