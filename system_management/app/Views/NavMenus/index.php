<?= $this->extend('layouts/default') ?>
<?= $this->section('title') ?>Nav Menus<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <h1>Nav Menus</h1>
    <table id="navMenusTable" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Menu ID</th>
                <th>Menu Name</th>
                <th>Menu URL</th>
                <th>Status</th>
                <th>Toggle</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
    <a href="<?= site_url('/nav-menus/create') ?>" class="btn btn-primary">Add Menu</a>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        var tableId = '#navMenusTable';
        var table = $(tableId).DataTable({
            ajax: {
                url: '<?= site_url('nav-menus/get-menus') ?>',
                dataSrc: '',
                data: function(d){
                    d.status = $('#statusFilter').val();
                },
                error: function (xhr, error, thrown) {
                    console.error("Error occurred during AJAX request:", error, thrown);
                    console.error("Status:", xhr.status);
                }
            },
            columns: [
                { data: 'id' },
                { data: 'title' },
                { data: 'url' },
                { data: 'status'},
                { data: null, render: function(data, type, row) {
                    if (data.status === 'ACTIVE') {
                        var actionHTML = `<div style='display: flex; justify-content: center; align-items: center;'><a href='#'><i class='fa-solid fa-toggle-on fa-lg m-2' id='refreshButton'></i></a></div>`;
                        return actionHTML;
                    } else if (data.status === 'INACTIVE') {
                        var actionHTML = `<div style='display: flex; justify-content: center; align-items: center;'><a href='#'><i class='fa-solid fa-toggle-off fa-lg m-2' id='refreshButton'></i></a></div>`;
                        return actionHTML;
                    }
                }}
            ],
            createdRow: function(row, data, dataIndex) {
                // Get the 'cstatus' cell
                var statusColumnIndex = 3; // replace with the actual index of the 'cstatus' column
                var activeColor = "#38CB89";
                var inactiveColor = "#FC7303";
                formatStatusCell(row, data, statusColumnIndex, activeColor, inactiveColor);

                $(row).attr('data-id', data.id);
                $(row).attr('data-status', data.status);
            },
            initComplete: function () {
                createStatusFilter(table, tableId, 'statusFilter');
            },
            columnDefs: [{ orderable: false, targets: [3, 4] }]
        });

        toggleStatusAndRedraw(table, '<?= site_url('nav-menus/toggle-status') ?>');
    });

    function createStatusFilter(table, tableId, statusFilterId) {
        var select = $('<select id="' + statusFilterId + '" class="form-select form-select-sm"><option value="">All Statuses</option></select>')
            .css({
                'width': '150px',
                'display': 'inline-block',
                'margin-right': '10px'
            })
            .prependTo($('.dt-search')) // Changed this line to prepend to the .dt-search div
            .on('change', function () {
                var val = $.fn.dataTable.util.escapeRegex(
                    $(this).val()
                );
                // Assuming the status column is at index 3
                table.column(3).search(val ? '^' + val + '$' : '', true, false).draw();
            });
        $(tableId + '_filter label').css('display', 'inline-block');
        // Add options for 'ACTIVE' and 'INACTIVE'
        select.append('<option value="ACTIVE">ACTIVE</option>');
        select.append('<option value="INACTIVE">INACTIVE</option>');
    }
    function formatStatusCell(row, data, statusColumnIndex, activeColor, inactiveColor) {
        var statusCell = $(row).find('td').eq(statusColumnIndex);
        var statusHTML;
         

        if (data.status === 'ACTIVE') {
            statusHTML = `<span style="background-color: ${activeColor}; color: #FFFFFF; padding: 5px 10px; border-radius: 5px;">${data.status}</span>`;
        } else if (data.status === 'INACTIVE') {
            statusHTML = `<span style="background-color: ${inactiveColor}; color: #FFFFFF; padding: 5px 10px; border-radius: 5px;">${data.status}</span>`;
        }
        statusCell.html(statusHTML);
    }

    function toggleStatusAndRedraw(table, ajaxUrl) {
        $(document).on('click', '#refreshButton', function(e) {
            e.preventDefault();

            var rowId = $(this).closest('tr').data('id');
            var row = $(this).closest('tr');
            var newStatus = $(this).closest('tr').data('status') === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE';
            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    id: rowId,
                    status: newStatus
                },
                success: function(response) {
                    // Redraw the datatable
                    row.data('status', newStatus);
                    table.ajax.reload(null, false);

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error toggling status:', textStatus, errorThrown, jqXHR);
                }
            });
        });

    }


</script>
<?= $this->endSection() ?>