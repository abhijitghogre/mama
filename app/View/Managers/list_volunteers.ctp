<div class="content-box-large">
    <div class="panel-heading">
        <div class="panel-title"> List of volunteers</div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-striped">
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
                        foreach ($volunteers as $volunteer) {
                            ?>
                            <tr>
                                <td><?php echo $volunteer['Manager']['First Name'] . " " . $volunteer['Manager']['Last Name']; ?></td>
                                <td><?php echo $volunteer['Manager']['Username']; ?></td>
                                <td>
                                    <?php
                                    echo $this->Html->link(
                                            'Edit', array(
                                        'controller' => 'managers',
                                        'action' => 'edit', $volunteer['Manager']['id']
                                            )
                                    );
                                    ?>
                                    <?php
                                    if ($role == 'superadmin') {
                                        echo " | " . $this->Html->link(
                                                'Delete', array(
                                            'controller' => 'managers',
                                            'action' => 'delete', $volunteer['Manager']['id']
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