<div class="users form">
    <div class="formMessage">
        <?php echo $this->Session->flash(); ?>
    </div>
    <div class="content-box-header">
        <div class="panel-title formTitle">
            <?php
            if ($role == 'admin') {
                echo __('Add a new volunteer');
            } else {
                echo __('Add a new manager');
            }
            ?>
        </div>
    </div>
    <div class="content-box-large padding-10 box-with-header">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <?php
                    echo $this->Form->create('Manager', array('class' => 'form-horizontal', 'role' => 'form'));
                    ?>
                    <div class="form-group formField">
                        <label for="inputusername" class="col-sm-2 control-label">Username:</label>
                        <div class="col-sm-10">
                            <?php echo $this->Form->input('username', array('label' => false, 'class' => 'form-control', 'placeholder' => 'Username', 'id' => 'inputusername')); ?>
                        </div>
                    </div>
                    <div class="form-group formField">
                        <label for="inputpass" class="col-sm-2 control-label">Password:</label>
                        <div class="col-sm-10">
                            <?php echo $this->Form->input('password', array('label' => false, 'class' => 'form-control', 'placeholder' => 'Password', 'id' => 'inputpass')); ?>
                        </div>
                    </div>
                    <div class="form-group formField">
                        <label for="inputconpass" class="col-sm-2 control-label">Confirm Password:</label>
                        <div class="col-sm-10">
                            <?php echo $this->Form->input('password', array('label' => false, 'class' => 'form-control', 'placeholder' => 'Confirm Password', 'id' => 'inputconpass')); ?>
                        </div>
                    </div>
                    <?php
                    if ($role == 'admin') {
                        echo $this->Form->input('role', array('type' => 'hidden', 'value' => 'volunteer'));
                    } elseif ($role == 'superadmin') {
                        ?>
                        <div class="form-group formField">
                            <label for="inputrole" class="col-sm-2 control-label">Role:</label>
                            <div class="col-sm-10">
                                <?php echo $this->Form->input('role', array('options' => array('admin' => 'Admin', 'volunteer' => 'Volunteer'), 'default' => 'admin', 'label' => false, 'class' => 'form-control', 'placeholder' => 'Confirm Password', 'id' => 'inputrole')); ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="form-group formField">
                        <label for="inputfname" class="col-sm-2 control-label">First Name:</label>
                        <div class="col-sm-10">
                            <?php echo $this->Form->input('fname', array('label' => false, 'class' => 'form-control', 'placeholder' => 'First Name', 'id' => 'inputfname')); ?>
                        </div>
                    </div>
                    <div class="form-group formField">
                        <label for="inputlname" class="col-sm-2 control-label">Last Name:</label>
                        <div class="col-sm-10">
                            <?php echo $this->Form->input('lname', array('label' => false, 'class' => 'form-control', 'placeholder' => 'Last Name', 'id' => 'inputlname')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <?php
                            echo $this->Form->submit('Register', array('class' => 'btn btn-primary', 'title' => 'Register'));
                            echo $this->Form->end();
                            ?>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>