<?php
/*
 * search.php — index-free search across Kirk's recipe pages.
 *
 * Place this file at /search.php. On each search it scans the recipe
 * subfolders directly (no stored index), so new recipes are searchable the
 * moment they're uploaded. Searches both the title and the page text.
 */

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

$ROOT    = __DIR__;            // the /kirk directory this file lives in
$EXCLUDE = ['css', 'images'];  // folders to skip
$results = [];

if ($q !== '') {
    $terms = preg_split('/\s+/', $q);

    foreach (glob($ROOT . '/*', GLOB_ONLYDIR) as $dir) {
        if (in_array(basename($dir), $EXCLUDE, true)) continue;

        foreach (glob($dir . '/*.html') as $file) {
            $html = @file_get_contents($file);
            if ($html === false) continue;

            // Title: prefer <title>, then <h1>, then a tidied filename.
            $title = '';
            if (preg_match('/<title>(.*?)<\/title>/is', $html, $m)) {
                $title = trim(strip_tags($m[1]));
            }
            if ($title === '' && preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $html, $m)) {
                $title = trim(strip_tags($m[1]));
            }
            if ($title === '') {
                $title = ucwords(str_replace(['_', '-'], ' ',
                          pathinfo($file, PATHINFO_FILENAME)));
            }

            // Build a searchable text blob (drop scripts/styles, then all tags).
            $text = preg_replace('/<script\b[^>]*>.*?<\/script>/is', ' ', $html);
            $text = preg_replace('/<style\b[^>]*>.*?<\/style>/is', ' ', $text);
            $text = $title . ' ' . strip_tags($text);

            // Require every term to appear somewhere (AND search, case-insensitive).
            $match = true;
            foreach ($terms as $t) {
                if ($t === '') continue;
                if (stripos($text, $t) === false) { $match = false; break; }
            }
            if (!$match) continue;

            $url = '/' . basename($dir) . '/' . basename($file);
            $results[$url] = $title;
        }
    }
    natcasesort($results);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Recipes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-light-green.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/css/kirk_style.css">
    <link rel="icon" href="/images/kr.jpg" type="image/jpg">
    <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
</head>
<body>

<!-- Navigation bar -->
<div id="nav-placeholder"></div>
<script>
$(function () { $("#nav-placeholder").load("/css/nav_kirk.html"); });
</script>

<!-- Header -->
<div id="header-placeholder"></div>
<script>
$(function () { $("#header-placeholder").load("/css/header_kirk.html"); });
</script>

<!-- Results -->
<div class="w3-container w3-card-4 w3-margin">
    <h2 class="w3-center w3-theme-d1 w3-padding">Recipe Search</h2>

    <form action="/search.php" method="get" class="w3-center w3-padding" role="search">
        <input type="search" name="q" value="<?php echo htmlspecialchars($q, ENT_QUOTES); ?>"
               placeholder="Search recipes&hellip;" aria-label="Search recipes"
               class="w3-input w3-border" style="display:inline-block; width:60%; max-width:400px;">
        <button type="submit" class="w3-button w3-theme-d5"><i class="fa fa-search"></i> Search</button>
    </form>

    <div class="w3-padding">
    <?php if ($q === ''): ?>
        <p class="w3-center">Type something above to search the recipes.</p>
    <?php elseif (count($results) === 0): ?>
        <p class="w3-center">No recipes matched
           &ldquo;<?php echo htmlspecialchars($q, ENT_QUOTES); ?>&rdquo;.</p>
    <?php else: ?>
        <p class="w3-center"><?php echo count($results); ?>
           result<?php echo count($results) === 1 ? '' : 's'; ?> for
           &ldquo;<?php echo htmlspecialchars($q, ENT_QUOTES); ?>&rdquo;:</p>
        <ul class="w3-ul w3-card-2 w3-white">
        <?php foreach ($results as $url => $title): ?>
            <li><a href="<?php echo htmlspecialchars($url, ENT_QUOTES); ?>">
                <?php echo htmlspecialchars($title, ENT_QUOTES); ?></a></li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    </div>
</div>

<!-- Footer -->
<div id="footer-placeholder"></div>
<script>
$(function () { $("#footer-placeholder").load("/css/footer_kirk.html"); });
</script>

</body>
</html>
