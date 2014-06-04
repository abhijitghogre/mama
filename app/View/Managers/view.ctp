<div class="content-box-large">
    <div class="panel-heading">
        <div class="panel-title">Manager Details</div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-striped">
                    <tbody>
                        <?php
                        foreach ($manager[0]['Manager'] as $key => $value) {
                            ?>
                            <tr>
                                <td><?php echo $key; ?></td>
                                <td><?php echo $value; ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <?php
                        echo $this->Html->link(
                                'Edit Details', array('controller' => 'managers', 'action' => 'edit', $this->Session->read('Auth.User.id')), array('class' => 'btn btn-primary')
                        );
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>