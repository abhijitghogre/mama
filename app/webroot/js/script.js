var BASE_URL = document.location.origin;
if (BASE_URL === "http://localhost") {
    BASE_URL = "http://localhost/hero/v2/mama";
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
                user_meta_id: $('input.user_meta_id').val()
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
            console.log('here');
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

    /***
     * TEST CODE BELOW REMOVE AFTER USE
     */

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