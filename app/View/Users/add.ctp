<input type="hidden" value="<?php echo $project_id; ?>" class="project_id"/>
<div class="content-box-large">
    <div class="panel-heading">
        <div class="panel-title">Enroll User</div>
    </div>
    <div class="panel-body">
        <form id="newUserForm" class="form-horizontal" role="form">
            <?php echo $this->element('mandatory_fields'); ?>
            <?php echo $custom_fields; ?>
            <div class="row">
                <div class="small-10 columns text-center small-centered"><input type="submit" class="enroll_user_btn button small" value="Save"></div>
            </div>
        </form>
    </div>
</div>
<div class = "row">

</div>
