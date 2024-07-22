jQuery(document).ready(function ($) {
    $(".user-id").on('change',function () {
        var user_id = $(this).val();
        $.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action: 'get_appraisals_for_user',
                user_id: user_id
            },
            success: function(response) {
                if (response.success) {
                    $('#appraisal_id').empty().append(response.data);
                } else {
                    console.error("Error occurred:", response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error occurred:", error);
            }
        });
    });
});

jQuery(document).ready(function($) {
    $('.add_message_form').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('action', 'add_message');
        $.ajax({
            url: ajaxurl,
            type: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('.add_message_form')[0].reset();
                    $('.add_message_form .success').remove();
                    $('.add_message_form').prepend('<div class="success">' + response.data + '</div>');
                    setTimeout(function() {
                        $('.add_message_form .success').fadeOut('slow', function() {
                            $(this).remove();
                        });
                        var baseURL = window.location.origin + '/wp-admin/';
                        var newURL = baseURL + 'admin.php?page=aiaa_messages';
                        window.location.href = newURL;
                    }, 800);
                } else {
                    console.error("Error occurred:", response.data); // Show error message
                }
            },
            error: function(xhr, status, error) {
                console.error("Error occurred:", error);
            }
        });
    });
});

jQuery(document).ready(function ($) {
    $(".open").on('click',function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var url = $(this).attr('href');
        $.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action: 'update_vin_status',
                id: id
            },
            success: function(response) {
                console.log(response);
                window.location.href = url;
            },
        });
    });
});


jQuery(document).ready(function($) {
    $('.edit_message_form').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('action', 'edit_message');
        $.ajax({
            url: ajaxurl,
            type: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('.edit_message_form .success').remove();
                    $('.edit_message_form').prepend('<div class="success">' + response.data + '</div>');
                    setTimeout(function() {
                        $('.edit_message_form .success').fadeOut('slow', function() {
                            $(this).remove();
                        });
                        var baseURL = window.location.origin + '/wp-admin/';
                        var newURL = baseURL + 'admin.php?page=aiaa_messages';
                        window.location.href = newURL;
                    }, 800);
                } else {
                    console.error("Error occurred:", response.data); // Show error message
                }
            },
            error: function(xhr, status, error) {
                console.error("Error occurred:", error);
            }
        });
    });
});
