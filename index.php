<?php require 'config.php'; require 'header.php';

$search = $_GET['search'] ?? '';
$category_slug = $_GET['category'] ?? '';
$platform_slug = $_GET['platform'] ?? '';

$where = [];
$params = [];
if ($search) {
    $where[] = "g.title LIKE ?";
    $params[] = "%$search%";
}
if ($category_slug) {
    $where[] = "c.slug = ?";
    $params[] = $category_slug;
}
if ($platform_slug) {
    $where[] = "p.slug = ?";
    $params[] = $platform_slug;
}

$sql = "SELECT DISTINCT g.id, g.title, g.release_date
        FROM games g
        LEFT JOIN game_categories gc ON g.id = gc.game_id
        LEFT JOIN categories c ON gc.category_id = c.id
        LEFT JOIN game_platforms gp ON g.id = gp.game_id
        LEFT JOIN platforms p ON gp.platform_id = p.id";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY g.title";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$games = $stmt->fetchAll();

// Get all categories & platforms for filter dropdowns
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$platforms = $pdo->query("SELECT * FROM platforms ORDER BY name")->fetchAll();
?>

<h2>Game Catalog</h2>
<form class="row g-3 mb-4" method="get">
  <div class="col-md-4"><input name="search" class="form-control" placeholder="Search by title" value="<?= htmlspecialchars($search) ?>"></div>
  <div class="col-md-3">
    <select name="category" class="form-select"><option value="">All Categories</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= $cat['slug'] ?>" <?= $category_slug==$cat['slug']?'selected':'' ?>><?= htmlspecialchars($cat['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-3">
    <select name="platform" class="form-select"><option value="">All Platforms</option>
      <?php foreach ($platforms as $plat): ?>
        <option value="<?= $plat['slug'] ?>" <?= $platform_slug==$plat['slug']?'selected':'' ?>><?= htmlspecialchars($plat['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-2"><button class="btn btn-outline-primary">Filter</button></div>
</form>

<div class="row">
  <?php foreach ($games as $game): ?>
    <div class="col-md-4 mb-3">
      <div class="card h-100">
        <div class="card-body">
          <h5 class="card-title"><?= htmlspecialchars($game['title']) ?></h5>
          <p class="card-text"><small class="text-muted">Released: <?= $game['release_date'] ?? 'TBA' ?></small></p>
          <a href="game.php?id=<?= $game['id'] ?>" class="btn btn-sm btn-primary">View Details</a>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php require 'footer.php'; ?>