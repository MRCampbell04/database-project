<?php require 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
if (!isset($_GET['id'])) { header("Location: dashboard.php"); exit; }

$stmt = $pdo->prepare("SELECT * FROM user_games WHERE id=? AND user_id=?");
$stmt->execute([$_GET['id'], $_SESSION['user_id']]);
$entry = $stmt->fetch();
if (!$entry) { header("Location: dashboard.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        $del = $pdo->prepare("DELETE FROM user_games WHERE id=? AND user_id=?");
        $del->execute([$entry['id'], $_SESSION['user_id']]);
        header("Location: dashboard.php");
        exit;
    }
    // Update
    $status = $_POST['status'];
    $rating = $_POST['rating'] ?: null;
    $notes = trim($_POST['notes']);
    $date_completed = ($status === 'finished' && !empty($_POST['date_completed'])) ? $_POST['date_completed'] : null;
    $upd = $pdo->prepare("UPDATE user_games SET status=?, rating=?, notes=?, date_completed=? WHERE id=? AND user_id=?");
    $upd->execute([$status, $rating, $notes, $date_completed, $entry['id'], $_SESSION['user_id']]);
    header("Location: dashboard.php");
    exit;
}

require 'header.php';
?>
<h2>Edit Library Entry</h2>
<form method="post">
  <div class="mb-3"><label>Status</label>
      <select name="status" class="form-select">
          <option value="want_to_play" <?= $entry['status']=='want_to_play'?'selected':'' ?>>Want to Play</option>
          <option value="playing" <?= $entry['status']=='playing'?'selected':'' ?>>Playing</option>
          <option value="finished" <?= $entry['status']=='finished'?'selected':'' ?>>Finished</option>
          <option value="dropped" <?= $entry['status']=='dropped'?'selected':'' ?>>Dropped</option>
      </select>
  </div>
  <div class="mb-3"><label>Rating</label><input type="number" name="rating" min="1" max="10" class="form-control" value="<?= $entry['rating'] ?>"></div>
  <div class="mb-3" id="completedDateGroup" style="display: <?= $entry['status']=='finished'?'block':'none' ?>"><label>Date Completed</label><input type="date" name="date_completed" class="form-control" value="<?= $entry['date_completed'] ?>"></div>
  <div class="mb-3"><label>Notes</label><textarea name="notes" class="form-control"><?= htmlspecialchars($entry['notes'] ?? '') ?></textarea></div>
  <button class="btn btn-primary">Update</button>
  <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Delete this entry?')">Remove from Library</button>
</form>
<script>
  document.querySelector('select[name="status"]').addEventListener('change', function(){
    document.getElementById('completedDateGroup').style.display = this.value === 'finished' ? 'block' : 'none';
  });
</script>
<?php require 'footer.php'; ?>