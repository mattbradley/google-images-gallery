<?php

    $files = scandir('pics');
    $files = array_slice($files, 2);
    shuffle($files);
    $files = array_slice($files, 0, 25);

    $pics = array();
    foreach ($files as $f) {
        $p = array();

        $p['src'] = $f;
        $p['id'] = preg_replace('/[^\d]/', '', $f);

        $info = getimagesize('pics/' . $p['src']);
        $p['w'] = $info[0];
        $p['h'] = $info[1];

        $pics[] = $p;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Photo Gallery</title>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
        }

        h1 {
            font: bold 2.5em "Helvetica Neue", Helvetica, Arial, sans-serif;
            text-align: center;
            border-bottom: 1px solid #666;
            padding: 16px 0;
            margin: 16px 0 32px;
        }

        #container {
            width: 1000px;
            margin: 0 auto;
        }

        .row {
            position: relative;
        }

        .row:after {
            content: '.';
            font-size: 0;
            height: 0;
            visibility: hidden;
            display: block;
            clear: both;
        }

        .row-info {
            position: absolute;
            left: 0;
            bottom: 0;
        }

        .pic {
            float: left;
            margin: 2px;
            border: 1px solid #fff;
            text-align: center;
            -webkit-transition: box-shadow 250ms;
            position: relative;
            z-index: 0;
        }

        .pic-container {
            width: 100%;
            height: 100%;
            background: #000;
            display: none;
        }

        .pic-container img {
            opacity: 0.85;
            -webkit-transition: opacity 250ms;
        }

        .pic-hover:hover {
            box-shadow: 0 0 32px 4px rgba(0, 0, 0, 0.9);
            z-index: 1;
            cursor: pointer;
        }

        .pic-hover:hover img {
            opacity: 1;
        }
    </style>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready( function() {
            var pics = $.parseJSON('<?= json_encode($pics) ?>');
            var rowWidth = 1000;
            var spacing = 6;

            var b = 0, e = 0, rowHeight;
            while (e < pics.length) {
                do {
                    e++;

                    var totalWidth = 0;
                    for (var i = b; i < e; i++)
                        totalWidth += pics[i].w / pics[i].h;

                    rowHeight = Math.round((rowWidth - spacing * (e - b)) / totalWidth);
                } while (rowHeight > 200 && e < pics.length);

                var actualRowWidth = 0;
                for (var i = b; i < e; i++) {
                    pics[i]._w = Math.round(pics[i].w / pics[i].h * rowHeight);
                    pics[i]._h = rowHeight;
                    actualRowWidth += pics[i]._w;
                }

                var diff = (rowWidth - spacing * (e - b)) - actualRowWidth;
                var per = Math.floor(diff / (e - b));
                var extra = diff % (e - b);
                if (extra < 0) extra += e - b;
                for (var i = b; i < e; i++) {
                    pics[i]._w += per;
                    if (i - b < extra) pics[i]._w++;
                }

                var rowDiv = $('<div></div>')
                    .addClass('row')
                    .appendTo('#container');

                for (var i = b; i < e; i++) {
                    var pic = $('<div></div>')
                        .addClass('pic')
                        .css( { width: pics[i]._w, height: pics[i]._h } )
                        .appendTo(rowDiv);

                    var picContainer = $('<div></div>')
                        .addClass('pic-container');

                    var img = $(buildimg(pics[i]))
                        .load( function() {
                            $(this).parents('.pic-container').fadeIn(800, function() {
                                $(this).parent().addClass('pic-hover');
                            } );
                        } );

                    picContainer
                        .html(img)
                        .wrapInner('<a href="pic/' + pics[i].id + '" />');

                    pic.append(picContainer).appendTo(rowDiv);
                }

                b = e;
            }
        } );

        function buildimg(pic) {
            return (
                '<img ' +
                'src="pic/' + pic.id + '/' + pic._w + 'x' + pic._h + '/' + pic.src + '" ' +
                'width="' + pic._w + '" ' +
                'height="' + pic._h + '" ' +
                '/>');
        }
    </script>
</head>
<body>
    <div id="container">
        <h1>Photo Gallery</h1>
    </div>
</body>
</html>
