// Document Ready
    $(document).ready(function() {
        $().SmoothAnchors();

        // VC table change events
        $(function () {
            var get_current_dates = $('select.table-select-select option:nth-child(2)').val();
            $('select.table-select-select').val(get_current_dates).change();
        });
        $( 'select[name="vc_table_date_range"]' ).change( function() {
			var selectedKey = $(this).val();
			var parentElm = $(this).closest('.vc_tta-panel-body');
			$('.vc-toggle-wrapper', parentElm).addClass('hidden');
			$('.vc-toggle-wrapper.table-' + selectedKey, parentElm).removeClass('hidden');
        });
    });
