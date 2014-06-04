<input type="hidden" value="<?php echo $project_id; ?>" class="project_id"/>
<input type="hidden" value="<?php echo $user_id; ?>" class="user_id"/>
<input type="hidden" value="<?php echo $user_meta_id; ?>" class="user_meta_id"/>
<div class = "row">
    <form id="newUserForm" data-edit-flag="1">
        <div class = "small-8 columns small-centered">
            <div class="formMessage">
                <?php echo $this->Session->flash(); ?>
            </div>
            <div class="formTitle text-center">
                Edit Enrolled User
            </div>
        </div>
        <?php echo $this->element('mandatory_fields'); ?>
        <?php echo $custom_fields; ?>
        <div class="row">
            <div class="small-10 columns text-center small-centered"><input type="submit" class="enroll_user_btn button small" value="Save"></div>
        </div>
    </form>
</div>
<div class="hide dom-edit-flag">
    <div class="fields-data">
        <?php echo json_encode($user[0], TRUE); ?>
    </div>
</div>
