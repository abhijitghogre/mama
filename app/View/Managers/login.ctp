<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="login-wrapper">
            <div class="box">
                <div class="content-wrap">
                    <h6>Sign In</h6>
                    <?php echo $this->Form->create('Manager'); ?>
                    <?php echo $this->Form->input('username', array('label' => false, 'class' => 'form-control', 'placeholder' => 'Username')); ?>
                    <?php echo $this->Form->input('password', array('label' => false, 'class' => 'form-control', 'placeholder' => 'Password')); ?>
                    <div class="action">
                        <?php
                        echo $this->Form->submit('Login', array('class' => 'btn btn-primary signup', 'title' => 'Login'));
                        echo $this->Form->end();
                        ?>
                    </div>                
                </div>
            </div>
        </div>
    </div>
</div>