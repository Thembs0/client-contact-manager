<?php
// Get base_url if not defined
if (!isset($base_url)) {
    $base_url = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'];
    $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
}
?>
<div class="actions">
    <a href="<?php echo $base_url; ?>?action=clients&page=create" class="btn">Add New Client</a>
</div>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
<?php endif; ?>

<?php if (empty($clients)): ?>
    <div class="no-data">No clients found.</div>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Client code</th>
                <th class="text-center">No. of linked contacts</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clients as $client): ?>
            <tr>
                <td><?php echo htmlspecialchars($client['name']); ?></td>
                <td><?php echo htmlspecialchars($client['client_code']); ?></td>
                <td class="text-center"><?php echo isset($countMap[$client['id']]) ? $countMap[$client['id']] : 0; ?></td>
                <td>
                    <a href="<?php echo $base_url; ?>?action=clients&page=view&id=<?php echo $client['id']; ?>" class="btn-link">View</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>