<input type="hidden" value="<?php echo $project_id; ?>" class="project_id"/>
<input type="hidden" value="<?php echo $user_id; ?>" class="user_id"/>
<input type="hidden" value="<?php echo $user_meta_id; ?>" class="user_meta_id"/>
<input type="hidden" value="<?php echo $user_callflag_id; ?>" class="user_callflag_id"/>
<div class="formMessage">
    <?php echo $this->Session->flash(); ?>
</div>
<div class="content-box-header">
    <div class="panel-title">Edit Enrolled User</div>
</div>
<div class="content-box-large padding-10 box-with-header">
    <div class="panel-body">
        <form id="newUserForm" class="form-horizontal" role="form" data-edit-flag="1">
            <?php echo $this->element('mandatory_fields'); ?>
            <?php echo $custom_fields; ?>
            <div class="row">
                <input type="submit" class="enroll_user_btn btn btn-primary col-md-offset-4" value="Save"/>
            </div>
        </form>
    </div>
</div>
<div class="hide dom-edit-flag">
    <div class="fields-data">
        <?php echo json_encode($user[0], TRUE); ?>
    </div>
</div>