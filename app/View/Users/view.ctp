<div class="content-box-header">
    <div class="panel-title">User Details</div>
</div>
<div class="content-box-large padding-10 box-with-header">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-striped">
                    <tbody>
                        <tr><td>Name</td><td><?php echo $user[0]['User']['name']; ?></td></tr>
                        <tr><td>Phone no.</td><td><?php echo $user[0]['User']['phone_no']; ?></td></tr>
                        <tr><td>LMP</td><td><?php echo $user[0]['User']['lmp']; ?></td></tr>
                        <tr><td>Gestational age</td><td><?php echo $user[0]['User']['enroll_gest_age']; ?></td></tr>
                        <tr><td>Preferred call slot</td><td><?php echo $user[0]['User']['call_slots']; ?></td></tr>
                        <tr><td>Delivery Status</td><td><?php echo $user[0]['User']['delivery']; ?></td></tr>
                        <tr><td>Language</td><td><?php echo $user[0]['User']['language']; ?></td></tr>
                        <tr><td>Registration Date</td><td><?php echo $user[0]['User']['registration_date']; ?></td></tr>
                        <tr><td>Delivery Date</td><td><?php echo $user[0]['User']['delivery_date']; ?></td></tr>
                        <tr><td>Entry Date</td><td><?php echo $user[0]['User']['entry_date']; ?></td></tr>
                        <tr><td>Phone Type</td><td><?php echo $user[0]['User']['phone_type']; ?></td></tr>
                        <tr><td>Phone Code</td><td><?php echo $user[0]['User']['phone_code']; ?></td></tr>
                        <tr><td>Age</td><td><?php echo $user[0]['UserMeta']['age']; ?></td></tr>
                        <tr><td>Education</td><td><?php echo $user[0]['UserMeta']['education']; ?></td></tr>
                        <tr><td>Phone Owner</td><td><?php echo $user[0]['UserMeta']['phone_owner']; ?></td></tr>
                        <tr><td>Alternate Phone</td><td><?php echo $user[0]['UserMeta']['alternate_no']; ?></td></tr>
                        <tr><td>Alternate Phone Owner</td><td><?php echo $user[0]['UserMeta']['alternate_no_owner']; ?></td></tr>
                        <tr><td>Birth Place</td><td><?php echo $user[0]['UserMeta']['birth_place']; ?></td></tr>

                        <!--CUSTOM FIELDS BELOW-->
                        <?php
                        $custom_fields_data = json_decode($user[0]['UserMeta']['custom_fields'], true);
                        foreach ($custom_fields as $key => $value) {
                            ?>
                            <tr>
                                <td>
                                    <?php echo $value['label']; ?>
                                </td>
                                <td>
                                    <?php
                                    if ($value['type'] === 'checkbox') {
                                        $cvals = '';
                                        foreach ($custom_fields_data[$key] as $val) {
                                            $cvals = $cvals . $val . ',';
                                        }
                                        $cvals = rtrim($cvals, ",");
                                        echo $cvals;
                                    } else {
                                        echo $custom_fields_data[$key];
                                    }
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
                        <?php
                        echo $this->Html->link(
                                'Edit Details', array('controller' => 'users', 'action' => 'edit', $user_id, $projectId), array('class' => 'btn btn-primary')
                        );
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>