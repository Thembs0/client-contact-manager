<div class="form-container">
    <h2>Client Details: <?php echo htmlspecialchars($client['name']); ?></h2>
    
    <div class="tabs">
        <div class="tab-header">
            <button type="button" class="tab-link active" data-tab="general-tab">General</button>
            <button type="button" class="tab-link" data-tab="contacts-tab">Contact(s)</button>
        </div>
        
        <div id="general-tab" class="tab-content active">
            <table>
                <tr>
                    <th>Name:</th>
                    <td><?php echo htmlspecialchars($client['name']); ?></td>
                </tr>
                <tr>
                    <th>Client Code:</th>
                    <td><?php echo htmlspecialchars($client['client_code']); ?></td>
                </tr>
                <tr>
                    <th>Created:</th>
                    <td><?php echo date('Y-m-d H:i:s', strtotime($client['created_at'])); ?></td>
                </tr>
            </table>
        </div>
        
        <div id="contacts-tab" class="tab-content">
            <h3>Linked Contacts</h3>
            
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
            
            <h4>Link New Contact</h4>
            <form id="link-contact-form" method="POST">
                <input type="hidden" name="client_id" value="<?php echo $client['id']; ?>">
                <div class="form-group">
                    <select name="contact_id" required>
                        <option value="">Select a contact...</option>
                        <?php
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
        </div>
    </div>
    
    <div class="actions mt-20">
        <a href="index.php?action=clients&page=index" class="btn">Back to Clients</a>
    </div>
</div>