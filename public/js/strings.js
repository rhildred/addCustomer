$(".editable").click(function (e) {
	$(".save").hide();
	$(e.target).next().show();    
    e.stopPropagation();
});
 
$(document).click(function() {
    $(".save").hide();
});

$(".save").click(function (e) {
	var editable = $(e.target).prev();
    var content = editable.html();
        $.ajax({
            url: './strings.php',
            type: 'POST',
            data: {
            content: content, 
            text_id: editable.attr('name')            
            },
            success:function (data) {
                if (data == '1')
                {
                    
                }
                else
                {
                    
                }
            }
        });
    });