import './styles/datagrid.scss';
import './app';
import 'ublaboo-datagrid/assets/datagrid';
import 'ublaboo-datagrid/assets/datagrid-spinners';
import 'ublaboo-datagrid/assets/datagrid-instant-url-refresh';

document.addEventListener('DOMContentLoaded', function () {
	$('.table-responsive-stack').each(function (i) {
		var id = $(this).attr('id');
		$(this).find("thead tr:nth-child(2) th").each(function (i) {
			let theadValue = $(this).text();

			if ($(this).find('a').length > 0) {
				theadValue = $(this).find('a').first().text();
			}
			theadValue = $.trim(theadValue);
			console.log(theadValue);
			if(theadValue.length>0) {
				$('#' + id + ' td:nth-child(' + (i + 1) + ')').prepend('<span class="table-responsive-stack-thead">' + theadValue + ':</span> ');
			}else{
				$('#' + id + ' td:nth-child(' + (i + 1) + ')').prepend('<span class="table-responsive-stack-thead"></span> ');
			}
			$('.table-responsive-stack-thead').hide();

		});
	});

	$('.table-responsive-stack').each(function () {
		var thCount = $(this).find("th").length;
		var rowGrow = 100 / thCount + '%';
		$(this).find("th, td").css('flex-basis', rowGrow);
	});

	function flexTable() {
		if ($(window).width() < 768) {
			$(".table-responsive-stack").each(function (i) {
				$(this).find(".table-responsive-stack-thead").show();
				$(this).find('thead').hide();
			});
			$(".table-responsive-stack").addClass('table-is-responsive');
		} else {
			$(".table-responsive-stack").each(function (i) {
				$(this).find(".table-responsive-stack-thead").hide();
				$(this).find('thead').show();
			});
			$(".table-responsive-stack").removeClass('table-is-responsive');
		}
	}

	flexTable();

	window.onresize = function (event) {
		flexTable();
	};
});



