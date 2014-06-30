<div class="content-box-header">
    <div class="panel-title">List of projects</div>
</div>
<div class="content-box-large padding-10 box-with-header">
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-12 text-right">
                <?php echo $this->Html->link('<i class="glyphicon glyphicon-plus-sign"></i> Add new projects', array('controller' => 'projects', 'action' => 'add'), array('class' => 'btn btn-primary add-project-button', 'escape' => false)); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-7">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Women Registered</th>
                            <th>User Actions</th>
                            <th>Form Fields Actions</th>
                            <th>Project Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($projects as $project) {
                            ?>
                            <tr>
                                <td><?php echo ucfirst($project['Project']['Name']); ?></td>
                                <td><?php echo $project[0]['count']; ?></td>
                                <td>
                                    <?php
                                    echo $this->Html->link(
                                            'Enroll User', array(
                                        'controller' => 'users',
                                        'action' => 'add', $project['Project']['id']
                                            ), array('class' => 'btn btn-primary btn-xs')
                                    );
                                    echo $this->Html->link(
                                            'List Users', array(
                                        'controller' => 'users',
                                        'action' => 'listUsers', $project['Project']['id']
                                            ), array('class' => 'btn btn-info btn-xs')
                                    );
                                    ?>
                                </td>     
                                <td>     
                                    <?php
                                    echo $this->Html->link(
                                            'Manage Fields', array(
                                        'controller' => 'projects',
                                        'action' => 'manageFields', $project['Project']['id']
                                            ), array('class' => 'btn btn-success btn-xs')
                                    );
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo $this->Html->link(
                                            'Edit Project', array(
                                        'controller' => 'projects',
                                        'action' => 'edit', $project['Project']['id']
                                            ), array('class' => 'btn btn-warning btn-xs')
                                    );

                                    echo $this->Html->link(
                                            'Delete Project', array(
                                        'controller' => 'projects',
                                        'action' => 'delete', $project['Project']['id']
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
