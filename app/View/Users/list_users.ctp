<div class="content-box-large">
    <div class="panel-heading">
        <div class="panel-title">List of users</div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
//            var_dump($users);exit;
                        foreach ($users as $user) {
                            ?>
                            <tr>
                                <td><?php echo $user['User']['Name']; ?></td>
                                <td><?php echo $user['User']['Phone']; ?></td>
                                <td>
                                    <?php
                                    echo $this->Html->link(
                                            'View', array(
                                        'controller' => 'users',
                                        'action' => 'view', $user['User']['id'], $projectId
                                            )
                                    );
                                    ?>
                                    <?php
                                    echo " | " . $this->Html->link(
                                            'Edit', array(
                                        'controller' => 'users',
                                        'action' => 'edit', $user['User']['id'], $projectId
                                            )
                                    );
                                    ?>
                                    <?php
                                    if ($role == 'superadmin') {
                                        echo " | " . $this->Html->link(
                                                'Delete', array(
                                            'controller' => 'users',
                                            'action' => 'delete', $user['User']['id']
                                                )
                                        );
                                    }
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
