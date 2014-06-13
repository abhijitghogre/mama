<input type="hidden" value="<?php echo $project_id; ?>" class="project_id"/>
<div class="formMessage">
    <?php echo $this->Session->flash(); ?>
</div>
<div class="content-box-header">
    <div class="panel-title">Enroll User</div>
</div>
<div class="content-box-large padding-10 box-with-header">
    <div class="panel-body">
        <form id="newUserForm" class="form-horizontal" role="form">
            <?php echo $this->element('mandatory_fields'); ?>
            <?php echo $custom_fields; ?>
            <div class="row">
                <div class="col-sm-8 text-center">
                    <input type="submit" class="enroll_user_btn btn btn-primary" value="Save"/>
                </div>
            </div>
        </form>
    </div>
</div>