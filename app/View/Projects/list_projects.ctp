<div class="content-box-large">
    <div class="panel-heading">
        <div class="panel-title">List of projects</div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($projects as $project) {
                            ?>
                            <tr>
                                <td><?php echo $project['Project']['Name']; ?></td>
                                <td>
                                    <?php
                                    echo $this->Html->link(
                                            'Enroll User', array(
                                        'controller' => 'users',
                                        'action' => 'add', $project['Project']['id']
                                            )
                                    );
                                    echo " | " . $this->Html->link(
                                            'List Users', array(
                                        'controller' => 'users',
                                        'action' => 'listUsers', $project['Project']['id']
                                            )
                                    );
                                    echo " | " . $this->Html->link(
                                            'Manage Fields', array(
                                        'controller' => 'projects',
                                        'action' => 'manageFields', $project['Project']['id']
                                            )
                                    );
                                    echo " | " . $this->Html->link(
                                            'Edit Project', array(
                                        'controller' => 'projects',
                                        'action' => 'edit', $project['Project']['id']
                                            )
                                    );

                                    echo " | " . $this->Html->link(
                                            'Delete Project', array(
                                        'controller' => 'projects',
                                        'action' => 'delete', $project['Project']['id']
                                            )
                                    );
                                    ?> 
                                </td>
                            </tr>
                                    <?php
                                }
                                ?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-sm-12 text-center">
<?php echo $this->Html->link('Add new projects', array('controller' => 'projects', 'action' => 'add'), array('class' => 'btn btn-primary')); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
