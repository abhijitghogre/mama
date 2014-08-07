<div class="row">
    <div class="col-sm-8">
        <div class="form-group formField">
            <label for="mand-channel" class="col-sm-2 control-label">Channel*:</label>
            <div class="col-sm-10">
                <?php
                    $channeloption[0] = array('value'=>0,'name'=>'');
                    foreach($channels as $c) {
                            $channeloption[] = array('value'=>$c['Channel']['id'],'name'=>$c['Channel']['Name']);
                    }
                    echo $this->Form->input('channel_id', array('type' => 'select','name'=>'mand-channel', 'options' => $channeloption, 'label' => false, 'div' => FALSE, 'class'=>'form-control'));
                ?>
            </div>
        </div>
        <div class="form-group formField">
            <label for="mand-name" class="col-sm-2 control-label">Name*:</label>
            <div class="col-sm-10">
                <input name="mand-name" id="mand-name" class="form-control" type="text" required>
            </div>
        </div>
        <div class="form-group formField">
            <label for="mand-age" class="col-sm-2 control-label">Age*:</label>
            <div class="col-sm-10">
                <input name="mand-age" id="mand-age" class="form-control" type="number" required>
            </div>
        </div>
        <div class="form-group formField">
            <label class="col-md-2 control-label">Education*:</label>
            <div class="col-md-10">
                <div class="radio">
                    <label><input type="radio" value="1" name="mand-education" checked/>0 to 5th Standard</label>
                </div>
                <div class="radio">
                    <label><input type="radio" value="2" name="mand-education"/>5th to 10th Standard</label>
                </div>
                <div class="radio">
                    <label><input type="radio" value="3" name="mand-education"/>10th Passed</label>
                </div>
                <div class="radio">
                    <label><input type="radio" value="4" name="mand-education"/>12th Passed</label>
                </div>
                <div class="radio">
                    <label><input type="radio" value="5" name="mand-education"/>Graduate</label>
                </div>
                <div class="radio">
                    <label><input type="radio" value="6" name="mand-education"/>Post Graduate</label>
                </div>
            </div>
        </div>
        <div class="form-group formField">
            <label for="mand-phone" class="col-sm-2 control-label">Phone*:</label>
            <div class="col-sm-10">
                <input name="mand-phone" id="mand-phone" class="form-control" type="number" required>
            </div>
        </div>
        <div class="form-group formField">
            <label class="col-md-2 control-label">Phone type*:</label>
            <div class="col-md-10">
                <div class="radio">
                    <label><input type="radio" value="1" name="mand-phone-type" checked/>Mobile (Mumbai)</label>
                </div>
                <div class="radio">
                    <label><input type="radio" value="2" name="mand-phone-type"/>Mobile (Others)</label>
                </div>
                <div class="radio">
                    <label><input type="radio" value="3" name="mand-phone-type"/>Landline (Mumbai)</label>
                </div>
                <div class="radio">
                    <label><input type="radio" value="4" name="mand-phone-type"/>Landline (Others)</label>
                </div>
            </div>
        </div>
        <div class="form-group formField">
            <label for="mand-phone-code" class="col-sm-2 control-label">Phone code*:</label>
            <div class="col-sm-10">
                <input name="mand-phone-code" id="mand-phone-code" class="form-control" type="text" required>
            </div>
        </div>
        <div class="form-group formField">
            <label class="col-md-2 control-label">Phone Owner*:</label>
            <div class="col-md-10">
                <div class="radio">
                    <label><input type="radio" value="woman" name="mand-phone-owner" checked/>Woman</label>
                </div>
                <div class="radio">
                    <label><input type="radio" value="husband" name="mand-phone-owner"/>Husband</label>
                </div>
                <div class="radio">
                    <label><input type="radio" value="family" name="mand-phone-owner"/>Family Phone</label>
                </div>
                <div class="radio">
                    <label><input type="radio" value="other" name="mand-phone-owner"/>Other</label>
                </div>
                <input name="mand-phone-owner-other" type="text" required class="form-control">
            </div>
        </div>
        <div class="form-group formField">
            <label for="mand-phone-alt" class="col-sm-2 control-label">Alternate Phone*:</label>
            <div class="col-sm-10">
                <input name="mand-phone-alt" id="mand-phone-alt" class="form-control" type="number" required>
            </div>
        </div>
        <div class="form-group formField">
            <label class="col-md-2 control-label">Alternate No. Phone Owner*:</label>
            <div class="col-md-10">
                <div class="radio">
                    <label><input type="radio" value="woman" name="mand-phone-owner-alt" checked/>Woman</label>
                </div>
                <div class="radio">
                    <label><input type="radio" value="husband" name="mand-phone-owner-alt"/>Husband</label>
                </div>
                <div class="radio">
                    <label><input type="radio" value="family" name="mand-phone-owner-alt"/>Family Phone</label>
                </div>
                <div class="radio">
                    <label><input type="radio" value="other" name="mand-phone-owner-alt"/>Other</label>
                </div>
                <input name="mand-phone-owner-alt-other" type="text" required class="form-control">
            </div>
        </div>
        <div class="form-group formField">
            <label for="mand-lmp" class="col-sm-2 control-label">LMP*:</label>
            <div class="col-sm-10">
                <input name="mand-lmp" id="mand-lmp" class="form-control" type="date" required>
            </div>
        </div>
        <div class="form-group formField">
            <label for="mand-gest-age" class="col-sm-2 control-label">Gestational age at the time of enrollment:</label>
            <div class="col-sm-10">
                <input name="mand-gest-age" id="mand-gest-age" class="form-control" type="number">
            </div>
        </div>
        <div class="form-group formField">
            <label for="mand-reg-date" class="col-sm-2 control-label">Registration date:</label>
            <div class="col-sm-10">
                <input name="mand-reg-date" id="mand-reg-date" class="form-control" type="date">
            </div>
        </div>
        <div class="form-group formField">
            <label class="col-md-2 control-label" for="mand-call-slot">Preferred Call Slot*:</label>
            <div class="col-md-10">
                <select class="form-control" id="mand-call-slot" name="mand-call-slot">
                    <option value="1">6:00 AM - 7:00 AM</option>
                    <option value="2">7:00 AM - 8:00 AM</option>
                    <option value="3">8:00 AM - 9:00 AM</option>
                    <option value="4">9:00 AM - 10:00 AM</option>
                    <option value="5">10:00 AM - 11:00 AM</option>
                    <option value="6">11:00 AM - 12:00 PM</option>
                    <option value="7">12:00 PM - 1:00 PM</option>
                    <option value="8">1:00 PM - 2:00 PM</option>
                    <option value="9">2:00 PM - 3:00 PM</option>
                    <option value="10">3:00 PM - 4:00 PM</option>
                    <option value="11">4:00 PM - 5:00 PM</option>
                    <option value="12">5:00 PM - 6:00 PM</option>
                    <option value="13">6:00 PM - 7:00 PM</option>
                    <option value="14">7:00 PM - 8:00 PM</option>
                    <option value="15">8:00 PM - 9:00 PM</option>
                    <option value="16">9:00 PM - 10:00 PM</option>
                    <option value="17">10:00 PM - 11:00 PM</option>
                    <option value="18">11:00 PM - 12:00 AM</option>
                </select> 
            </div>
        </div>
        <div class="form-group formField">
            <label class="col-md-2 control-label">Delivery status*:</label>
            <div class="col-md-10">
                <div class="radio">
                    <label><input type="radio" value="0" name="mand-delivery-status" checked/>Pregnant</label>
                </div>
                <div class="radio">
                    <label><input type="radio" value="1" name="mand-delivery-status"/>Successful delivery</label>
                </div>
                <div class="radio">
                    <label><input type="radio" value="2" name="mand-delivery-status"/>Unsuccessful delivery</label>
                </div>
            </div>
        </div>
        <div class="form-group formField">
            <label for="mand-delivery-date" class="col-sm-2 control-label">Delivery date:</label>
            <div class="col-sm-10">
                <input name="mand-delivery-date" id="mand-delivery-date" class="form-control" type="date" required/>
            </div>
        </div>
        <div class="form-group formField">
            <label class="col-md-2 control-label">Language*:</label>
            <div class="col-md-10">
                <div class="radio">
                    <label><input type="radio" value="2" name="mand-language" checked/>Hindi</label>
                </div>
                <div class="radio">
                    <label><input type="radio" value="3" name="mand-language"/>Marathi</label>
                </div>
            </div>
        </div>
        <div class="form-group formField">
            <label class="col-md-2 control-label">Birth Place*:</label>
            <div class="col-md-10">
                <div class="radio">
                    <label><input type="radio" value="same-village" name="mand-birth-place" checked/>Same Village</label>
                </div>
                <div class="radio">
                    <label><input type="radio" value="other" name="mand-birth-place"/>Other</label>
                </div>
                <input name="mand-birth-place-other" type="text" required class="form-control"/>
            </div>
        </div>
    </div>
</div>