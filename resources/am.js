jQuery(document).ready(function($){
	$('.am-date').datepicker({
		dateFormat: "mm/dd/y",
		showOtherMonths : true
	});

	$('.am-date').focus(function(){
		$('#am-filter-type-week').prop('checked',false);
		$('#am-filter-type-date').prop('checked', true);
	});

	$('#am-week').focus(function(){
		$('#am-filter-type-date').prop('checked',false);
		$('#am-filter-type-week').prop('checked', true);
	});	

	$('.am-show-author-posts').click(function(e){
		e.preventDefault();
		$(this).siblings('.author-posts').toggle();
		$(this).text($(this).text() == 'Hide' ? 'Show' : 'Hide');
	});

	$('#am-results-table').tablesorter({
		headerTemplate: '{content} {icon}'
	});

	$('#am-form').submit(function(e){
		e.preventDefault();
		if ($('#am-filter-type-date').attr('checked') == 'checked' ){
			$('#am-start-date, #am-end-date').each(function(){
				var el = $(this);
				if (!el.val()){
					el.addClass('am-bad-field');
				}else{
					el.removeClass('am-bad-field');	
				}
			});
			if ($('.am-bad-field').length == 0){
				$('#am-form').unbind('submit').submit();	
			}
		}else{
			$('#am-form').unbind('submit').submit();	
		}
	});
});