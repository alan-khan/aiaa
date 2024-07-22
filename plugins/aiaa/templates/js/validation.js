jQuery(document).ready(function ($) {
  $("#startAppraisalValidation").validate({
    rules: {
      vtype: "required",
      vin: "required",
    },
    messages: {
      vtype: 'The "Vehicle Type" field is required.',
      vin: 'The "Vehicle Vin" field is required.',
    },
    errorElement: "div",
    errorLabelContainer: ".alert",
    success: function (label) {},
    submitHandler: function (form) {
      $(".loader").show();
      form.submit();
    },
    showErrors: function (errorMap, errorList) {
      this.defaultShowErrors();
      if (errorList.length) {
        $("html, body").animate(
          {
            scrollTop: $(".alert-danger").offset().top,
          },
          500
        );
      }
    },
  });
});
jQuery(document).ready(function ($) {
  $("#registerValidation").validate({
    rules: {
      firstname: "required",
      lastname: "required",
      username: "required",
      email: "required",
      password: "required",
      phone: "required",
      company: "required",
      address: "required",
      city: "required",
      state: "required",
      zip: "required",
    },
    messages: {
      firstname: "The First Name field is required.",
      lastname: "The Last Name field is required.",
      username: "The User Name field is required.",
      email: "The Email field is required.",
      password: "The Password field is required.",
      phone: "The Phone field is required.",
      company: "The Company field is required.",
      address: "The Address field is required.",
      city: "The City field is required.",
      state: "The State field is required.",
      zip: "The Zip field is required.",
    },
    errorElement: "div",
    errorLabelContainer: ".alert",
    success: function (label) {},
    submitHandler: function (form) {
      var datastring = $("#registerValidation").serialize();
      $(".loader").show();
      form.submit();
    },
    showErrors: function (errorMap, errorList) {
      this.defaultShowErrors();
      if (errorList.length) {
        $("html, body").animate(
          {
            scrollTop: $(".alert-danger").offset().top,
          },
          500
        );
      }
    },
  });
});


function checkMSLandUnknownField() {
  if ($('#priorNadaTradeInValueMSL').is(':checked') || $('#priorNadaTradeInValueUnknown').is(':checked')) {
    alert('TRUE');
    return false;
  } else {
    alert('FALSE');
    return true;
  }
}


jQuery(document).ready(function ($) {
  $(".new_apraisal").on("click", function () {
    var submitButtonValue = $(this).val();
    if (
      submitButtonValue === "Submit Appraisal" ||
      submitButtonValue === "Update Appraisal"
    ) {
      $("#newAppraisalValidation").validate().destroy();
      $("#newAppraisalValidation").validate({
        rules: {
          make: "required",
          model: "required",
          vyear: "required",
          body: "required",
          vehicleType: "required",
          color: "required",
          hasKeys: "required",
          odometer: "required",
          runningStatus: "required",
          front: {
            required: true,
            extension: "jpeg|png|pdf|jpg",
          },
          rear: {
            required: true,
            extension: "jpeg|png|pdf|jpg",
          },
          passenger: {
            required: true,
            extension: "jpeg|png|pdf|jpg",
          },
          driver: {
            required: true,
            extension: "jpeg|png|pdf|jpg",
          },
          interior: {
            extension: "jpeg|png|pdf|jpg",
          },

        },
        messages: {
          make: 'The "Make" field is required.',
          model: 'The "Model" field is required.',
          vyear: 'The "Year" field is required.',
          body: 'The "Body" field is required.',
          vehicleType: 'The "Vehicle Type" field is required.',
          color: 'The "Exterior Color" field is required.',
          hasKeys: 'The "Do you have keys to the vehicle?" field is required.',
          odometer: 'The "Vehicle Mileage" field is required.',
          runningStatus: 'The "Run Status" field is required.',
          front:
            'The "Front Photo" field is required and must be a JPEG, PNG, PDF or JPG image.',
          rear: 'The "Rear Photo" field is required and must be a JPEG, PNG, PDF or JPG image.',
          passenger:
            'The "Passenger Photo" field is required and must be a JPEG, PNG, PDF or JPG image.',
          driver:
            'The "Driver Photo" field is required and must be a JPEG, PNG, PDF or JPG image.',
          interior: 'The "Interior Photo" field must be a JPEG, PNG, PDF or JPG image.',

        },
        errorElement: "div",
        errorLabelContainer: ".alert",
        success: function (label) {},
        submitHandler: function (form) {
          var datastring = $("#newAppraisalValidation").serialize();
          $(".loader").show();
          form.submit();
        },
        showErrors: function (errorMap, errorList) {
          this.defaultShowErrors();
          if (errorList.length) {
            $("html, body").animate(
              {
                scrollTop: $(".alert-danger").offset().top,
              },
              500
            );
          }
        },
      });
    } else {
      $("#newAppraisalValidation").validate().destroy();
      $("#newAppraisalValidation").validate({
        rules: {
          front: {
            extension: "jpeg|png|pdf|jpg",
          },
          rear: {
            extension: "jpeg|png|pdf|jpg",
          },
          passenger: {
            extension: "jpeg|png|pdf|jpg",
          },
          driver: {
            extension: "jpeg|png|pdf|jpg",
          },
          interior: {
            extension: "jpeg|png|pdf|jpg",
          },

        },
        messages: {
          front: 'The "Front Photo" field must be a JPEG, PNG, PDF or JPG image.',
          rear: 'The "Rear Photo" field must be a JPEG, PNG, PDF or JPG image.',
          passenger:
            'The "Passenger Photo" field must be a JPEG, PNG, PDF or JPG image.',
          driver: 'The "Driver Photo" field must be a JPEG, PNG, PDF or JPG image.',
          interior: 'The "Interior Photo" field must be a JPEG, PNG, PDF or JPG image.',
        },
        errorElement: "div",
        errorLabelContainer: ".alert",
        success: function (label) {},
        submitHandler: function (form) {
          $(".loader").show();
          form.submit();
        },
        showErrors: function (errorMap, errorList) {
          this.defaultShowErrors();
          if (errorList.length) {
            $("html, body").animate(
              {
                scrollTop: $(".alert-danger").offset().top,
              },
              500
            );
          }
        },
      });
    }
  });
});

jQuery(document).ready(function ($) {

  function imageExist(elementId){
    if(jQuery(elementId).val() !== null && jQuery(elementId).val() !== undefined && jQuery(elementId).val() !== "") {
      return false;
    }else{
      return true;
    } 
  }

  $(".data_submit").on("click", function () {
    var submitButtonValue = $(this).val();
    if (
      submitButtonValue === "Submit Appraisal" ||
      submitButtonValue === "Update Appraisal"
    ) {
     
      var front = imageExist("#front");
      var rear = imageExist("#rear");
      var driver =  imageExist("#driver");
      var passenger = imageExist("#passenger");
      var top = imageExist("#top");

      $("#editAppraisalValidation").validate().destroy();
      $("#editAppraisalValidation").validate({
        rules: {
          make: "required",
          model: "required",
          vyear: "required",
          body: "required",
          vehicleType: "required",
          color: "required",
          priorNadaTradeInValue: "required",
          hasKeys: "required",
          odometer: "required",
          runningStatus: "required",
          front: {
            required: front,
            extension: "jpeg|png|pdf|jpg",
          },
          rear: {
            required: rear,
            extension: "jpeg|png|pdf|jpg",
          },
          passenger: {
            required: passenger,
            extension: "jpeg|png|pdf|jpg",
          },
          driver: {
            required: driver,
            extension: "jpeg|png|pdf|jpg",
          },
          interior: {
            extension: "jpeg|png|pdf|jpg",
          },

          other: {
            required: false,
            extension: "jpeg|png|pdf|jpg",
          },
        },
        messages: {
          make: 'The "Make" field is required.',
          model: 'The "Model" field is required.',
          vyear: 'The "Year" field is required.',
          body: 'The "Body" field is required.',
          vehicleType: 'The "Vehicle Type" field is required.',
          color: 'The "Exterior Color" field is required.',
          priorNadaTradeInValue: 'The "NADA Loan Value" field is required.',
          hasKeys: 'The "Do you have keys to the vehicle" field is required.',
          odometer: 'The "Vehicle Mileage" field is required.',
          runningStatus: 'The "Run Status" field is required.',
          front:
            'The "Front Photo" field is required and must be a JPEG, PNG, PDF or JPG image.',
          rear: 'The "Rear Photo" field is required and must be a JPEG, PNG, PDF or JPG image.',
          passenger:
            'The "Passenger Photo" field is required and must be a JPEG, PNG, PDF or JPG image.',
          driver:
            'The "Driver Photo" field is required and must be a JPEG, PNG, PDF or JPG image.',
          interior:
            'The "Interior Photo"  must be a JPEG, PNG, PDF or JPG image.',
          other:
              'The "Other" field must be a JPEG, PNG, PDF or JPG image.',
        },
        errorElement: "div",
        errorLabelContainer: ".alert",
        success: function (label) {},
        submitHandler: function (form) {
          $(".loader").show();
          form.submit();
        },
        showErrors: function (errorMap, errorList) {
          this.defaultShowErrors();
          if (errorList.length) {
            $("html, body").animate(
              {
                scrollTop: $(".alert-danger").offset().top,
              },
              500
            );
          }
        },
      });
    } else {
      $("#editAppraisalValidation").validate().destroy();
      $("#editAppraisalValidation").validate({
        rules: {
          front: {
            extension: "jpeg|png|pdf|jpg",
          },
          rear: {
            extension: "jpeg|png|pdf|jpg",
          },
          passenger: {
            extension: "jpeg|png|pdf|jpg",
          },
          driver: {
            extension: "jpeg|png|pdf|jpg",
          },
          top: {
            extension: "jpeg|png|pdf|jpg",
          },
          interior: {
            extension: "jpeg|png|pdf|jpg",
          },

        },
        messages: {
          front: 'The "Front Photo" field must be a JPEG, PNG, PDF or JPG image.',
          rear: 'The "Rear Photo" field must be a JPEG, PNG, PDF or JPG image.',
          passenger:
            'The "Passenger Photo" field must be a JPEG, PNG, PDF or JPG image.',
          driver: 'The "Driver Photo" field must be a JPEG, PNG, PDF or JPG image.',
          interior: 'The "Interior Photo" field must be a JPEG, PNG, PDF or JPG image.',

        },
        errorElement: "div",
        errorLabelContainer: ".alert",
        success: function (label) {},
        submitHandler: function (form) {
          $(".loader").show();
          form.submit();
        },
        showErrors: function (errorMap, errorList) {
          this.defaultShowErrors();
          if (errorList.length) {
            $("html, body").animate(
              {
                scrollTop: $(".alert-danger").offset().top,
              },
              500
            );
          }
        },
      });
    }
  });
});

jQuery(document).ready(function ($) {
  // NADA Loan Value
  $("#info-icon").mouseover(function () {
    $("#tooltip").show();
  });

  $("#info-icon").mouseout(function () {
    $("#tooltip").hide();
  });

  // Front Photo
  $("#photo-front").mouseover(function () {
    $("#tooltip-photo-front").show();
  });

  $("#photo-front").mouseout(function () {
    $("#tooltip-photo-front").hide();
  });

  // Rear Photo
  $("#photo-rear").mouseover(function () {
    $("#tooltip-photo-rear").show();
  });

  $("#photo-rear").mouseout(function () {
    $("#tooltip-photo-rear").hide();
  });

  // Passenger Photo
  $("#photo-passenger").mouseover(function () {
    $("#tooltip-photo-passenger").show();
  });

  $("#photo-passenger").mouseout(function () {
    $("#tooltip-photo-passenger").hide();
  });

  // Driver Photo
  $("#photo-driver").mouseover(function () {
    $("#tooltip-photo-driver").show();
  });

  $("#photo-driver").mouseout(function () {
    $("#tooltip-photo-driver").hide();
  });

  // Other Photo
  $("#photo-other").mouseover(function () {
    $("#tooltip-photo-other").show();
  });

  $("#photo-other").mouseout(function () {
    $("#tooltip-photo-other").hide();
  });

  $("#photo-interior").mouseover(function () {
    $("#tooltip-photo-interior").show();
  });

  $("#photo-interior").mouseout(function () {
    $("#tooltip-photo-interior").hide();
  });
});
