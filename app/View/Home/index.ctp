<div class="content-box-large">
    <div class="panel-heading">
        <div class="panel-title">
            <div class="row">
                <form class="form-horizontal col-sm-4" role="form">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Project:</label>
                        <div class="col-sm-10">
                            <select class="form-control">
                                <?php
                                foreach ($projects as $project) {
                                    ?>
                                    <option value="<?php echo $project['Project']['id']; ?>"><?php echo $project['Project']['Name']; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Period:</label>
                        <div class="col-sm-10">
                            <div class="bfh-datepicker stats-date-select" data-format="y-m-d" data-date="today"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="content-box-header">
                            <div class="panel-title">Daily Stats</div>
                        </div>
                        <div class="content-box-large box-with-header">
                            <div id="daily-stats" style="height: 230px;"></div>
                            <br /><br />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="content-box-header">
                            <div class="panel-title">Weekly Stats</div>
                        </div>
                        <div class="content-box-large box-with-header">
                            <div id="weekly-stats" style="height: 230px;"></div>
                            <br /><br />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="content-box-header">
                            <div class="panel-title">Monthly Stats</div>
                        </div>
                        <div class="content-box-large box-with-header">
                            <div id="monthly-stats" style="height: 230px;"></div>
                            <br /><br />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="content-box-header">
                            <div class="panel-title">Yearly Stats</div>
                        </div>
                        <div class="content-box-large box-with-header">
                            <div id="yearly-stats" style="height: 230px;"></div>
                            <br /><br />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>