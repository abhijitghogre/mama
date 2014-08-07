<div class="formMessage error-box">
    <?php echo $this->Session->flash(); ?>
</div>
<div class="content-box-header">
    <div class="panel-title formTitle">
        <?php
        echo __('Add a new channel');
        ?>
    </div>
</div>
<div class="content-box-large padding-10 box-with-header">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <?php
                echo $this->Form->create('Channel', array('class' => 'form-horizontal', 'role' => 'form'));
                ?>
                <div class="form-group formField">
                    <label for="channelname" class="col-sm-2 control-label">Channel name:</label>
                    <div class="col-sm-10">
                        <?php echo $this->Form->input('channel_name', array('label' => false, 'class' => 'form-control', 'placeholder' => 'Channel name', 'id' => 'channelname')); ?>
                    </div>
                </div>
                <div class="form-group formField">
                    <label class="col-md-2 control-label">Channel Type:</label>
                    <div class="col-md-10">
                        <div class="radio">
                            <label><input type="radio" value="1" name="data[Channel][channel_type]" checked/>NGO</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" value="2" name="data[Channel][channel_type]"/>Hospital</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" value="3" name="data[Channel][channel_type]"/>Other</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <?php
                        echo $this->Form->submit('Add', array('class' => 'btn btn-primary', 'title' => 'Add channel'));
                        echo $this->Form->end();
                        ?>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>