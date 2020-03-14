<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="assets/core.css">
        <title>Популярные слова</title>
    </head>

    <body>
        <?php if($_POST) : ?>
            <?php
                $count = $_POST['count'];
                $text = $_POST['text'];
                $text = preg_replace('/[&%+@*().,?!\d]/', ' ', $text);
                $text = preg_replace('/\s\s/', ' ', $text);
                $wcounts = array(0 => 0);

                $word = '';
                $i = 0;
                while($i <= strlen($text)) {
                    if(preg_match('/[A-zА-яр-юР-Ю]/i', $text[$i])) {
                        $word .= $text[$i];
                    }
                    else {
                        if(strlen($word) >= 3) {
                            if(in_array($word, array_keys($wcounts))) {
                                $wcounts[$word]++;
                            }
                            else {
                                $unit = array($word => 1);
                                array_push($wcounts, $unit);
                            }
                        }

                        $word = '';
                    }

                    $i++;
                }

                $wcounts = array_filter($wcounts);
                arsort($wcounts);
                $wcounts = array_slice($wcounts, 0, $count);
            ?>

            <h1><?= count($wcounts) ?> популярных слов</h1>
            <ul>
                <?php foreach($wcounts as $key => $value) : ?>
                    <li><?= $key ?> - <?= $value ?>
                <?php endforeach; ?>
            </ul>
            <p><a href="index.php">Ещё раз</a>
        <?php else : ?>
            <h1>Популярные слова</h1>
            <form action="index.php" method="post">
                <p><textarea name="text" rows="8" cols="60" placeholder="Откуда"></textarea>
                <p><input type="text" name="count" value="10" placeholder="Сколько">
                <p><button type="submit">Найти</button>
            </form>
        <?php endif; ?>
    </body>
</html>
