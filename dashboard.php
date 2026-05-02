<?php require 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$stmt = $pdo->prepare("SELECT ug.id, g.title, ug.status, ug.rating, ug.notes, ug.date_added, ug.date_completed
                       FROM user_games ug JOIN games g ON ug.game_id = g.id
                       WHERE ug.user_id = ?
                       ORDER BY ug.date_added DESC");
$stmt->execute([$_SESSION['user_id']]);
$library = $stmt->fetchAll();

require 'header.php';
?>
<h2>My Game Library</h2>
<?php if (empty($library)): ?>
    <p>Your library is empty. Browse the <a href="index.php">catalog</a> and add games!</p>
<?php else: ?>
    <table class="table table-striped">
        <thead><tr><th>Game</th><th>Status</th><th>Rating</th><th>Notes</th><th>Date Added</th><th>Completed</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($library as $entry): ?>
            <tr>
                <td><?= htmlspecialchars($entry['title']) ?></td>
                <td><span class="badge bg-<?= $entry['status']=='finished'?'success':($entry['status']=='playing'?'primary':'secondary') ?>"><?= ucfirst(str_replace('_',' ',$entry['status'])) ?></span></td>
                <td><?= $entry['rating'] ?? '-' ?></td>
                <td><?= htmlspecialchars(substr($entry['notes'] ?? '', 0, 50)) ?></td>
                <td><?= $entry['date_added'] ?></td>
                <td><?= $entry['date_completed'] ?? '-' ?></td>
                <td><a href="edit_status.php?id=<?= $entry['id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php require 'footer.php'; ?>