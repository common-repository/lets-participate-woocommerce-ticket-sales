// Loaded
var $ = jQuery;
var prevWalkerId;
var prevWalkerDob;
var prevWalkerCheckDetailsBtn;
var cantRememberDialog;
var submitEmailBtn;

var walkedBeforeBtn;
var notWalkedBeforeBtn;

var firstNameInput, surnameInput,
    emailInput, phoneInput,
    countryInput, townInput,
    dobInput, genderInput,
    nationalityInput;

$(document).ready(function () {
    $('.js-dob-picker input').datepicker({
        dateFormat: 'dd-mm-yy',
        changeYear: true,
        changeMonth: true,
        maxDate: -1,
        minDate: "-150y",
        yearRange: "-150:-1",
        onSelect: function (date, picker) {
            if (letspart_checkIdAndDobFilledIn()) {
                letspart_showConfirmDetailsBtn(true);
            } else {
                letspart_showConfirmDetailsBtn(false);
            }
        }
    });

    $('#place_order').hide();

    walkedBeforeBtn = $('#btn-walked-before');
    notWalkedBeforeBtn = $('#btn-not-walked-before');
    walkedBeforeBtn.click(function (e) {
        e.preventDefault();
        document.querySelector('#letspart_custom_checkout_field').style.display = "block";
        document.querySelector('#part-details-form').style.display = "none";
        $('#place_order').hide();
    });
    notWalkedBeforeBtn.click(function (e) {
        e.preventDefault();
        document.querySelector('#letspart_custom_checkout_field').style.display = "none";
        document.querySelector('#part-details-form').style.display = "block";
        $('#place_order').show();
    });

    prevWalkerId = document.querySelector('#lp_walkerId');
    prevWalkerDob = document.querySelector('#lp_prevWalkerDOB');
    prevWalkerCheckDetailsBtn = document.querySelector('#check-details');

    if (
        prevWalkerId.value != null
        && prevWalkerId.value.length > 0
        && prevWalkerDob.value.length > 0
        && prevWalkerDob.value != null
    ) {
        $('#walked_before_title').hide();
        walkedBeforeBtn.hide();
        notWalkedBeforeBtn.hide();
        letspart_fetchParticipant();
    }

    cantRememberDialog = document.querySelector('#lp_forgotten_details_modal');
    cantRememberDialog.addEventListener('click', function (e) {
        if (cantRememberDialog != e.target) return;
        letspart_hideCantRememberDialog();
    });

    submitEmailBtn = document.querySelector('#lp_forgotten_details_modal button');
    submitEmailBtn.addEventListener('click', function (e) {
        e.preventDefault();

        letspart_disableBtn($('#lp_forgotten_details_modal button'));

        var email = document.querySelector('#forgottenEmailField').value;
        if (email == null || email.length < 1) {
            letspart_enableBtn($('#lp_forgotten_details_modal button'));
            window.alert("Please enter valid email");
        } else {
            cantRememberDialog.querySelector('p:last-child').style.display = "none";

            $.getJSON(
                "https://app.letsparticipate.com/api/ajax-participantSearch-wp.php?function=?",
                {
                    authToken: document.querySelector('#lp_auth_token').value,
                    email: email,
                    emailOrg: $('#emailOrganisers').val()
                },
                function (response) {
                    if (response.success) {
                        console.log("Email sent");
                        letspart_hideCantRememberDialog();
                        document.querySelector('#reminder-email-sent').style.display = "block";
                    } else {
                        console.log("Email not sent");
                        // Show the incorrect email dialog
                        cantRememberDialog.querySelector('p:last-child').style.display = "block";
                    }
                    letspart_enableBtn($('#lp_forgotten_details_modal button'));
                }
            );
        }
    });

// Get all fields...
    firstNameInput = document.querySelector('#billing_first_name');
    surnameInput = document.querySelector('#billing_last_name');
    emailInput = document.querySelector('#billing_email');
    phoneInput = document.querySelector('#billing_phone');
// countryInput = document.querySelector('#');
    townInput = document.querySelector('#billing_city');
    dobInput = document.querySelector('#lp_walkerDOB');
    genderInput = document.querySelector('#lp_walkerGender');
    nationalityInput = document.querySelector('#lp_walkerNationality');

// Hide the check details btn
    letspart_showConfirmDetailsBtn(false);

// Listeners to show or hide the check details button
    prevWalkerId.addEventListener('blur', function () {
        if (letspart_checkIdAndDobFilledIn()) {
            letspart_showConfirmDetailsBtn(true);
        } else {
            letspart_showConfirmDetailsBtn(false);
        }
    });
    prevWalkerDob.addEventListener('blur', function () {
        if (letspart_checkIdAndDobFilledIn()) {
            letspart_showConfirmDetailsBtn(true);
        } else {
            letspart_showConfirmDetailsBtn(false);
        }
    });
    prevWalkerId.addEventListener('input', function () {
        if (letspart_checkIdAndDobFilledIn()) {
            letspart_showConfirmDetailsBtn(true);
        } else {
            letspart_showConfirmDetailsBtn(false);
        }
    });
    prevWalkerDob.addEventListener('input', function () {
        if (letspart_checkIdAndDobFilledIn()) {
            letspart_showConfirmDetailsBtn(true);
        } else {
            letspart_showConfirmDetailsBtn(false);
        }
    });

    prevWalkerCheckDetailsBtn.addEventListener('click', function (e) {
        e.preventDefault();
        letspart_disableBtn($('#check-details'));
        letspart_fetchParticipant();
    });

    document.querySelector('#cant-remember-link').addEventListener('click', function (e) {
        letspart_showCantRememberDialog();
    })
})
;

function letspart_checkIdAndDobFilledIn() {
    if (
        prevWalkerId.value != null
        && prevWalkerId.value.length > 0
        && prevWalkerDob.value != null
        && prevWalkerDob.value.length > 0) {
        return true;
    }

    return false;
}

function letspart_showConfirmDetailsBtn(show) {
    if (show) {
        prevWalkerCheckDetailsBtn.style.display = 'inline-block';
    } else {
        prevWalkerCheckDetailsBtn.style.display = 'none';
    }
}

function letspart_fetchParticipant() {
    var id = prevWalkerId.value;
    var dob = prevWalkerDob.value;

    $.getJSON(
        "https://app.letsparticipate.com/api/ajax-participantSearch-wp.php?function=?",
        {
            authToken: document.querySelector('#lp_auth_token').value,
            id: id,
            dob: dob
        },
        function (response) {
            handleParticipantResponse(response, dob);
        }
    );
}

function handleParticipantResponse(response, dob) {
    console.log(response);

    if ($.isEmptyObject(response)) {
        // Participant not found
        console.log("Participant not found");

        letspart_showCantRememberDialog();
        letspart_enableBtn($('#check-details'));
    } else {
        // Participant found
        firstNameInput.value = response.forename;
        surnameInput.value = response.surname;
        emailInput.value = response.email;
        phoneInput.value = response.phone;
        townInput.value = response.town;
        dobInput.value = dob;
        genderInput.value = response.gender;
        nationalityInput.value = response.nationality;

        document.querySelector('#letspart_custom_checkout_field').style.display = "none";
        document.querySelector('#part-details-form').style.display = "block";
        letspart_enableBtn($('#check-details'));

        $('#place_order').show();
        $('#part-details-form').prepend("<h2>ID: " + response.event_participant_id + "</h2>")
    }
}

function letspart_showCantRememberDialog() {
    cantRememberDialog.style.display = 'flex';
}

function letspart_hideCantRememberDialog() {
    cantRememberDialog.style.display = "none";
}

function letspart_disableBtn(btn) {
    btn.attr('disabled', 'true');
    btn.append($('#spinner').clone().show());
}

function letspart_enableBtn(btn) {
    btn.removeAttr('disabled');
    btn.find('img').remove();
}