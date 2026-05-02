<?php require 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$platforms = $pdo->query("SELECT * FROM platforms ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $release = $_POST['release_date'] ?: null;
    
    $stmt = $pdo->prepare("INSERT INTO games (title, description, release_date) VALUES (?, ?, ?)");
    $stmt->execute([$title, $desc, $release]);
    $gameId = $pdo->lastInsertId();
    
    if (!empty($_POST['categories'])) {
        $insCat = $pdo->prepare("INSERT INTO game_categories (game_id, category_id) VALUES (?, ?)");
        foreach ($_POST['categories'] as $catId) $insCat->execute([$gameId, $catId]);
    }
    if (!empty($_POST['platforms'])) {
        $insPlat = $pdo->prepare("INSERT INTO game_platforms (game_id, platform_id) VALUES (?, ?)");
        foreach ($_POST['platforms'] as $platId) $insPlat->execute([$gameId, $platId]);
    }
    header("Location: game.php?id=$gameId");
    exit;
}
require 'header.php';
?>
<h2>Add New Game</h2>
<form method="post">
  <div class="mb-3"><input name="title" class="form-control" placeholder="Game Title" required></div>
  <div class="mb-3"><textarea name="description" class="form-control" placeholder="Description"></textarea></div>
  <div class="mb-3"><input type="date" name="release_date" class="form-control"></div>
  <div class="mb-3"><label>Categories</label><br>
    <?php foreach ($categories as $cat): ?>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="categories[]" value="<?= $cat['id'] ?>" id="cat<?= $cat['id'] ?>">
        <label class="form-check-label" for="cat<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></label>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="mb-3"><label>Platforms</label><br>
    <?php foreach ($platforms as $plat): ?>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="platforms[]" value="<?= $plat['id'] ?>" id="plat<?= $plat['id'] ?>">
        <label class="form-check-label" for="plat<?= $plat['id'] ?>"><?= htmlspecialchars($plat['name']) ?></label>
      </div>
    <?php endforeach; ?>
  </div>
  <button class="btn btn-primary">Add Game</button>
</form>
<?php require 'footer.php'; ?>