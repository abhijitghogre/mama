<div class = "row">
    <div class = "small-8 columns small-centered">
        <div class="formMessage">
            <?php echo $this->Session->flash(); ?>
        </div>
        <div class="formTitle text-center">
            List of users
        </div>
        <table class = "userDetails">
            <tr>
                <th>Name</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
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
        </table>
    </div>
</div>
