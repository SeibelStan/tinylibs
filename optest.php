<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$matches = [];

$count = @$_GET['count'] ?: 1050;

$shifts = (object)[
    1000 => 1,
    2000 => 0.9,
    5000 => 0.85,
    10000 => 0.8,
];

$price = 0;

if($shifts) {
    $from = min(0, 0);
    $to = max(0, $count);

    $shiftsKeys = array_keys((array) $shifts);
    $lastShift = $shiftsKeys[count($shiftsKeys) - 1];

    for($i = $from; $i < $to; $i++) {
        $vShifts = [];
        foreach($shifts as $unit) {
            array_push($vShifts, $unit);
        }

        $iShift = 0;

        foreach($shiftsKeys as $j => $key) {
            if($i >= $shiftsKeys[$iShift] && $i < $lastShift) {
                $iShift++;
            }
        }
        if($i < $lastShift) {
            $price += $vShifts[$iShift];
        }
        else {
            $price += ((array)$shifts)[$lastShift];
        }
    }

    $price /= $count;
}

$priceAdd = 0;

$optsData = [
    'о1 +10%',
    'о2 +10$',
    'о3 +45$',
    'о4 +140$',
];

$opts = [];
foreach($optsData as $opt) {
    preg_match('/(.+?) ([+-])(.+?)([$%])$/', $opt, $matches);
    array_shift($matches);
    $opts[] = $matches;
}

$checkOpts = [];

foreach($_GET as $k => $v) {
    if(preg_match('/opt_/', $k)) {
        $optKey = (int)preg_replace('/opt_/', '', $k);
        $checkOpts[] = $optKey;
        $opt = $opts[$optKey];

        if($opt[3] == '%') {
            $optVal = $opt[2] / 100;
            if($opt[1] == '-') {
                $priceAdd -= $price * $count * $optVal;
            }
            else {
                $priceAdd += $price * $count * $optVal;
            }
        }
        else {
            $optVal = $opt[2];
            if($opt[1] == '-') {
                $priceAdd = $priceAdd - $optVal;
            }
            else {
                $priceAdd = $priceAdd + $optVal;
            }
        }
    }
}

$sum = $price * $count + $priceAdd;

?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <script src="vendor/jquery-3.3.1.min.js"></script>
</head>
<body>
    <form action="">
        <p>
        <?php foreach($opts as $k => $opt) : ?>
            <?php $optName = 'opt_' . $k ?>
            <input type="checkbox"
                data-label="<?= $opt[0] ?>"
                data-sig="<?= $opt[1] ?>"
                data-value="<?= $opt[2] ?>"
                data-mod="<?= $opt[3] ?>"
                name="<?= $optName ?>"
                id="<?= $optName ?>"
                <?= in_array($k, $checkOpts) ? 'checked' : '' ?>>
            <label for="<?= $optName ?>"><?= $opt[0] ?> <?= $opt[1] ?><?= $opt[2] ?><?= $opt[3] ?></label>
            <br>
        <?php endforeach; ?>
        <p><input type="number" name="count" value="<?= $count ?>">
        <p><button>Расчёт</button>
        <p>Цена: <span class="price"><?= $price ?></span>/шт
        <p>Сумма: <span class="sum"><?= $sum ?></span>$
    </form>
</body>
</html>

<script>
    function priceRecalc() {
        var count = $('[name="count"]').val();
        
        var price = 0;

        if(shifts) {
            var shiftsKeys = Object.keys(shifts);
            var lastShift = shiftsKeys[shiftsKeys.length - 1];

            var from = Math.min(0, 0);
            var to = Math.max(0, count);

            for(var i = from; i < to; i++) {
                var iShift = 0;
                for(var j in shiftsKeys) {
                    if(i >= shiftsKeys[iShift] && i < lastShift) {
                        iShift++;
                    }
                }
                if(i < lastShift) {
                    price += shifts[shiftsKeys[iShift]];
                }
                else {
                    price += shifts[lastShift];
                }
            }
        }

        price /= count;

        var priceAdd = 0;

        $('[name^="opt_"]').each(function () {
            if($(this).prop('checked')) {
                var opt = [
                    $(this).data('label'),
                    $(this).data('sig'),
                    $(this).data('value'),
                    $(this).data('mod')
                ];

                if(opt[3] == '%') {
                    optVal = opt[2] / 100;
                    if(opt[1] == '-') {
                        priceAdd -= price * count * optVal;
                    }
                    else {
                        priceAdd += price * count * optVal;
                    }
                }
                else {
                    optVal = opt[2];
                    if(opt[1] == '-') {
                        priceAdd = priceAdd - optVal;
                    }
                    else {
                        priceAdd = priceAdd + optVal;
                    }
                }
            }
        });

        var sum = price * count + priceAdd;

        $('.price').html(price.toFixed(4).replace(/\.*0+$/, ''));
        $('.sum').html(sum.toFixed(2).replace(/\.*0+$/, ''));
    }

    var shifts = <?= json_encode($shifts) ?>;
    var opts = <?= json_encode($opts) ?>;

    $(function () {
        $('input').change(function () {
            priceRecalc();
        });

        priceRecalc();
    });
</script>