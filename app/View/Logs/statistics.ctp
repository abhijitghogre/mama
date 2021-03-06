<div class="content-box-header">
    <div class="panel-title">
        Statistics
    </div>
</div>
<div class="content-box-large box-with-header">
    <div class="panel-body">
        <form class="form-vertical" role="form">
            <div class="row">
                <div class="col-md-2">
                    <label class="control-label">Project:</label>
                    <?php
                    foreach ($projects as $p) {
                        $project[] = array('value' => $p['Project']['id'], 'name' => $p['Project']['Name']);
                    }
                    echo $this->Form->input('project_id', array('type' => 'select', 'options' => $project, 'label' => false, 'class' => 'form-control', 'div' => FALSE));
                    ?>
                </div>
                <div class="col-md-2">
                    <label class="control-label">Filter:</label>
                    <?php
                    $filter = array(
                        "1" => "Number of women to be called as per registration",
                        "2" => "Number of women actually called, and which attempt",
                        "3" => "Number & Reasons for women not being called",
                        "4" => "Number of women listening the message(20%,50%,100%)",
                        "5" => "Number of missed calls received and call back"
                    );
                    echo $this->Form->input('filter', array('type' => 'select', 'options' => $filter, 'label' => false, 'class' => 'form-control', 'div' => FALSE));
                    ?>
                </div>
                <div class="col-md-2">
                    <label class="control-label">From:</label>
                    <input type="text" id="statsdatefrom" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="control-label">To:</label>
                    <input type="text" id="statsdateto" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="control-label">&nbsp;</label></br>
                    <div class="btn-group">
                        <button type="button" value="1" class="btn btn-default statsbtn" id="day">Day</button>
                        <button type="button" value="2" class="btn btn-default statsbtn" id="week">Week</button>
                        <button type="button" value="3" class="btn btn-default statsbtn" id="month">Month</button>
                        <button type="button" value="4" class="btn btn-default statsbtn" id="year">Year</button>
                    </div>
                    <input type="button" class="btn btn-primary report-stats float-right" value="Generate CSV"/>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-md-12">
                <div class="content-box-large" id="stats-box">
                    <div id="bar-chart" style="height: 230px;"></div>
                    <br /><br />
                </div>
            </div>
        </div>
    </div>
</div>