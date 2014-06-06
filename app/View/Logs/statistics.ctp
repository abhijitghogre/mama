<div class="content-box-large">
    <div class="panel-heading">
        <form class="form-vertical" role="form">
            <div class="row">
                <div class="col-md-2">
                    <label class="control-label">Project:</label>
                    <?php
                        foreach($projects as $p){
                            $project[] = array('value'=>$p['Project']['id'],'name'=>$p['Project']['Name']);
                        }
                        echo $this->Form->input('project_id', array('type' => 'select', 'options' => $project, 'label' => false, 'class' => 'form-control', 'div' => FALSE));
                    ?>
                </div>
                <div class="col-md-2">
                    <label class="control-label">Filter:</label>
                     <?php 
                        $filter = array(
                            "1"=>"Number of women to be called as per registration",
                            "2"=>"Number of women actually called, and which attempt",
                            "3"=>"Number & Reasons for women not being called",
                            "4"=>"Number of women listening to only 20% of the message",
                            "5"=>"Number of women listening to only 50% of the message",
                            "6"=>"Number of women listening to 100% of the message",
                            "7"=>"Number of missed calls received and call back"
                            );
                        echo $this->Form->input('filter', array('type' => 'select', 'options' => $filter, 'label' => false, 'class' => 'form-control', 'div' => FALSE));
                    ?>
                </div>
                <div class="col-md-2">
                    <label class="control-label">Period:</label>
                    <input type="date" id="statsdate" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="control-label">&nbsp;</label></br>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default" id="day">Day</button>
                        <button type="button" class="btn btn-default" id="week">Week</button>
                        <button type="button" class="btn btn-default" id="month">Month</button>
                        <button type="button" class="btn btn-default" id="year">Year</button>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="control-label">&nbsp;</label></br>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default" id="left"><i class="glyphicon glyphicon-circle-arrow-left"></i></button>
                        <button type="button" class="btn btn-default" id="right"><i class="glyphicon glyphicon-circle-arrow-right"></i></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <div class="content-box-header">
                    <div class="panel-title" id="stats-title">Daily Statistics</div>
                </div>
                <div class="content-box-large box-with-header">
                    <div id="daily-stats" style="height: 230px;"></div>
                    <br /><br />
                </div>
            </div>
        </div>
    </div>
</div>