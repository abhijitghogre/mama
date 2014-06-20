var BASE_URL = document.location.origin;
if (BASE_URL === "http://localhost" || BASE_URL === "http://192.168.0.143") {
    BASE_URL = "http://localhost/mama";
} else {
    BASE_URL = "http://herohelpline.org/mMitra-MAMA";
}

$(document).ready(function() {
//initialize custom fields
    $('.custom-field').hide();
    $('.show-for-text').show();
    disableValidationForHiddenFields();
//custom fields change listener
    $('.field_type').change(function() {
        $('.custom-field').hide();
        $('.show-for-' + $(this).val()).show();
        disableValidationForHiddenFields();
    });

    //add new field
    $('#new-custom-fields').submit(function(e) {
        e.preventDefault();
        var fieldsArr = {};
        var type = $('.field_type').val();
        var label = $('.field-name').val();
        var name = cleanText(label);
        var default_value = $('.field-value').val();
        var required = $('.field-required').prop('checked');
        var disabled = $('.field-disabled').prop('checked');
        var rows = $('.field-rows').val();
        var cols = $('.field-cols').val();
        switch (type) {
            case "text":
                fieldsArr.type = type;
                fieldsArr.label = label;
                fieldsArr.name = name;
                fieldsArr.value = default_value;
                fieldsArr.required = required;
                fieldsArr.disabled = disabled;
                break;
            case "radio":
                fieldsArr.type = type;
                fieldsArr.label = label;
                fieldsArr.name = name;
                fieldsArr.required = required;

                var radios = [];
                $('.field-radios').each(function() {
                    radios.push({
                        checked: $(this).find('.field-radios-checked').prop('checked'),
                        disabled: $(this).find('.field-radios-disabled').prop('checked'),
                        label: $(this).find('.field-radios-label').val()
                    });
                });
                fieldsArr.radios = radios;
                break;
            case "checkbox":
                fieldsArr.type = type;
                fieldsArr.label = label;
                fieldsArr.name = name;
                fieldsArr.required = required;

                var checkboxes = [];
                $('.field-checkboxes').each(function() {
                    checkboxes.push({
                        checked: $(this).find('.field-checkbox-checked').prop('checked'),
                        disabled: $(this).find('.field-checkbox-disabled').prop('checked'),
                        label: $(this).find('.field-checkbox-label').val()
                    });
                });
                fieldsArr.checkboxes = checkboxes;
                break;
            case "select":
                fieldsArr.type = type;
                fieldsArr.label = label;
                fieldsArr.name = name;
                fieldsArr.required = required;

                var options = [];
                $('.field-select').each(function() {
                    options.push({
                        selected: $(this).find('.field-select-checked').prop('checked'),
                        disabled: $(this).find('.field-select-disabled').prop('checked'),
                        value: $(this).find('.field-select-label').val()
                    });
                });
                fieldsArr.options = options;
                break;
            case "textarea":
                fieldsArr.type = type;
                fieldsArr.label = label;
                fieldsArr.name = name;
                fieldsArr.value = default_value;
                fieldsArr.required = required;
                fieldsArr.disabled = disabled;
                fieldsArr.rows = rows;
                fieldsArr.cols = cols;
                break;
        }

        //check if the request is edit or add
        if ($('.add_field').attr('data-edit') === "true") {
            //send the generated JSON to projects controller with ajax to edit a field
            $.post(BASE_URL + '/projects/editField/' + $('.project_id').val(), {
                custom_fields: fieldsArr,
                index: $('.add_field').attr('data-edit-index')
            },
            function(response) {
                location.reload();
            });
        } else {
            //send the generated JSON to projects controller with ajax to add a field
            $.post(BASE_URL + '/projects/addField/' + $('.project_id').val(), {
                custom_fields: fieldsArr
            },
            function(response) {
                $('.custom-fields-cont').html(response);
                clearFields();
                $('#custom-field-modal').modal('hide');
            });
        }

    });

    //add checkbox/radio/select row
    $('.add-button').on('click', function() {
        switch ($(this).attr('data-type')) {
            case "checkbox":
                //copy the div from Views/Projects/manage_fields.ctp
                var row = $('.field-checkboxes').clone().wrap('<div>').parent().html();
                $('.field-checkboxes-cont').append(row);
                break;
            case "radio":
                //copy the div from Views/Projects/manage_fields.ctp
                var row = $('.field-radios').clone().wrap('<div>').parent().html();
                $('.field-radios-cont').append(row);
                break;
            case "select":
                //copy the div from Views/Projects/manage_fields.ctp
                var row = $('.field-select').clone().wrap('<div>').parent().html();
                $('.field-select-cont').append(row);
                break;
        }
    });

    //remove button click
    $(document).on('click', '.close-button', function() {
        var type = $(this).attr('data-type');
        switch (type) {
            //cases for form checkbox/radio/select
            case "radio":
                if (!($('.field-radios-cont .field-radios').length > 2)) {
                    alert('Minimum two fileds are needed for radio buttons group.');
                } else {
                    $(this).closest('.field-radios').remove();
                }
                break;
            case "checkbox":
                if (!($('.field-checkboxes-cont .field-checkboxes').length > 1)) {
                    alert('Minimum one filed is needed for check boxes group.');
                } else {
                    $(this).closest('.field-checkboxes').remove();
                }
                break;
            case "select":
                if (!($('.field-select-cont .field-select').length > 2)) {
                    alert('Minimum two fileds are needed for drop down options.');
                } else {
                    $(this).closest('.field-select').remove();
                }
                break;

                //case for custom fields
            case "customField":
                //send removal request
                removeCustomField($(this));
                break;
        }
    });

    //allow only one radio/select button to be selected while adding custom radio buttons
    $(document).on('change', '.field-radios-checked', function() {
        $('.field-radios-checked').removeAttr('checked');
        $(this).prop('checked', true);
    });
    $(document).on('change', '.field-select-checked', function() {
        $('.field-select-checked').removeAttr('checked');
        $(this).prop('checked', true);
    });

    //edit custom fields
    $(document).on('click', '.edit-button', function() {

        //focus edit fields form
        $('#custom-field-modal').modal('show');

        //change required html
        $('.newFieldFormTitle').html('Edit field');
        $('input.add_field').val('Save');
        var parent = $(this).closest('.formField');
        $('.custom-fields-cont .formField').css({'background-color': '#F0F0F0'});
        parent.css({'background-color': '#F0B6B6'});
        var type = parent.attr('data-type');
        var jsonData = JSON.parse(parent.find('.json-data').html());
        $('.field_type').val(type).trigger('change');
        $('.cancel-button-cont').html('<input type="button" data-dismiss="modal" value="Cancel" class="cancel_edit_field btn btn-primary">');
        $('.add_field').attr('data-edit', 'true');
        $('.add_field').attr('data-edit-index', parent.attr('data-index'));

        switch (type) {
            case "text":
                $('.field-name').val(jsonData.label);
                $('.field-value').val(jsonData.value);
                $('.field-required').prop('checked', "true" === jsonData.required);
                $('.field-disabled').prop('checked', "true" === jsonData.disabled);
                break;
            case "textarea":
                $('.field-name').val(jsonData.label);
                $('.field-cols').val(jsonData.cols);
                $('.field-rows').val(jsonData.rows);
                $('.field-value').val(jsonData.value);
                $('.field-required').prop('checked', "true" === jsonData.required);
                $('.field-disabled').prop('checked', "true" === jsonData.disabled);
                break;
            case "radio":
                $('.field-name').val(jsonData.label);
                $('.field-required').prop('checked', "true" === jsonData.required);
                var row = $('.field-radios').clone().wrap('<div>').parent().html();
                $('.field-radios-cont').html('');
                $.each(jsonData.radios, function(index, value) {
                    var rowObj = $(row);
                    rowObj.find('input.field-radios-label').val(value.label);
                    rowObj.find('input.field-radios-checked').prop('checked', value.checked === "true");
                    rowObj.find('input.field-radios-disabled').prop('checked', value.disabled === "true");
                    $('.field-radios-cont').append(rowObj);
                });
                break;
            case "checkbox":
                $('.field-name').val(jsonData.label);
                $('.field-required').prop('checked', "true" === jsonData.required);
                var row = $('.field-checkboxes').clone().wrap('<div>').parent().html();
                $('.field-checkboxes-cont').html('');
                $.each(jsonData.checkboxes, function(index, value) {
                    var rowObj = $(row);
                    rowObj.find('input.field-checkbox-label').val(value.label);
                    rowObj.find('input.field-checkbox-checked').prop('checked', value.checked === "true");
                    rowObj.find('input.field-checkbox-disabled').prop('checked', value.disabled === "true");
                    $('.field-checkboxes-cont').append(rowObj);
                });
                break;
            case "select":
                $('.field-name').val(jsonData.label);
                $('.field-required').prop('checked', "true" === jsonData.required);
                var row = $('.field-select').clone().wrap('<div>').parent().html();
                $('.field-select-cont').html('');
                $.each(jsonData.options, function(index, value) {
                    var rowObj = $(row);
                    rowObj.find('input.field-select-label').val(value.value);
                    rowObj.find('input.field-select-checked').prop('checked', value.selected === "true");
                    rowObj.find('input.field-select-disabled').prop('checked', value.disabled === "true");
                    $('.field-select-cont').append(rowObj);
                });
                break;
        }

    });

    //cancel edit field
    $(document).on('click', '.cancel_edit_field', function() {
        location.reload();
    });

    //manage inputs on "OTHER" field
    // for phone owner
    $('input[type="text"][name="mand-phone-owner-other"]').hide();
    $('input[type="text"][name="mand-phone-owner-other"]').prop('disabled', true);
    $('input[name="mand-phone-owner"]').change(function() {
        if ($('input[type="radio"][name="mand-phone-owner"][value="other"]').prop('checked')) {
            $('input[type="text"][name="mand-phone-owner-other"]').show().prop('disabled', false);
        } else {
            $('input[type="text"][name="mand-phone-owner-other"]').hide().prop('disabled', true);
        }
    });
    // for alternate phone owner
    $('input[type="text"][name="mand-phone-owner-alt-other"]').hide();
    $('input[type="text"][name="mand-phone-owner-alt-other"]').prop('disabled', true);
    $('input[name="mand-phone-owner-alt"]').change(function() {
        if ($('input[type="radio"][name="mand-phone-owner-alt"][value="other"]').prop('checked')) {
            $('input[type="text"][name="mand-phone-owner-alt-other"]').show().prop('disabled', false);
        } else {
            $('input[type="text"][name="mand-phone-owner-alt-other"]').hide().prop('disabled', true);
        }
    });
    // for successful delivery and date
    $('input[name="mand-delivery-date"]').prop('disabled', true);
    $('input[name="mand-delivery-date"]').closest('.formField').hide();
    $('input[type="radio"][name="mand-delivery-status"]').change(function() {
        if ($(this).val() === '0' || $(this).val() === 0) {
            $('input[name="mand-delivery-date"]').prop('disabled', true);
            $('input[name="mand-delivery-date"]').closest('.formField').hide();
        } else {
            $('input[name="mand-delivery-date"]').prop('disabled', false);
            $('input[name="mand-delivery-date"]').closest('.formField').show();
        }
    });
    // for phone code
    $('input[name="mand-phone-code"]').prop('disabled', true);
    $('input[name="mand-phone-code"]').closest('.formField').hide();
    $('input[type="radio"][name="mand-phone-type"]').change(function() {
        if (($(this).val() === '1' || $(this).val() === 1) || ($(this).val() === '2' || $(this).val() === 2)) {
            $('input[name="mand-phone-code"]').prop('disabled', true);
            $('input[name="mand-phone-code"]').closest('.formField').hide();
        } else {
            $('input[name="mand-phone-code"]').prop('disabled', false);
            $('input[name="mand-phone-code"]').closest('.formField').show();
        }
    });
    //for birth place
    $('input[type="text"][name="mand-birth-place-other"]').hide();
    $('input[type="text"][name="mand-birth-place-other"]').prop('disabled', true);
    $('input[name="mand-birth-place"]').change(function() {
        if ($('input[type="radio"][name="mand-birth-place"][value="other"]').prop('checked')) {
            $('input[type="text"][name="mand-birth-place-other"]').show().prop('disabled', false);
        } else {
            $('input[type="text"][name="mand-birth-place-other"]').hide().prop('disabled', true);
        }
    });

    //handle new user enrollment
    $('#newUserForm').submit(function(e) {
        e.preventDefault();

        //validate LMP - gestational age - registration date
        if ($('input[name="mand-lmp"]').val() === "") {
            if ($('input[name="mand-gest-age"]').val() === "" || $('input[name="mand-reg-date"]').val() === "") {
                alert('Either fill in gestational age and registration date or fill in LMP.');
                return;
            }
        }

        //create custom fields json and store it in a hidden input
        var fields = {};
        $('.custom-field-input').each(function(index, value) {
            var fIndex = $(this).closest('.formField').attr('data-index');
            var type = $(this).attr('type');
            switch (type) {
                case "checkbox":
                    if ($(this).prop('checked')) {
                        if (typeof fields[fIndex] === 'undefined') {
                            fields[fIndex] = [];
                        }
                        fields[fIndex].push($(this).val());
                    }
                    break;
                case "radio":
                    if ($(this).prop('checked')) {
                        fields[fIndex] = $(this).val();
                    }
                    break;
                case "text":
                    fields[fIndex] = $(this).val();
                    break;
                case "textarea":
                    fields[fIndex] = $(this).val();
                    break;
                case "select":
                    fields[fIndex] = $(this).val();
                    break;
            }
        });

        //check if request is for adding new user or editing the pre added user
        if ($(this).attr('data-edit-flag') === '1') {
            $.post(BASE_URL + '/users/add/' + $('.project_id').val(), {
                formData: $(this).serialize(),
                customFields: fields,
                editing: "1",
                user_id: $('input.user_id').val(),
                user_meta_id: $('input.user_meta_id').val(),
                user_callflag_id: $('input.user_callflag_id').val()
            }, function(response) {
                if (JSON.parse(response).type === "success") {
                    window.location.replace(BASE_URL + '/users/listUsers/' + $('.project_id').val());
                } else {
                    alert('Some error occured. Please contact administrator');
                }
            });
        } else {
            $.post(BASE_URL + '/users/add/' + $('.project_id').val(), {
                formData: $(this).serialize(),
                customFields: fields
            }, function(response) {
                if (JSON.parse(response).type === "success") {
                    window.location.replace(BASE_URL + '/users/listUsers/' + $('.project_id').val());
                } else {
                    alert('Some error occured. Please contact administrator');
                }
            });
        }

    });

    //editing user. prepopulate form
    if ($('.dom-edit-flag').length > 0) {

        var data = JSON.parse($('.fields-data').html());
        //name
        $('input[name="mand-name"]').val(data.User.name);

        //age
        $('input[name="mand-age"]').val(data.UserMeta.age);

        //education
        $('input[name="mand-education"][value="' + data.UserMeta.education + '"]').prop('checked', true);

        //phone
        $('input[name="mand-phone"]').val(data.User.phone_no);

        //phone type
        if ($.inArray(data.User.phone_type, ["1", "2"]) > -1) {
            $('input[name="mand-phone-type"][value="' + data.User.phone_type + '"]').prop('checked', true);
        } else {
            $('input[name="mand-phone-type"][value="' + data.User.phone_type + '"]').prop('checked', true).trigger('change');
            $('input[name="mand-phone-code"]').val(data.User.phone_code);
        }

        //phone owner
        if ($.inArray(data.UserMeta.phone_owner, ['woman', 'husband', 'family']) > -1) {
            $('input[name="mand-phone-owner"][value="' + data.UserMeta.phone_owner + '"]').prop('checked', true);
        } else {
            $('input[name="mand-phone-owner"][value="other"]').prop('checked', true).trigger('change');
            $('input[name="mand-phone-owner-other"]').val(data.UserMeta.phone_owner);
        }

        //alternate phone
        $('input[name="mand-phone-alt"]').val(data.UserMeta.alternate_no);

        //phone owner alternate
        if ($.inArray(data.UserMeta.alternate_no_owner, ['woman', 'husband', 'family']) > -1) {
            $('input[name="mand-phone-owner-alt"][value="' + data.UserMeta.alternate_no_owner + '"]').prop('checked', true);
        } else {
            $('input[name="mand-phone-owner-alt"][value="other"]').prop('checked', true).trigger('change');
            $('input[name="mand-phone-owner-alt-other"]').val(data.UserMeta.alternate_no_owner);
        }

        //lmp
        $('input[name="mand-lmp"]').val(data.User.lmp);

        //gest age
        $('input[name="mand-gest-age"]').val(data.User.enroll_gest_age);

        //reg date
        $('input[name="mand-reg-date"]').val(data.User.registration_date);

        //call slot
        $('select[name="mand-call-slot"] option[value="' + data.User.call_slots + '"]').prop('selected', true);

        //delivery status
        if ($.inArray(data.User.delivery, ['0']) > -1) {
            $('input[name="mand-delivery-status"][value="' + data.User.delivery + '"]').prop('checked', true);
        } else {
            $('input[name="mand-delivery-status"][value="' + data.User.delivery + '"]').prop('checked', true).trigger('change');
            $('input[name="mand-delivery-date"]').val(data.User.delivery_date);
        }

        //language
        $('input[name="mand-language"][value="' + data.User.language + '"]').prop('checked', true);

        //birth place
        if ($.inArray(data.UserMeta.birth_place, ['same-village']) > -1) {
            $('input[name="mand-birth-place"][value="' + data.UserMeta.birth_place + '"]').prop('checked', true);
        } else {
            $('input[name="mand-birth-place"][value="other"]').prop('checked', true).trigger('change');
            $('input[name="mand-birth-place-other"]').val(data.UserMeta.birth_place);
        }


        /**
         * Custom fields
         */

        $('.custom-field-input').each(function(index, value) {
            var current_input = $(this);
            var fIndex = current_input.closest('.formField').attr('data-index');
            var custom_values = JSON.parse(data.UserMeta.custom_fields);

            var type = current_input.attr('type');
            switch (type) {
                case "checkbox":
                    current_input.prop('checked', false);
                    $.each(custom_values[fIndex], function(index, value) {
                        if (current_input.val() === value) {
                            current_input.prop('checked', true);
                        }
                    });
                    break;
                case "radio":
                    current_input.prop('checked', false);
                    if (current_input.val() === custom_values[fIndex]) {
                        current_input.prop('checked', true);
                    }
                    break;
                case "text":
                    current_input.val(custom_values[fIndex]);
                    break;
                case "textarea":
                    current_input.val(custom_values[fIndex]);
                    break;
                case "select":
                    current_input.find('option[value="' + custom_values[fIndex] + '"]').prop('selected', true);
                    break;
            }
        });
    }

    //hide form message on click
    $(document).on('click', '.formMessage', function() {
        $('#flashMessage').remove();
    });

    //relode page after model is hidden
    $('#custom-field-modal').on('hidden.bs.modal', function(e) {
        location.reload();
    });

    /*logs page*/
    var oTable = $('#report').dataTable();
    $('#LogsProjectId').on('change', function() {
        var id = $(this).val();
        if (id != 0) {
            $('.username').hide();
            $('.project' + id).show();
        } else {
            $('.username').show();
        }

    });
    // setting fromdate and todate in logs form
    var now = new Date();
    var day = ("0" + now.getDate()).slice(-2);
    var month = ("0" + (now.getMonth() + 1)).slice(-2);
    var today = now.getFullYear() + "-" + (month) + "-" + (day);
    var fdate = new Date();
    fdate = ("0" + (fdate.getDate() - fdate.getDay() + 1)).slice(-2);
    var fromdate = now.getFullYear() + "-" + (month) + "-" + (fdate);
    $('#LogsFromdate').val(fromdate);
    $('#LogsTodate').val(today);

    $('#generatereport').on('click', function(e) {
        e.preventDefault();
        var projectId = $('#LogsProjectId').val();
        var userId = $('#LogsUserId').val();
        var callCode = $('#LogsCallCode').val();
        var fromDate = $('#LogsFromdate').val();
        var toDate = $('#LogsTodate').val();
        var other = $('#LogsOther').val();
        $('.loader').show();
        $.post(BASE_URL + '/logs/generate_report', {projectId: projectId, userId: userId, callCode: callCode, fromDate: fromDate, toDate: toDate}, function(data) {
            oTable.fnDestroy();
            $('#report').replaceWith(data);
            oTable = $('#report').dataTable({"aaSorting": [[6, "desc"]]});
            $('.loader').hide();
        });
    });

    /***
     * TEST CODE BELOW REMOVE AFTER USE
     */
    /*
     $('input[name="mand-name"]').val('Jane Doe');
     $('input[name="mand-age"]').val(42);
     $('input[name="mand-phone"]').val(8898132628);
     $('input[name="mand-phone-alt"]').val(8898132628);
     $('input[name="mand-phone-type"]').prop('checked', true);
     $('input[name="mand-phone-type"]').trigger('change');
     $('input[name="mand-phone-code"]').val('022');
     $('input[name="mand-phone-owner"]').prop('checked', true);
     $('input[name="mand-phone-owner"]').trigger('change');
     $('input[name="mand-phone-owner-other"]').val('neighbour');
     $('input[name="mand-lmp"]').val("2014-05-16");
     $('input[name="mand-reg-date"]').val("2014-05-16");
     $('input[name="mand-gest-age"]').val(42);
     */
	 
	/*Edit stage functionality*/
	$('.editstage').on('click', function(e){
		e.preventDefault();
		var delbutton = $(this).next('.delstage');
		var container = $(this).parents('.content-box-header').next('.content-box-large');
		var stagedata =  $.parseJSON(container.find('.stagecontent > .stagedata').attr('data-stage'));

		var emptyform = $("#stageinfoformcont").clone();
		
		var preparedform = prepform(stagedata,emptyform);
		
		delbutton.addClass('canceleditstage').text('Cancel').removeClass('delstage');
		container.prepend(preparedform[0].removeClass('hide')).find('.stagecontent').hide();
		container.parents('.stageoutermaincont').attr('data-form-no',preparedform[1]);
		$(this).hide();
		
		
	});
	
	/*Cancel when edit stage is activated functionality*/
	$(document).on('click','.canceleditstage', function(e){
		e.preventDefault();
		var container = $(this).parents('.content-box-header').next('.content-box-large');
		container.find('#stageinfoformcont').remove();
		container.find('.stagecontent').show();
		$(this).addClass('delstage').text('Delete').removeClass('canceleditstage');
		$(this).parent('.panel-options').find('.editstage').show();
	});
	
	
	/* add stage functionality */
	$(document).on('click','.addstageli',function(e){
		e.preventDefault();
		
		var pos = $(this).attr('data-stage-pos');
		var stagemaincont = $('#stagesmaincont');
		var stagetemplate = '<div class="col-md-12 panel-warning stageoutermaincont newstagemaincont"><div class="content-box-header panel-heading"><div class="panel-title stageheader">New Stage</div><div class="panel-options"><a href="/mama/projects/edit/7" class="btn btn-danger btn-xs remstage">Remove</a></div></div><div class="content-box-large box-with-header"><div class="row stagecontent"></div>';
		var emptyform = $("#stageinfoformcont").clone();
		var randomnumber=Math.floor(Math.random()*10000)
		emptyform.find('.stageinform').attr('id','form'+randomnumber);
			
		addvalidatiomplugintoform('form'+randomnumber);
		
		//makes the template into an object and adds the cloned form to the template
		var temp = $.parseHTML(stagetemplate);
			$(temp).find('.stagecontent').html(emptyform);
			$(temp).find('#stageinfoformcont').removeClass('hide');
			
		if(pos == 'start')
		{
			stagemaincont.prepend(temp);
		}
		else if(pos == 'end')
		{
			stagemaincont.append(temp);
		}
		else
		{
			stagemaincont.find('#'+pos+'maincont').after(temp);
		}
		stagemaincont.find('.chosen-select').chosen();
	});
	
	
	/*adds different stage data to add li dropdown*/
	if($('.addstage').length)
	{
		$('.stageheader').each(function(index){
			$('#addstagedropdown').append('<li data-stage-pos="'+$(this).text().toLowerCase()+'" class="addstageli"><a href="#">After '+$(this).text()+'</a></li>')
		});
	}
	
	//removes new stage element added by click on add stage option
	$(document).on('click','.remstage',function(e){
		e.preventDefault();
		$(this).parents('.newstagemaincont').remove();
	});
	
	
	$(document).on('click','.stagesaveall',function(e){
		e.preventDefault();
		var projectid = $(this).attr('data-project-id');
		var newstagejson = createnewstagejson();
		// $.post(BASE_URL + '/projects/saveallstages',{projectid : projectid, data: newstagejson},function(){
			
		// });
	});
	
	$(document).on('click','.removeslot',function(e){
		e.preventDefault();
		var mainparent = $(this).parents('.callslotschedulecont');
		var daykey = mainparent.attr('data-day-val');
		console.log(daykey);
		/*if(mainparent.find('.slotouttercont').find('.slotcont'+daykey).length > 1)
		{
			mainparent.find('.slotouttercont').remove();
		}
		else
		{
			$(this).parents('.slotouttercont').css('visibility','hidden');
		}*/
	});
	
	$(document).on('click','.addcall',function(e){
		e.preventDefault();
		var thisform = $(this).parents('.stageinform ');
		var mainparent = $(this).parents('.callschedulecont');
		addcallelement(mainparent);
		
		thisform.find('.callsperweek ').val(parseInt(thisform.find('.callsperweek ').val()) + 1);
	});
	
	$(document).on('click','.removecall',function(e){
		e.preventDefault();
		var thisform = $(this).parents('.stageinform ');
		
		//$(this).parents('.callattempt').remove();
		$('.callschedulecont .callattempt').last().remove();
		thisform.find('.callsperweek ').val(parseInt(thisform.find('.callsperweek ').val()) - 1);
	});
	
	$(document).on('blur','.callsperweek',function(e){
		e.preventDefault();
		var callval = $(this).val();
		var callschedulmaincont = $(this).parents('.form-group').next('.form-group');
		var callschval = callschedulmaincont.find('.callschedulecont .callattempt').length;
		var addbutton = callschedulmaincont.find('.addcall');
		var mainparent = callschedulmaincont.find('.callschedulecont');
		
		if(callval != callschval)
		{
			var diff = Math.abs(parseInt(callval - callschval));
			for(i=1; i<=diff; i++)
			{
				addcallelement(mainparent);
			}
		}
	});
	
	$(document).on('blur','.noofcallslots',function(e){
		e.preventDefault();
		var callval = parseInt($(this).val())
		var callslotmaincont = $(this).parents('.form-group').next('.form-group').find('.callslotschedulecont');
		var slotouttercont = callslotmaincont.find('.slotouttercont');
		
		slotouttercont.each(function(index,element){
			var selecttag = $(element).find('.slotdropdown').append('<option>');
			selecttag.html('');
			for(i=1;i<=callval;i++)
			{
				selecttag.append('<option value="'+i+'">Slot '+i+'</option>');
			}
		});
		$('.chosen-select').trigger('chosen:updated');
	});
	
	$(document).on('change','.chosen-select', function(evt, params) {
		console.log(params);

		if(typeof params.selected !== 'undefined')
		{
			var slotelement = createslotelement(params.selected);
			prevslotcont = $(this).parents('.slotouttercont').find('.slotcont[data-slot-number='+(params.selected-1)+']');
			if(typeof prevslotcont === 'undefined')
			{
				$(this).parents('.slotouttercont').find('.slotcont').last().append(slotelement);
			}
			else
			{
				prevslotcont.after(slotelement)
			}
		}
		else
		{
			$(this).parents('.slotouttercont').find('.slotcont[data-slot-number='+(params.deselected)+']').remove();
		}
	});
	
});//ready ends

//converts something like "Hello, This is my text." into "hello-this-is-my-text"
function cleanText(text) {
    return text.trim().toLowerCase()
            .replace(/ {2,}| /g, '-')
            .replace(/[^\w-]+/g, '');
}

//disable the hidden fields so that they dont get validated
function disableValidationForHiddenFields() {
    $('#new-custom-fields .formField').each(function() {
        $(this).find('input').prop('disabled', false);
        if ($(this).css('display') === "none") {
            $(this).find('input').prop('disabled', true);
        }
    });
}

//clear fields on ajax complete
function clearFields() {
    $('.field-name').val('');
    $('.field-checkbox-label').val('');
    $('.field-radios-label').val('');
    $('.field-value').val('');
    $('.field-select-label').val('');
    $('.field-rows').val('');
    $('.field-cols').val('');
}

//remove custom field
function removeCustomField(field) {

    if (confirm("Are you sure you want to remove this field? Press OK to remove the field else Cancel.")) {
        $.post(BASE_URL + '/projects/removeField/' + $('.project_id').val(), {
            index: field.closest('.formField').attr('data-index')
        },
        function(response) {
            $('.custom-fields-cont').html(response);
        });
    }
}


//adds default current stage data to an empty form during edit stage operation
function prepform(stagedata,emptyformcont)
{
	var filledformcont = emptyformcont;
	var filledform = emptyformcont.find('.stageinform');
	
	var randomnumber = Math.floor(Math.random()*10000)
	filledform.attr('id','form'+randomnumber);
	addvalidatiomplugintoform('form'+randomnumber);
	
	if(stagedata.type == 0)
		filledform.find('#stagetype option[value="0"]').attr('select','selected');
	else
		filledform.find('#stagetype option[value="1"]').attr('select','selected');
	
	if(stagedata.callfrequency == "daily")
	{	
		filledform.find('#daily').prop('checked',true);
		filledform.find('select.day-select').removeClass('hide').addClass('show');
		filledform.find('select#day-select-from option[value="'+stagedata.stageduration.start+'"]').attr('selected','selected');
		filledform.find('select#day-select-to option[value="'+stagedata.stageduration.end+'"]').attr('selected','selected');
	}
	else if(stagedata.callfrequency == "weekly")
	{	
		filledform.find('#weekly').prop('checked',true);
		filledform.find('select.week-select').removeClass('hide').addClass('show');
		filledform.find('select#week-select-from option[value="'+stagedata.stageduration.start+'"]').attr('selected','selected');
		filledform.find('select#week-select-to option[value="'+stagedata.stageduration.end+'"]').attr('selected','selected');
	}
	else if(stagedata.callfrequency == "monthly")
	{	
		filledform.find('#monthly').prop('checked',true);
		filledform.find('select.month-select').removeClass('hide').addClass('show');
		filledform.find('select#month-select-from option[value="'+stagedata.stageduration.start+'"]').attr('selected','selected');
		filledform.find('select#month-select-to option[value="'+stagedata.stageduration.end+'"]').attr('selected','selected');
	}
	
	
	filledform.find('.callsperweek').val(stagedata.numberofcalls);
	
	if(stagedata.numberofcalls > 0)
	{
		filledform.find('.callschedulecont').html('');
		var count = stagedata.numberofcalls;
		for(i = 1;i <= count; i++)
		{
			filledform.find('.callschedulecont').append('<div class="row callattempt"><div class="col-sm-12"><div class="row margin-top-5" id="callattempt'+i+'cont">'+
							'<label class="col-sm-5 control-label">Call '+i+' (1st attempt):</label>'+
							'<div class="col-sm-7">'+
								'<select class="form-control" id="callattempt'+i+'select" name="callattempt'+i+'select">' +
									'<option value="sun">Sunday</option>'+
									'<option value="mon">Monday</option>'+
									'<option value="tue">Tuesday</option>'+
									'<option value="wed">Wednesday</option>'+
									'<option value="thu">Thursday</option>'+
									'<option value="fri">Friday</option>'+
									'<option value="sat">Saturday</option>'+
								'</select>'+
							'</div>'+
						'</div>');
			filledform.find('#callattempt'+i+'select option[value='+stagedata.callvolume[i].attempt1+']').attr('selected','selected');
			
			filledform.find('#callattempt'+i+'cont').after('<div class="row">'+
							'<label class="col-sm-5 control-label">Call '+i+' Recall '+i+':</label>'+
							'<div class="col-sm-7">'+
								'<select class="form-control" id="callrecall'+i+'" name="callrecall'+i+'">'+
									'<option value="sun">Sunday</option>'+
									'<option value="mon">Monday</option>'+
									'<option value="tue">Tuesday</option>'+
									'<option value="wed">Wednesday</option>'+
									'<option value="thu">Thursday</option>'+
									'<option value="fri">Friday</option>'+
									'<option value="sat">Saturday</option>'+
								'</select>'+
							'</div>'+
						'</div></div></div>');
			filledform.find('.callschedulecont').append('<div class="row margin-top-5"><div class="col-sm-7"></div><div class="col-sm-5"><a href="#" class="btn btn-info btn-xs addcall">Add Call</a></div></div>');
			filledform.find('#callrecall'+i+' option[value='+stagedata.callvolume[i].call1recall+']').attr('selected','selected');						
		}
	}

	filledform.find('#noofcallslot').val(stagedata.callslotsnumber);
	
	if(stagedata.callslotsnumber > 0)
	{
		var struct = '';
		
		$.each(stagedata.callslotsdays, function( key, value ) {
			var i = 1;
			struct += '<div class="row"><label class="col-sm-1 control-label daylabel'+i+'" data-day-val="'+key+'">'+key+':</label><div class="col-sm-11">';
			$.each(value, function( k, v ) {
				struct += '<div class="row">'+
								'<label class="col-sm-3">'+
									'Slot '+k+':  Start Time: '+
								'</label>'+
								'<div class="col-sm-4">'+
								'<div class="row">'+
									'<div class="col-sm-6">'+
								'<select class="form-control" id="slot'+k+'starttimehour" name="slot'+k+'starttimehour">';
									var sp = v.start.split("");
									var sh = sp[0]+""+sp[1];
									var sm = sp[2]+""+sp[3];
									var ep = v.start.split("");
									var eh = ep[0]+""+ep[1];
									var em = ep[2]+""+ep[3];
									var select = '';
									
									for(d=1;d <= 24; d++)
									{
										if(sh == d)
											select = 'selected';
										struct += '<option value="'+d+'" '+select+'>'+d+'</option>';
										select = '';
									}
				struct +=		'</select></div><div class="col-sm-6">';	
				struct +=		'<select class="form-control"  id="slot'+k+'starttimemin" name="slot'+k+'starttimemin">';
									for(d=1;d <= 60; d++)
									{
										if(sm == d)
											select = 'selected';
										struct += '<option value="'+d+'" '+select+'>'+d+'</option>';
										select = '';
									}					
				struct += 		'</select></div></div>'+
								'</div>'+
								'<label class="col-sm-2">'+
									'End Time: '+
								'</label>'+
								'<div class="col-sm-4">'+
								'<div class="row">'+
									'<div class="col-sm-6">'+
									'<select class="form-control"  id="slot'+k+'endtimehour" name="slot'+k+'endtimehour">';
										var ep = v.end.split("");
										var eh = ep[0]+""+ep[1];
										var em = ep[2]+""+ep[3];
										var select = '';
										
										for(d=1;d <= 24; d++)
										{
											if(eh == d)
												select = 'selected';
											struct += '<option value="'+d+'" '+select+'>'+d+'</option>';
											select = '';
										}
					struct +=		'</select></div><div class="col-sm-6">';	
					struct +=		'<select class="form-control"  id="slot'+k+'endtimemin" name="slot'+k+'endtimemin">';
										for(d=1;d <= 60; d++)
										{
											if(em == d)
												select = 'selected';
											struct += '<option value="'+d+'" '+select+'>'+d+'</option>';
											select = '';
										}					
					struct += 		'</select></div>'+
								'</div>'+
								'</div>'+
							'</div>';
			});
			
			struct +=  '</div></div>';
			i++;
		});	
		
		filledform.find('.callslotschedulecont').html('').append(struct);
	}
	
	emptyformcont.find('.stageinform').replaceWith(filledform);
	
	var reply = new Object();
	reply[0] = emptyformcont;
	reply[1] = randomnumber;
	return reply;
}


function createnewstagejson()
{
	var i=1;
	var stagejson = '';
	var allstagejson = '{';
	
	$('.stageoutermaincont').each(function(index){
		
		allstagejson += i == 1 ? '' : '},'
		if($(this).hasClass('newstagemaincont'))
		{
			stagejson = createjsonformdata($(this).find('.stageinform'));
		}
		else if(!$(this).hasClass('newstagemaincont') && $(this).find('#stageinfoformcont').length)
		{
			stagejson = createjsonformdata($(this).find('.stageinform'));
		}
		else
		{
			stagejson = $(this).find('.stagedata').attr('data-stage'); 
		}
		
		allstagejson += '"'+i+'":'+stagejson;
		;
		i++;
	});
	
	allstagejson += '}';
	
	//console.log(allstagejson);

	var validateresults = validatestagetimeline(allstagejson);
				
	if(validateresults['flag'] == 1)
	{
		var prevstagecont = $('#stagesmaincont').find('.stageoutermaincont:eq('+(validateresults['prevstage'] - 1)+')');
		var prevstageform = prevstagecont.find('.stageinform');
		
		if(prevstageform.length)
		{
			prevstageform.find('.stagedurationcont').css('border','1px solid #000');
		}
		else
		{
			prevstagecont.find('.stagedurationval').css('border','1px solid #000');
		}

		var presentstagecont = $('#stagesmaincont').find('.stageoutermaincont:eq('+(validateresults['presentstage'] - 1)+')')
		var presentform = presentstagecont.find('.stageinform');
		if(presentform.length)
		{
			presentform.find('.stagedurationcont').css('border','1px solid #000');
		}
		else
		{
			presentstagecont.find('.stagedurationval').css('border','1px solid #000');
		}
		
		$('.toperrorbox').text('huge errpr');
	}
	else
	{
		return allstagejson;
	}
}

function createjsonformdata(formelements)
{
	var json = '{';
	
	var callfrequency = '';
	
	$.each(formelements,function(index,value){

		$.each(value,function(i,v){
		var th = $(v);
		switch(th.attr('name'))
		{
			case 'stagetype':
				json += '"type":"'+th.val()+'",'
			break;
			
			case 'callfrequency':
				if(th.is(':checked'))
				{
					json += '"callfrequency":"'+th.val()+'",'
					callfrequency = th.val();
				}
			break;
			
			case 'dayselectfrom':
				if(callfrequency == 'daily')
				{
					json += '"stageduration":{"start":"'+th.val()+'",'
				}
			break;
			
			case 'weekselectfrom':
				if(callfrequency == 'weekly')
				{
					json += '"stageduration":{"start":"'+th.val()+'",'
				}
			break;
			
			case 'monselectfrom':
				if(callfrequency == 'monthly')
				{
					json += '"stageduration":{"start":"'+th.val()+'",'
				}
			break;
			
			case 'dayselectto':
				if(callfrequency == 'daily')
				{
					json += '"end":"'+th.val()+'"},'
				}
			break;
			
			case 'weekselectto':
				if(callfrequency == 'weekly')
				{
					json += '"end":"'+th.val()+'"},'
				}
			break;
			
			case 'monselectto':
				if(callfrequency == 'monthly')
				{
					json += '"end":"'+th.val()+'"},'
				}
			break;
			
			case 'callsperfreqn':
				var c = th.val();
				json += '"numberofcalls":"'+c+'","callvolume":{'
				
				for(i=1;i<=c;i++)
				{
					json += '"'+i+'":{"attempt1":"'+$('#callattempt'+i+'select').val()+'",';
					json += '"call'+i+'recall":"'+$('#callrecall'+i).val()+'"}';
					json += i==c ? '' : ',';
				}
				json += '},'
			break;
			
			case 'noofcallslots':
				var c = th.val();
				console.log(c);
				json += '"callslotsnumber":"'+c+'","callslotsdays":{';
				
				for(var i=1;i<=7;i++)
				{
					if($('.daylabel'+i).length)
					{
						json += '"'+$('.daylabel'+i).attr('data-day-val')+'":{';
						for(var d=1;d<=c;d++)
						{
							json += '"'+d+'":{"start":"'+$('#slot'+d+'starttimehour').val()+$('#slot'+d+'starttimemin').val()+'",';
							json += '"end":"'+$('#slot'+d+'endtimehour').val()+$('#slot'+d+'endtimemin').val()+'"}';
							json += d==c ? '' : ',';
						}
						json += i == 7 ? '' : '},';
					}
				}
				json += '}';
			break;
			
			case 'default':
			break;
			
		}
		});
	});
	
	json += '}';
	return json;
}

function addvalidatiomplugintoform(id)
{
	$('#'+id).validationEngine();
}

function validatestagetimeline(json)
{
	var json = $.parseJSON(json);
	var error = new Object();
	error['flag'] = 0;
	var prebirtharray = [];
	var start = json[1].stageduration.start;
	var prevend = 0;
	var prevsatge = 0;
	
	var prebirthweeks = 39;
	var postbirthweeks  = 260;
	$.each(json,function(index,value){
		//console.log(prevend+" "+value.stageduration.start+" "+value.stageduration.end+" "+(prevend++));
		if(value.type == '0')
		{
			if(prevend != 0 && value.stageduration.start < prebirthweeks && value.stageduration.start != (prevend++))	
			{
				error['flag'] = 1;
				error['type'] = 'datemissmatch';
				error['stagetype'] = 'prebirth';
				error['prevstage'] = prevsatge;
				error['presentstage'] = index;
				prevend = index;
				return false;
			}
			else
			{
				prevend = value.stageduration.end;
				prevsatge = index;
			}
		}
	});
	
	return error;
}

function addcallelement(mainparent)
{
	var presentkey = (mainparent.find('.callattempt').length)+1;
	var element = mainparent.find('.callattempt').first().clone();
	console.log(presentkey);
	
	element.find('#callattempt1cont').attr('id','callattempt'+presentkey+'cont').append('<div class="close-button removecall show"><i class="glyphicon glyphicon-remove"></i></div>').find('label').text('Call '+presentkey+' (1st attempt):');
	element.find('#callattempt1select').attr('id','callattempt'+presentkey+'select').attr('name','callattempt'+presentkey+'select')
	element.find('#callrecall1').attr('id','callrecall1'+presentkey).attr('name','callrecall1'+presentkey);
	mainparent.find('.callattempt').last().after(element);
}

function createslotelement(index)
{
	var element = '';
	element = '<div class="row slotcontsun slotcont" data-slot-number="'+index+'"><label class="col-sm-3">Slot '+index+':  Start Time: </label><div class="col-sm-4"><div class="row"><div class="col-sm-6"><select class="form-control" id="slot'+index+'starttimehour" name="slot'+index+'starttimehour"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option></select></div><div class="col-sm-6"><select class="form-control" id="slot'+index+'starttimemin" name="slot'+index+'starttimemin"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option></select></div></div></div><label class="col-sm-3">End Time: </label><div class="col-sm-4"><div class="row"><div class="col-sm-6"><select class="form-control" id="slot'+index+'starttimehour" name="slot'+index+'starttimehour"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option></select></div><div class="col-sm-6"><select class="form-control" id="slot'+index+'starttimemin" name="slot'+index+'starttimemin"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option></select></div></div><div class="close-button removeslot show"><i class="glyphicon glyphicon-remove"></i></div></div></div>';
	
	return element;
}