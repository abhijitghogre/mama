<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en" >

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php echo $this->Html->charset(); ?>
        <?php
        echo $this->Html->css('bootstrap.min');
        echo $this->Html->css('morris');
        echo $this->Html->css('dataTables.bootstrap');
        echo $this->Html->css('theme');
        echo $this->Html->css('style');
		echo $this->Html->css('validationEngine.jquery');
		echo $this->Html->css('chosen');

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
        <div class="header">
            <div class="container">
                <div class="row">
                    <div class="col-md-8">
                        <!-- Logo -->
                        <div class="logo">
                            <h1>
                                <?php
                                echo $this->Html->link($this->Html->image('logo.png', array('alt' => 'Mama Logo', 'class' => 'logo')), array('controller' => 'home', 'action' => 'index'), array('escape' => false));
                                ?>
                            </h1>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="navbar navbar-inverse" role="banner">
                            <nav class="collapse navbar-collapse bs-navbar-collapse navbar-right" role="navigation">
                                <ul class="nav navbar-nav">
                                    <li class="dropdown">
                                        <?php
                                        if ($this->Session->read('Auth.User')) {
                                            $manager = $this->Session->read('Auth.User.username');

                                            echo $this->Html->link('Welcome ' . $manager . ' <b class="caret"></b>', array('controller' => 'managers', 'action' => 'view'), array('escape' => FALSE, 'class' => 'dropdown-toggle', 'data-toggle' => 'dropdown'));
                                        } else {
                                            echo $this->Html->link('Login', array('controller' => 'managers', 'action' => 'login'));
                                        }
                                        ?>
                                        <ul class="dropdown-menu animated fadeInUp">
                                            <li>
                                                <?php
                                                if ($this->Session->read('Auth.User')) {
                                                    echo $this->Html->link('Profile', array('controller' => 'managers', 'action' => 'view'));
                                                }
                                                ?>
                                            </li>
                                            <li>
                                                <?php
                                                if ($this->Session->read('Auth.User')) {
                                                    echo $this->Html->link('Logout', array('controller' => 'managers', 'action' => 'logout'));
                                                }
                                                ?>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-content">
            <div class="row">
                <div class="col-md-2">
                    <div class="sidebar content-box" style="display: block;">
                        <ul class="nav">
                            <!-- Main menu -->
                            <li>
                                <?php
                                echo $this->Html->link('<i class="glyphicon glyphicon-home"></i> Manage Projects', array('controller' => 'projects', 'action' => 'listProjects'), array('escape' => false));
                                ?>
                            </li>
                            <li class="submenu">
                                <a href="#">
                                    <i class="glyphicon glyphicon-user"></i> Volunteers
                                    <span class="caret pull-right"></span>
                                </a>
                                <!-- Sub menu -->
                                <ul>
                                    <li class="">
                                        <?php
                                        if ($role == 'admin') {
                                            echo $this->Html->link('Add volunteers', array('controller' => 'managers', 'action' => 'add'));
                                        } elseif ($role == 'superadmin') {
                                            echo $this->Html->link('Add volunteers/admins', array('controller' => 'managers', 'action' => 'add'));
                                        }
                                        ?>

                                    </li>
                                    <li class="">
                                        <?php
                                        if ($role == 'superadmin' || $role == 'admin') {
                                            echo $this->Html->link('View volunteers', array('controller' => 'managers', 'action' => 'listVolunteers'));
                                        }
                                        ?>
                                    </li>
                                    <li class="">
                                        <?php
                                        if ($role == 'superadmin') {
                                            echo $this->Html->link('View admins', array('controller' => 'managers', 'action' => 'listAdmins'));
                                        }
                                        ?>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <?php
                                //if ($role == 'superadmin' || $role == 'admin') {
                                echo $this->Html->link('<i class="glyphicon glyphicon-th-list"></i> Logs', array('controller' => 'logs', 'action' => 'index'), array('escape' => false));
                                //}
                                ?>
                            </li>
                            <li>
                                <?php
                                //if ($role == 'superadmin' || $role == 'admin') {
                                echo $this->Html->link('<i class="glyphicon glyphicon-stats"></i> Statistics', array('controller' => 'logs', 'action' => 'statistics'), array('escape' => false));
                                //}
                                ?>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-10">
                    <?php echo $this->fetch('content'); ?>
                </div>
            </div>
        </div>



        <?php
        echo $this->Html->script('jquery');
        echo $this->Html->script('bootstrap.min');
        echo $this->Html->script('morris');
        echo $this->Html->script('jquery.dataTables.min');
        echo $this->Html->script('dataTables.bootstrap');
        echo $this->Html->script('theme');
        echo $this->Html->script('stats');
        echo $this->Html->script('jquery.validationEngine');
        echo $this->Html->script('chosen.jquery.min');
		echo $this->Html->script('script');

        echo $this->fetch('script');
        ?>

    </body>
</html>