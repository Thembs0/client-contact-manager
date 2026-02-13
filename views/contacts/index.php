<div class="actions">
    <a href="index.php?action=contacts&page=create" class="btn">Add New Contact</a>
</div>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
<?php endif; ?>

<?php if (empty($contacts)): ?>
    <div class="no-data">No contact(s) found.</div>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Surname</th>
                <th>Email address</th>
                <th class="text-center">No. of linked clients</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($contacts as $contact): ?>
            <tr>
                <td><?php echo htmlspecialchars($contact['name']); ?></td>
                <td><?php echo htmlspecialchars($contact['surname']); ?></td>
                <td><?php echo htmlspecialchars($contact['email']); ?></td>
                <td class="text-center"><?php echo $contact['client_count'] ?? 0; ?></td>
                <td>
                    <a href="index.php?action=contacts&page=view&id=<?php echo $contact['id']; ?>" class="btn-link">View</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>