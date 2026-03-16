<?php
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$pages = [
    'index' => 'Home',
    'comidas' => 'Typical foods',
    'deportes' => 'Main sports',
    'fiestas' => 'Traditional festivals',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baskenland - <?php echo $pages[$currentPage] ?? 'Project'; ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="site-header">
        <div class="header-inner">
            <div class="brand">
                <h1>Baskenland</h1>
                <p>Students from the Netherlands and Spain</p>
            </div>
            <nav class="main-nav">
                <?php foreach ($pages as $file => $label): ?>
                    <?php $active = ($file === $currentPage) ? 'active' : ''; ?>
                    <a class="nav-link <?php echo $active; ?>" href="<?php echo $file; ?>.php"><?php echo $label; ?></a>
                <?php endforeach; ?>
            </nav>
        </div>
    </header>
