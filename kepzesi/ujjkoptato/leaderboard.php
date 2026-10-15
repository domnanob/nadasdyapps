<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leaderboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <?php
  require_once __DIR__ . '/../includes/database.php';
  $conn = kepzesi_database_connection('ujjkoptato_jatek');

  $result = $conn->query('SELECT username, score FROM ujjkoptato_leaderboard ORDER BY score DESC LIMIT 10');
  ?>

    <table class="table">
      <thead>
        <h1 class="my-4 text-center"> 🏆 <span style="color:#1DB954;">Ranglista</span> 🏆</h1>
        <tr>
          <th scope="col">Rank</th>
          <th scope="col">Név</th>
          <th scope="col">Pont</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $scores = $result->fetchAll();
        if ($scores) {
            $rank = 1;
            foreach ($scores as $row) {
                echo "<tr>";
                echo "<td scope='row' style='font-weight:bold'>" . $rank . "</td>";
                echo "<td>" . htmlspecialchars($row["username"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["score"]) . "</td>";
                echo "</tr>";
                $rank++;
            }
        } else {
            echo "<tr><td colspan='3'>No results found</td></tr>";
        }
        ?>
      </tbody>
    </table>
<div class="text-center">
    <a href="index.php" class="btn btn-lg" id="leadera" style="background-color:#1DB954; color:white;">
        Vissza a menübe
    </a>
</div>
</body>
</html>