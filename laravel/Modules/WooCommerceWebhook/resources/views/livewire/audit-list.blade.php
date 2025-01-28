<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">WooCommerce Audits</h3>
        </div>
        <div class="card-body">
            <div wire:ignore>
                <table id="audit-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Status</th>
                            <th>Error Message</th>
                            <th>Request Data</th>
                            <th>Response Data</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Audit Detail Modal -->
    <div wire:ignore.self class="modal fade" id="auditDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Audit Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <h6>Request Data</h6>
                        <pre id="modalRequestData" class="bg-light p-3"></pre>
                    </div>
                    <div class="mb-3">
                        <h6>Response Data</h6>
                        <pre id="modalResponseData" class="bg-light p-3"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function truncateJson(data) {
                const str = JSON.stringify(data);
                return str.length > 50 ? str.substring(0, 50) + '...' : str;
            }

            function showAuditDetail(rowData) {
                try {
                    const requestData = JSON.stringify(JSON.parse(rowData.request_data), null, 2);
                    const responseData = JSON.stringify(JSON.parse(rowData.response_data), null, 2);
                    
                    document.getElementById('modalRequestData').textContent = requestData;
                    document.getElementById('modalResponseData').textContent = responseData;
                    
                    new bootstrap.Modal(document.getElementById('auditDetailModal')).show();
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                }
            }

            $(document).ready(function() {
                $('#audit-table').DataTable({
                    serverSide: true,
                    ajax: '{{ route("api.woocommerce.audits.data") }}',
                    columns: [
                        {data: 'id'},
                        {data: 'status'},
                        {data: 'error_message'},
                        {
                            data: 'request_data',
                            render: function(data) {
                                return `<pre class="text-truncate" style="max-width: 200px;">${truncateJson(data)}</pre>`;
                            }
                        },
                        {
                            data: 'response_data',
                            render: function(data) {
                                return `<pre class="text-truncate" style="max-width: 200px;">${truncateJson(data)}</pre>`;
                            }
                        },
                        {
                            data: 'created_at',
                            render: function(data) {
                                return moment(data).format('YYYY-MM-DD HH:mm:ss');
                            }
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                return `<button onclick='showAuditDetail(${JSON.stringify(row)})' 
                                        class="btn btn-sm btn-primary">
                                        View Details
                                        </button>`;
                            }
                        }
                    ]
                });
            });
        </script>
@endpush
</div>