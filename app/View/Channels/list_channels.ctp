<div class="content-box-header">
    <div class="panel-title">List of channels</div>
</div>
<div class="content-box-large padding-10 box-with-header">
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-12 text-right">
                <?php echo $this->Html->link('<i class="glyphicon glyphicon-plus-sign"></i> Add new channels', array('controller' => 'channels', 'action' => 'add'), array('class' => 'btn btn-primary add-project-button', 'escape' => false)); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-9">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Women Registered</th>
                            <th>Channel Type</th>
                            <th>Project Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($channels as $channel) {
                            ?>
                            <tr>
                                <td><?php echo ucfirst($channel['Channel']['Name']); ?></td>
                                <td><?php echo $channel[0]['count']; ?></td>
                                <td><?php $type = array('1'=>'NGO','2'=>'Hospital','3'=>'Other'); echo $type[$channel['Channel']['Type']]; ?></td>
                                <td>
                                    <?php
                                    echo $this->Html->link(
                                            'Edit Channel', array(
                                        'controller' => 'channels',
                                        'action' => 'edit', $channel['Channel']['id']
                                            ), array('class' => 'btn btn-warning btn-xs')
                                    );

                                    echo $this->Html->link(
                                            'Delete Channel', array(
                                        'controller' => 'channels',
                                        'action' => 'delete', $channel['Channel']['id']
                                            ), array('class' => 'btn btn-danger btn-xs')
                                    );
                                    ?> 
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
