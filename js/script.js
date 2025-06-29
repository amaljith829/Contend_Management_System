$(".delete").on("click", function(e) {
  e.preventDefault();
  if(confirm("Are you sure?")) {
    var frm = $("<form>");
    frm.attr("action", $(this).attr("href"));
    frm.attr("method", "POST");
    frm.appendTo("body");
    frm.submit();
  }
});

$.validator.addMethod("dateTime", function(value, element) {
    return (value == "") || !isNaN(Date.parse(value));
}, "Please enter a valid date and time");

$("#form").validate({
    rules: {
        title: {
            required: true,
            minlength: 3
        },
        content: {
            required: true,
            minlength: 10
        },
        published_at: {
            dateTime: true
        }
    }
});

$("button.publish").on("click", function(e) {
    var id = $(this).data("id");
    $.ajax({
        url: "/admin/publish_article.php",
        method: "POST",
        data: { id: id },
        success: function(response) {
            location.reload();
        },
        error: function() {
            alert("Error publishing article");
        }
    });
});
