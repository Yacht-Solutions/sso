<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/style.css">
    <title><?=APP?></title>
    <style>

        <?php

        switch(APP)
        {
            case 'Site 1':
                $bgColor = 'orange';
                break;

            case 'Site 2':
                $bgColor = 'blueviolet';
                break;
        }

        ?>

        :root {
            --bg-color: <?=$bgColor?>;
        }

    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark" aria-label="Eighth navbar example">
        <div class="container">
            <a class="navbar-brand" href="#"><?=APP?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample07" aria-controls="navbarsExample07" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarsExample07">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php

                    switch(APP)
                    {
                        case 'Site 1':
                            ?>
                            <li class="nav-item">
                                <a class="nav-link" href="http://localhost:8200/">Site 2</a>
                            </li>
                            <?php
                            break;

                        case 'Site 2':
                            ?>
                            <li class="nav-item">
                                <a class="nav-link" href="http://localhost:8100/">Site 1</a>
                            </li>
                            <?php
                            break;
                    }

                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="http://localhost:8300/?action=displayCookies" target="_blank">Auth cookies</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <section>
        <?php require_once $page . '.php'; ?>
        <div class="container mt-4 bg-dark text-secondary p-3">
            <h4>$_SESSION</h4>
            <pre><?php print_r($_SESSION); ?></pre>
        </div>
    </section>
</body>
</html>
