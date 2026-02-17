// del
$("#modalDel").on("show.bs.modal", function (event) {
	var url = $(event.relatedTarget).data("url");
	$("#del_url").attr("action", url);
	$("#del_id").val($(event.relatedTarget).data("id"));
});
