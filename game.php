<?php require 'config.php';
if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$gameStmt = $pdo->prepare("SELECT * FROM games WHERE id = ?");
$gameStmt->execute([$_GET['id']]);
$game = $gameStmt->fetch();
if (!$game) { header("Location: index.php"); exit; }

// Get categories
$catStmt = $pdo->prepare("SELECT c.name FROM game_categories gc JOIN categories c ON gc.category_id = c.id WHERE gc.game_id = ?");
$catStmt->execute([$game['id']]);
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);

// Get platforms
$platStmt = $pdo->prepare("SELECT p.name FROM game_platforms gp JOIN platforms p ON gp.platform_id = p.id WHERE gp.game_id = ?");
$platStmt->execute([$game['id']]);
$platforms = $platStmt->fetchAll(PDO::FETCH_COLUMN);

// Add to user library (logged-in users only)
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $status = $_POST['status'] ?? 'want_to_play';
    $notes = trim($_POST['notes'] ?? '');
    $rating = (int)($_POST['rating'] ?? 0) ?: null;
    $date_completed = ($status === 'finished' && !empty($_POST['date_completed'])) ? $_POST['date_completed'] : null;

    $ins = $pdo->prepare("INSERT INTO user_games (user_id, game_id, status, notes, rating, date_completed)
                          VALUES (?,?,?,?,?,?)
                          ON DUPLICATE KEY UPDATE status=VALUES(status), notes=VALUES(notes), rating=VALUES(rating), date_completed=VALUES(date_completed)");
    try {
        $ins->execute([$_SESSION['user_id'], $game['id'], $status, $notes, $rating, $date_completed]);
        $message = '<div class="alert alert-success">Game added/updated in your library.</div>';
    } catch (PDOException $e) {
        $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
    }
}

require 'header.php';
echo $message;
?>
<h2><?= htmlspecialchars($game['title']) ?></h2>
<p><?= nl2br(htmlspecialchars($game['description'] ?? '')) ?></p>
<p><strong>Release Date:</strong> <?= $game['release_date'] ?? 'TBA' ?></p>
<p><strong>Categories:</strong> <?= $categories ? implode(', ', $categories) : 'None' ?></p>
<p><strong>Platforms:</strong> <?= $platforms ? implode(', ', $platforms) : 'None' ?></p>

<?php if (isset($_SESSION['user_id'])): ?>
    <hr>
    <h4>Manage in Your Library</h4>
    <?php
    // Check if already in library
    $check = $pdo->prepare("SELECT * FROM user_games WHERE user_id=? AND game_id=?");
    $check->execute([$_SESSION['user_id'], $game['id']]);
    $existing = $check->fetch();
    ?>
    <form method="post">
      <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="want_to_play" <?= ($existing && $existing['status']=='want_to_play')?'selected':'' ?>>Want to Play</option>
          <option value="playing" <?= ($existing && $existing['status']=='playing')?'selected':'' ?>>Playing</option>
          <option value="finished" <?= ($existing && $existing['status']=='finished')?'selected':'' ?>>Finished</option>
          <option value="dropped" <?= ($existing && $existing['status']=='dropped')?'selected':'' ?>>Dropped</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Rating (1-10)</label>
        <input type="number" name="rating" class="form-control" min="1" max="10" value="<?= $existing['rating'] ?? '' ?>">
      </div>
      <div class="mb-3" id="completedDateGroup" style="display: <?= ($existing && $existing['status']=='finished')?'block':'none' ?>;">
        <label class="form-label">Date Completed</label>
        <input type="date" name="date_completed" class="form-control" value="<?= $existing['date_completed'] ?? '' ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control"><?= htmlspecialchars($existing['notes'] ?? '') ?></textarea>
      </div>
      <button class="btn btn-success">Save to Library</button>
    </form>
    <script>
      // Show/hide date completed based on status
      document.querySelector('select[name="status"]').addEventListener('change', function(){
        document.getElementById('completedDateGroup').style.display = this.value === 'finished' ? 'block' : 'none';
      });
    </script>
<?php else: ?>
    <p><a href="login.php">Login</a> to add this game to your library.</p>
<?php endif; ?>
<?php require 'footer.php'; ?>