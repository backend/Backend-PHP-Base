<html>
    <head>
    <title>Exception: <?php echo $message ?></title>
    </head>
    <body>
        <div class="container">
            <h1>Exception: <?php echo  $title ?></h1>
            <h3><?php echo $message ?></h3>
            <?php if (!empty($sdebug_message)): ?>
                <table>
                    <?php echo  $xdebug_message ?>
                </table>
            <?php else: ?>
                <pre class="loud"><?php echo $exception ?>


<?php foreach($exception->getTrace() as $key => $line): ?>
    <?php echo count($exception->getTrace()) - $key ?>. <?php if (!empty($line['file'])): echo $line['file'] ?> [ <?php echo $line['line'] ?> ] - <?php endif; ?><?php echo $line['class'] ?><?php echo $line['type'] ?><?php echo $line['function'] ?>()
<?php endforeach; ?>
<?php endif; ?>
</pre>
        </div>
    </body>
</html>
