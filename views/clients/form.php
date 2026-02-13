<div class="form-container">
    <h2><?php echo isset($client) ? 'Edit Client' : 'Create New Client'; ?></h2>
    
    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="index.php?action=clients&page=create" onsubmit="return validateClientForm()">
        <div class="tabs">
            <div class="tab-header">
                <button type="button" class="tab-link active" data-tab="general-tab">General</button>
                <button type="button" class="tab-link" data-tab="contacts-tab">Contact(s)</button>
            </div>
            
            <div id="general-tab" class="tab-content active">
                <div class="form-group">
                    <label for="name">Name *</label>
                    <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="client_code">Client Code</label>
                    <input type="text" id="client_code" name="client_code" value="<?php echo isset($client['client_code']) ? htmlspecialchars($client['client_code']) : ''; ?>" readonly>
                    <?php if (!isset($client['client_code'])): ?>
                        <small>Client code will be auto-generated upon save</small>
                    <?php endif; ?>
                </div>
            </div>
            
            <div id="contacts-tab" class="tab-content">
                <h3>Linked Contacts</h3>
                <?php
                $linkedContacts = isset($client) ? $this->clientModel->getLinkedContacts($client['id']) : [];
                ?>
                
                <?php if (empty($linkedContacts)): ?>
                    <p class="no-data">No contacts found.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Contact Full Name</th>
                                <th>Contact email address</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($linkedContacts as $contact): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($contact['surname'] . ' ' . $contact['name']); ?></td>
                                <td><?php echo htmlspecialchars($contact['email']); ?></td>
                                <td>
                                    <a href="#" class="unlink-btn" data-client-id="<?php echo $client['id']; ?>" data-contact-id="<?php echo $contact['id']; ?>">Unlink</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                
                <?php if (isset($client)): ?>
                    <h4>Link New Contact</h4>
                    <form id="link-contact-form" method="POST">
                        <input type="hidden" name="client_id" value="<?php echo $client['id']; ?>">
                        <div class="form-group">
                            <select name="contact_id" required>
                                <option value="">Select a contact...</option>
                                <?php
                                $allContacts = $this->contactModel->findAll('surname', 'ASC');
                                $linkedContactIds = array_column($linkedContacts, 'id');
                                foreach ($allContacts as $contact):
                                    if (!in_array($contact['id'], $linkedContactIds)):
                                ?>
                                    <option value="<?php echo $contact['id']; ?>">
                                        <?php echo htmlspecialchars($contact['surname'] . ' ' . $contact['name'] . ' (' . $contact['email'] . ')'); ?>
                                    </option>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn">Link Contact</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-group mt-20">
            <button type="submit" class="btn">Save Client</button>
            <a href="index.php?action=clients&page=index" class="btn">Cancel</a>
        </div>
    </form>
</div>