<div class="row">
	<div class="col-md-12 panel-warning">
		<div class="content-box-header panel-heading">
			<div class="panel-title "><?php echo ucfirst($project_name)." - "; ?>Create Stages</div>
			
			<div class="panel-options addstagedropdown">
				<div class="btn-group">
					<button type="button" class="btn btn-primary dropdown-toggle addstage" data-toggle="dropdown">
						Add Stage <span class="caret"></span>
					</button>
					<ul class="dropdown-menu" id="addstagedropdown" role="menu">
						<li data-stage-pos="start" class="addstageli"><a href="#">At the beginning</a></li>
						<li data-stage-pos="end" class="addstageli"><a href="#">At the end</a></li>
						<li class="divider"></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="content-box-large box-with-header">
		<div class="row toperrorboxcont">
			<div class="col-md-12">
				<div class="error-box hide">
					error
				</div>
			</div>
		</div>
		<!--<div class="row">
			<div class="col-md-12">
				<div  class="content-box-header">
					<div id="stagetimeline" style="height:20px;width:1120px;border:1px solid #000;">
						<?php 
							/*$daywidth = 1120/ 2100;
							$weekwidth = 1120/299;
							$monthwidth = 1120/69;
							
							$colors = array('#000','#eee','#ccc','#aaa');
							foreach($stage_structure as $s) {
								$key = array_rand($colors);
								$color = $colors[$key];

								if($s['type'] == 0)
								{
									if($s['callfrequency'] == 'daily')
									{
										$start = substr($s['stageduration']['start'], 1);  
										$end = substr($s['stageduration']['end'], 1);

										for($start; $start <= $end; $start++)
										{
											echo "<span style='background:".$color.";height:20px;width:".$daywidth."px;position:relative;display:inline-block;'><span style='position:absolute;border:1px solid #ccc;right:0px;top:21px;height:5px;'></span></span>";
										}
									}
									elseif($s['callfrequency'] == 'weekly')
									{
										$start = $s['stageduration']['start']; 
										$end = $s['stageduration']['end'];

										for($start; $start <= $end; $start++)
										{
											
										}
										
										echo "<span style='background:".$color.";height:20px;width:".$weekwidth."px;position:relative;display:inline-block;'><span style='position:absolute;border:1px solid #ccc;right:0px;top:21px;height:5px;'></span></span>";
									}
									elseif($s['callfrequency'] == 'monthly')
									{
										$start = substr($s['stageduration']['start'], 7);  
										$end = substr($s['stageduration']['end'], 7);

										for($start; $start <= $end; $start++)
										{
											echo "<span style='background:".$color.";height:20px;width:".$monthwidth."px;position:relative;display:inline-block;'><span style='position:absolute;border:1px solid #ccc;right:0px;top:21px;height:5px;'></span></span>";
										}
									}
								}
							}*/
						?>
					</div>
				</div>
			</div>
		</div>-->
		
		<!-- Contains hidden form used for add and edit stage functionality-->
		<div class="row hide" id="stageinfoformcont">
			<div class="col-md-12">
			<form class="stageinform form-horizontal text-left" role="form">
				<div class="form-group">
					<label for="inputEmail3" class="col-sm-2 control-label">Stage Type:</label>
					<div class="col-sm-9">
						<select class="form-control validate[required]" id="stagetype" name="stagetype">
							<option value="0">Pre-Birth</option>
							<option value="1">Post-Birth</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="inputPassword3" class="col-sm-2 control-label">Calls Frequency:</label>
					<div class="col-sm-9">
						<label class="radio radio-inline">
							<input  class="classfrequency " type="radio" id="daily" name="callfrequency" value="daily">
							Daily </label>
						<label class="radio radio-inline">
							<input  class="classfrequency" type="radio"  id="weekly" name="callfrequency" value="weekly" checked>
							Weekly </label>
						<label class="radio radio-inline">
							<input  class="classfrequency" type="radio" id="monthly" name="callfrequency" value="monthly">
							Monthly </label>
					</div>
				</div>
				<div class="form-group stagedurationcont">
					<label class="col-sm-2 control-label">Stage Duration:</label>
					<label class="col-sm-1 control-label">From:</label>
					<div class="col-sm-3">
						<select class="form-control hide day-select validate[required]" id="day-select-from" name="dayselectfrom">
						<?php 
							for($i=1;$i<=2093;$i++) {
								echo "<option value='day".$i."'>Day ".$i."</option>";
							}
						?>
						</select>
						<select class="form-control week-select validate[required]" id="week-select-from" name="weekselectfrom">
						<?php 
							for($i=1;$i<=299;$i++) {
								echo "<option value='".$i."'>Week ".$i."</option>";
							}
						?>
						</select>
						<select class="form-control hide month-select validate[required]" id="month-select-from"  name="monselectfrom">
						<?php 
							for($i=1;$i<=69;$i++) {
								echo "<option value='month".$i."'>Month ".$i."</option>";
							}
						?>
						</select>
					</div>
					
					<label class="col-sm-2 control-label">To:</label>
					<div class="col-sm-3">
						<select class="form-control hide day-select validate[required]" id="day-select-to"  name="dayselectto">
						<?php 
							for($i=1;$i<=2093;$i++) {
								echo "<option  value='day".$i."'>Day ".$i."</option>";
							}
						?>
						</select>
						<select class="form-control week-select validate[required]" id="week-select-to" name="weekselectto">
						<?php 
							for($i=1;$i<=299;$i++) {
								echo "<option value='".$i."'>Week ".$i."</option>";
							}
						?>
						</select>
						<select class="form-control hide month-select validate[required]" id="month-select-to" name="monselectto">
						<?php 
							for($i=1;$i<=69;$i++) {
								echo "<option value='month".$i."'>Month ".$i."</option>";
							}
						?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">No of call slots:</label>
					<div class="col-sm-9">
						<input type="text" class="form-control validate[required] noofcallslots" id="noofcallslot" name="noofcallslots" placeholder="1" value="1">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Call Slot Schedule:</label>
					<div class="col-sm-9 callslotschedulecont">
						<div class="row">
							
							<?php 
								$daysofweek = array('sun' => 'Sunday','mon' => 'Monday','tue' => 'Tuesday','wed' => 'Wednesday','thu' => 'Thursday','fri' => 'Friday','sat' => 'Saturday');
								$s=1;
								foreach($daysofweek as $key => $value)
								{
									echo '<label class="col-sm-1 control-label daylabel daylabel'.$s.'" data-day-val="'.$key.'">'.$value.'</label>';
									echo '<div class="col-sm-11 slotouttercont margin-bottom-5">';
									echo '<div class="row slotdropdowncont">';
									echo '<div class="col-sm-12">';
									echo '<select data-placeholder="Select slots" class="chosen-select col-sm-12 slotdropdown" multiple>';
									echo '<option value="1" selected>Slot 1</option>';
									echo '</select>';
									echo '</div>';
									echo '</div><br>';
									echo '<div class="row slotcont'.$key.' slotcont margin-bottom-5" data-slot-number="1">';
									echo '<label class="col-sm-3">Slot 1:  Start Time: </label>';
									echo '<div class="col-sm-3">';
									echo '<div class="row">';
									echo '<div class="col-sm-6">';
									echo '<select class="form-control" id="slot'.$s.'starttimehour" name="slot'.$s.'starttimehour">';
									for($i=1;$i<=24;$i++)
									{
										echo '<option value="'.$i.'">'.$i.'</option>';
									}
									echo '</select>';
									echo '</div>';
									
									echo '<div class="col-sm-6">';
									echo '<select class="form-control" id="slot'.$s.'starttimemin" name="slot'.$s.'starttimemin">';
									for($i=1;$i<=60;$i++)
									{
										echo '<option value="'.$i.'">'.$i.'</option>';
									}
									echo '</select>';
									echo '</div>';
									echo '</div></div>';
									
									echo '<label class="col-sm-2">End Time: </label>';
									echo '<div class="col-sm-3">';
									echo '<div class="row">';
									echo '<div class="col-sm-6">';
									echo '<select class="form-control" id="slot'.$s.'starttimehour" name="slot'.$s.'starttimehour">';
									for($i=1;$i<=24;$i++)
									{
										echo '<option value="'.$i.'">'.$i.'</option>';
									}
									echo '</select>';
									echo '</div>';
									
									echo '<div class="col-sm-6">';
									echo '<select class="form-control" id="slot'.$s.'starttimemin" name="slot'.$s.'starttimemin">';
									for($i=1;$i<=60;$i++)
									{
										echo '<option value="'.$i.'">'.$i.'</option>';
									}
									echo '</select>';
									echo '</div>';
									echo '</div><!--<div class="close-button removeslot show"><i class="glyphicon glyphicon-remove"></i></div>--></div>';
									echo '</div></div>';
									
									$s++;
								}
							?>
							
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Calls Per Week:</label>
					<div class="col-sm-9">
						<input type="text" class="form-control callsperweek validate[required]" id="inputEmail3" name="callsperfreqn" placeholder="1" value="1">
					</div>
				</div>
				<div class="form-group callshcedulemaincont">
					<label class="col-sm-2 control-label">Call Schedule:</label>
					<div class="col-sm-9 callschedulecont">
						<div class="row callattempt">
							<label class="col-sm-2 control-label">Call 1:</label>
							<div class="col-sm-10 callschedslotmaincont">
								<div class="row callschedslotcont">
									<label class="col-sm-2 control-label">Slot 1:</label>
									<div class="col-sm-8">
										<div class="row margin-top-5" id="callattempt1cont">
											<label class="col-sm-5 control-label">(1st attempt):</label>
											<div class="col-sm-7">
												<select class="form-control" id="callattempt1select" name="callattempt1select">
													<option value="sun" selected="selected">Sunday</option>
													<option value="mon">Monday</option>
													<option value="tue">Tuesday</option>
													<option value="wed">Wednesday</option>
													<option value="thu">Thursday</option>
													<option value="fri">Friday</option>
													<option value="sat">Saturday</option>
												</select>
											</div>
										</div>
										<div class="row margin-top-5">
											<label class="col-sm-5 control-label">Recall 1:</label>
											<div class="col-sm-7">
												<select class="form-control" id="callrecall1" name="callrecall1">
													<option value="sun">Sunday</option>
													<option value="mon">Monday</option>
													<option value="tue" selected="selected">Tuesday</option>
													<option value="wed">Wednesday</option>
													<option value="thu">Thursday</option>
													<option value="fri">Friday</option>
													<option value="sat">Saturday</option>
												</select>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row margin-top-5">
							<div class="col-sm-7">
							</div>
							<div class="col-sm-5">
								<a href="#" class="btn btn-info btn-xs addcall">Add Call</a>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						<button type="submit" class="btn btn-primary stagesaveall" data-project-id="<?php echo $projectid; ?>">Update</button>
					</div>
				</div>
			</form>
			</div>
		</div>
		<!-- Hidden form end -->
	
		<div class="row" id="stagesmaincont">
		<?php foreach($stage_structure as $key => $value) { ?>
			<div class="col-md-12 panel-warning stageoutermaincont" id="stage<?php echo $key.'maincont'; ?>">
				<div class="content-box-header panel-heading">
					<div class="panel-title stageheader">Stage <?php echo $key; ?></div>
					
					<div class="panel-options rightbuttons">
						<a href="/mama/projects/edit/7" class="btn btn-warning btn-xs editstage">Edit</a>
						<a href="/mama/projects/edit/7" class="btn btn-danger btn-xs delstage">Delete</a>
					</div>
				</div>
				<div class="content-box-large box-with-header">
					<div class="row stagecontent">
						<div class="stagedata hide" data-stage='<?php echo json_encode($value,true); ?>'></div>
						<div class="col-md-12">
							<div class="row">
								
								<div class="col-md-3">
									<label class="control-label">
									Stage Type:
									</label>
									<span>
									<?php 
										$stagetype = $value['type'] == 0 ? 'Pre-Birth' : 'Post-Birth'; 
										echo $stagetype;
									?>
									</span>
								</div>
								
								<div class="col-md-3 stagedurationval">
								
									<label class="control-label">
									Stage Duration:
									</label>
									<span>
									<?php 
										echo $value['stageduration']['start']." - ".$value['stageduration']['end'];
									?>
									</span>	
								</div>
								
								<div class="col-md-3">
								
									<label class="control-label">
									Calls Frequency:
									</label>
									<span>
									<?php 
										echo $value['callfrequency'];
									?>
									</span>	
								</div>

								<div class="col-md-3">
								
								</div>

							</div>
							
							<br>
							<div class="row">
								<div class="col-md-3">
									<label class="control-label">
										Number of call slots:
									</label>
									<span>
									<?php 
										echo $value['callslotsnumber'];
									?>
									</span>	
								</div>
							</div>
							
							<br>
							<div class="row">
								<label class="col-md-2 control-label">
									Call Slot schedule:
								</label>
								<div class="col-sm-5">
									<table class="table table-bordered">
										<thead>
											<tr>
												<th>Call Day</th>
												<th>Slot #</th>
												<th>Slot Start & End Time</th>
											</tr>
										</thead>
										<tbody>
											<?php 
											$i=1;
											foreach($value['callslotsdays'] as $k => $v)
											{	
												echo "<tr>";
												echo "<td>".$k."</td>";
												echo "<td>";
												foreach($v as $a => $b)
												{
													echo "Slot".$a."<br>";
												}
												echo "</td>";
												echo "<td>";
												foreach($v as $a => $b)
												{
													echo date('h:i a',strtotime($b['start']))." - ".date('h:i a',strtotime($b['end']))."<br>";
												}
												echo "</td>";
												echo "</tr>";
												$i++;
											}
											?>
										</tbody>
									</table>
								</div>

							</div>

							<br>
							<div class="row">
								<label class="col-sm-2 control-label">
									<?php if($value['callfrequency'] == "weekly"){ $v = 'week'; } elseif($value['callfrequency'] == "monthly"){ $v = 'month'; } else{$v = 'day';}?>
									Calls per <?php echo $v?>:
								</label>
								<div class="col-sm-5">
									<?php 
										echo $value['numberofcalls'];
									?>
								</div>
							</div>
							
							<br>
							<div class="row">
									<label class="col-sm-2 control-label">
										Call Schedule:
									</label>
									<div class="col-sm-5">
										<table class="table table-bordered">
											<thead>
												<tr>
													<th>Call #</th>
													<th>Call Day</th>
													<th>Recall Attempt</th>
												</tr>
											</thead>
											<tbody>
												<?php 
												$i=1;
												foreach($value['callvolume'] as $k => $v)
												{	
													echo "<tr>";
													echo "<td>Call".$i."</td>";
													echo "<td>".$v['attempt1']."</td>";
													echo "<td>".$v['call'.$i.'recall']."</td>";
													echo "</tr>";
													$i++;
												}
												?>
											</tbody>
										</table>
									</div>
			
							</div>
						
						</div>
					</div>
				</div>
			</div>

		
		<?php } ?>
		</div>
	</div>
	</div>
</div>