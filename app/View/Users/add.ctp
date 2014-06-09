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
                <input type="submit" class="enroll_user_btn btn btn-primary col-md-offset-4" value="Save"/>
            </div>
        </form>
    </div>
</div>