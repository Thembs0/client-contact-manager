<div class="form-container">
    <h2><?php echo isset($contact) ? 'Edit Contact' : 'Create New Contact'; ?></h2>
    
    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $field => $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="index.php?action=contacts&page=create" onsubmit="return validateContactForm()">
        <div class="tabs">
            <div class="tab-header">
                <button type="button" class="tab-link active" data-tab="general-tab">General</button>
                <button type="button" class="tab-link" data-tab="clients-tab">Clients</button>
            </div>
            
            <div id="general-tab" class="tab-content active">
                <div class="form-group">
                    <label for="name">Name *</label>
                    <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : (isset($contact['name']) ? htmlspecialchars($contact['name']) : ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="surname">Surname *</label>
                    <input type="text" id="surname" name="surname" value="<?php echo isset($_POST['surname']) ? htmlspecialchars($_POST['surname']) : (isset($contact['surname']) ? htmlspecialchars($contact['surname']) : ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : (isset($contact['email']) ? htmlspecialchars($contact['email']) : ''); ?>" required>
                </div>
            </div>
            
            <div id="clients-tab" class="tab-content">
                <h3>Linked Clients</h3>
                <?php
                $linkedClients = isset($contact) ? $this->contactModel->getLinkedClients($contact['id']) : [];
                ?>
                
                <?php if (empty($linkedClients)): ?>
                    <p class="no-data">No contact found.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Client name</th>
                                <th>Client code</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($linkedClients as $client): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($client['name']); ?></td>
                                <td><?php echo htmlspecialchars($client['client_code']); ?></td>
                                <td>
                                    <a href="#" class="unlink-btn" data-client-id="<?php echo $client['id']; ?>" data-contact-id="<?php echo $contact['id']; ?>">Unlink</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                
                <?php if (isset($contact)): ?>
                    <h4>Link New Client</h4>
                    <form id="link-client-form" method="POST" action="api/link_client_contact.php">
                        <input type="hidden" name="contact_id" value="<?php echo $contact['id']; ?>">
                        <div class="form-group">
                            <select name="client_id" required>
                                <option value="">Select a client...</option>
                                <?php
                                $allClients = $this->clientModel->findAll('name', 'ASC');
                                $linkedClientIds = array_column($linkedClients, 'id');
                                foreach ($allClients as $client):
                                    if (!in_array($client['id'], $linkedClientIds)):
                                ?>
                                    <option value="<?php echo $client['id']; ?>">
                                        <?php echo htmlspecialchars($client['name'] . ' (' . $client['client_code'] . ')'); ?>
                                    </option>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn">Link Client</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-group mt-20">
            <button type="submit" class="btn">Save Contact</button>
            <a href="index.php?action=contacts&page=index" class="btn">Cancel</a>
        </div>
    </form>
</div>