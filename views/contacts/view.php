<div class="form-container">
    <h2>Contact Details: <?php echo htmlspecialchars($contact['name'] . ' ' . $contact['surname']); ?></h2>
    
    <div class="tabs">
        <div class="tab-header">
            <button type="button" class="tab-link active" data-tab="general-tab">General</button>
            <button type="button" class="tab-link" data-tab="clients-tab">Clients</button>
        </div>
        
        <div id="general-tab" class="tab-content active">
            <table>
                <tr>
                    <th>Name:</th>
                    <td><?php echo htmlspecialchars($contact['name']); ?></td>
                </tr>
                <tr>
                    <th>Surname:</th>
                    <td><?php echo htmlspecialchars($contact['surname']); ?></td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td><?php echo htmlspecialchars($contact['email']); ?></td>
                </tr>
                <tr>
                    <th>Created:</th>
                    <td><?php echo date('Y-m-d H:i:s', strtotime($contact['created_at'])); ?></td>
                </tr>
            </table>
        </div>
        
        <div id="clients-tab" class="tab-content">
            <h3>Linked Clients</h3>
            
            <?php if (empty($linkedClients)): ?>
                <p class="no-data">No clients found.</p>
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
            
            <h4>Link New Client</h4>
            <form id="link-client-form" method="POST" action="api/link_client_contact.php">
                <input type="hidden" name="contact_id" value="<?php echo $contact['id']; ?>">
                <div class="form-group">
                    <select name="client_id" required>
                        <option value="">Select a client...</option>
                        <?php
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
        </div>
    </div>
    
    <div class="actions mt-20">
        <a href="index.php?action=contacts&page=index" class="btn">Back to Contacts</a>
    </div>
</div>