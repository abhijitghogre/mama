<div class="content-box-header">
    <div class="panel-title">Update delivery status</div>
</div>
<div class="content-box-large padding-10 box-with-header">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered" id="need_update">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
//            var_dump($users);exit;
                        foreach ($users as $user) {
                        ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo $user['name']; ?></td>
                            <td>
                                <?php
                                echo $this->Html->link(
                                        'Edit', 
                                        array(
                                                'controller' => 'users',
                                                'action' => 'edit', $user['id'], $user['project_id']
                                        ), 
                                        array('class' => 'btn btn-primary btn-xs')
                                );
                                ?>
                                <?php
                                echo $this->Html->link(
                                        'View', 
                                        array(
                                                'controller' => 'users',
                                                'action' => 'view', $user['id'], $user['project_id']
                                        ), 
                                        array('class' => 'btn btn-success btn-xs')
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