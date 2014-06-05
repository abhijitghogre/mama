<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en" >

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php echo $this->Html->charset(); ?>
        <?php
        echo $this->Html->css('bootstrap.min');
        echo $this->Html->css('theme');
        echo $this->Html->css('style');

        echo $this->fetch('css');
        ?>
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
        <title>
            <?php echo "MAMA"; ?>|
            <?php echo $title_for_layout; ?>
        </title>

    </head>
    <body>

        <div class="page-content">
            <?php echo $this->fetch('content'); ?>
        </div>

        <?php
        echo $this->Html->script('jquery');
        echo $this->Html->script('bootstrap.min');
        echo $this->Html->script('theme');
        echo $this->Html->script('script');

        echo $this->fetch('script');
        ?>              

    </body>
</html>