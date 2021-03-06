<div class="content-box-header">
    <div class="panel-title">List of users</div>
</div>
<div class="content-box-large padding-10 box-with-header">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-7">
                <table class="table table-bordered" id="List_users">
                    <thead>
                        <tr>
                            <th>User id</th>
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
                                <td><?php echo $user['User']['id']; ?></td>
                                <td><?php echo $user['User']['Name']; ?></td>
                                <td><?php echo $user['User']['Phone']; ?></td>
                                <td>
                                    <?php
                                    echo $this->Html->link(
                                            'View', array(
                                        'controller' => 'users',
                                        'action' => 'view', $user['User']['id'], $projectId
                                            ), array('class' => 'btn btn-success btn-xs')
                                    );
                                    ?>
                                    <?php
                                    echo $this->Html->link(
                                            'Edit', array(
                                        'controller' => 'users',
                                        'action' => 'edit', $user['User']['id'], $projectId
                                            ), array('class' => 'btn btn-primary btn-xs')
                                    );
                                    ?>
                                    <?php
                                    if ($role == 'superadmin') {
                                        echo $this->Html->link(
                                                'Delete', array(
                                            'controller' => 'users',
                                            'action' => 'delete', $user['User']['id']
                                                ), array('class' => 'btn btn-danger btn-xs')
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
