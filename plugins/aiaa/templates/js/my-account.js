jQuery(document).ready(function($){
    $(".del-appraisal").click(function(e){
        e.preventDefault();
        if(confirm("Are you sure you want to delete this appraisal?")){
            // Proceed with the deletion action
            var appraisalurl = $(this).attr("data-url");// Assuming you have an attribute for appraisal ID
            window.location.href = appraisalurl;
        }
    });
});


jQuery(document).ready(function($){
    $(".close-button").click(function(e){
        e.preventDefault();
        $('.modal').removeClass('show-modal');
    });
});
jQuery(document).ready(function($){
    $(".trigger").click(function(e){
        e.preventDefault();
        $('.modal .area').empty();
        var id = $(this).attr('data-id');
        var trig = $(this);
        var receiver_id = $(this).attr('data-receiver');
        var content = $(this).closest('tr').find('td[data-content]').data('content');
        var modalContent = '<h4>Message</h4>';
        modalContent += '<p>' + content + '</p>';
        modalContent += '<button class="button reply-btn" data-id="'+id+'" data-receiver="'+receiver_id+'">Reply</button>';
        $('.modal .area').html(modalContent);
        $('.modal').addClass('show-modal');
        $.ajax({
            url: ajax_object.ajaxurl,
            type: 'post',
            data: {
                action: 'update_status_message',
                parent_id: id,
                status: 'Read'
            },
            success: function(response) {
                console.log(response);
                trig.closest("tr").find("td:first-child").text("Read");
            },
        });
    });
    $(document).on('click', '.reply-btn', function(){
        $('.modal .area').empty();
        var paren_id = $(this).attr('data-id');
        var receiver_id = $(this).attr('data-receiver');
        var modalContent = '<h4>Reply</h4>';
        modalContent += '<textarea class="content"></textarea>';
        modalContent += '<button class="button send-btn" data-id="'+paren_id+'" data-receiver="'+receiver_id+'">Send</button>';
        $('.modal .area').html(modalContent);
        $.ajax({
            url: ajax_object.ajaxurl,
            type: 'post',
            data: {
                action: 'update_status_message',
                parent_id: paren_id,
                status: 'Read'
            },
            success: function(response) {
                console.log(response);
            },
        });
    });
    $(document).on('click', '.send-btn', function(){
        var parent_id = $(this).attr('data-id');
        var receiver_id = $(this).attr('data-receiver');
        var content = $(this).siblings('textarea.content').val();
        $.ajax({
            url: ajax_object.ajaxurl,
            type: 'post',
            data: {
                action: 'reply_message',
                parent_id: parent_id,
                receiver_id: receiver_id,
                content: content,
            },
            success: function(response) {
                console.log(response);
                $('.modal').removeClass('show-modal');
            },
        });

    });
});




