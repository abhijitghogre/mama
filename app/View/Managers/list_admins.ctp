<div class="content-box-header">
    <div class="panel-title"> List of admins</div>
</div>
<div class="content-box-large padding-10 box-with-header">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
//            var_dump($users);exit;
                        foreach ($admins as $admin) {
                            ?>
                            <tr>
                                <td><?php echo $admin['Manager']['First Name'] . " " . $admin['Manager']['Last Name']; ?></td>
                                <td><?php echo $admin['Manager']['Username']; ?></td>
                                <td>
                                    <?php
                                    echo $this->Html->link(
                                            'Promote', array(
                                        'controller' => 'managers',
                                        'action' => 'promoteAdmin', $admin['Manager']['id']
                                            ), array('class' => 'btn btn-warning btn-xs')
                                    );
                                    echo $this->Html->link(
                                            'Edit', array(
                                        'controller' => 'managers',
                                        'action' => 'edit', $admin['Manager']['id']
                                            ), array('class' => 'btn btn-primary btn-xs')
                                    );

                                    echo $this->Html->link(
                                            'Delete', array(
                                        'controller' => 'managers',
                                        'action' => 'delete', $admin['Manager']['id']
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
