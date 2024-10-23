<?= $this->extend("layouts/default")?>
<?= $this->section("title")?>Forms<?= $this->endSection() ?>

<?= $this->section("content")?>
<style>
    .table-responsive {
        overflow-x: auto;
    }
    table.dataTable {
        width: 100%;
        clear: both;
        border-collapse: collapse;
        table-layout: auto;
    }
    table.dataTable thead th {
        text-align: center;
        font-weight: bold;
        vertical-align: middle; 
        white-space: pre;
    }
    table.dataTable tbody td {
        word-wrap: break-word;
        text-align: justify; 
        white-space: nowrap; 
    }

    table.dataTable tbody td:first-child {
        text-align: center;
    }
</style>

<br>
<div class="d-flex justify-content-between align-items-center">
    <h5 class="title"><?=$clang[$l="Mass Upload Customers"] ?? $l?></h5>
    <div class="d-flex">
        <a class="btn btn-secondary me-2" href="<?= url_to("customers-upload-form")?>">
            Reupload
        </a>
        <button id="save_button" class="btn btn-primary"  
            <?php 
                if (isset($isValid2) && isset($isValid3) && isset($isValid4)) {
                    echo (!($isValid && $isValid2 && $isValid3 && $isValid4)) ? 'disabled' : '';
                } elseif (isset($isValid2) && isset($isValid3)) {
                    echo !$isValid || !$isValid2 || !$isValid3 ? 'disabled' : '';
                } elseif (isset($isValid2)) {
                    echo !$isValid || !$isValid2 ? 'disabled' : '';
                } elseif (isset($isValid3)) {
                    echo !$isValid || !$isValid3 ? 'disabled' : '';
                } else {
                    echo !$isValid ? 'disabled' : '';
                }
            ?>
            type="submit" form="dataForm">
            Insert Data
        </button>
    </div>
</div>
<br>
<body>
    <div class="row">
        <div class="col-12">
            <form id="dataForm" action="<?= url_to("Customers\\Customers::insertCustomers") ?>" method="POST">
                <!-- First Table for Sheet 1 -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <h5><?= esc($sheetName1) ?></h5>
                            <table class="table table-bordered table-hover table-striped table-sm small w-100 display pb-30" id="myTable">
                                <thead>
                                    <tr>
                                        <th>Errors</th>
                                        <th>Cell Number</th>
                                        <?php foreach ($data[0] as $key => $value): ?>
                                            <?php if ($key != 'Cell Number' && $key != 'errors'): ?>
                                                <th><?= esc($key) ?></th>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data as $index => $row): ?>
                                        <tr <?php if (!empty($row['errors'])) echo 'class="error"'; ?>>
                                            <td>
                                                <?php if (!empty($row['errors'])): ?>
                                                    <ul>
                                                        <?php foreach ($row['errors'] as $error): ?>
                                                            <li><?= esc($error) ?></li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                    <input type="hidden" name="data[<?= $index ?>][errors]" value='<?= json_encode($row['errors']) ?>'>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= esc($row['Cell Number']) ?></td>
                                            <?php foreach ($row as $key => $value): ?>
                                                <?php if ($key != 'errors' && $key != 'Cell Number'): ?>
                                                    <td><?= esc($value) ?></td>
                                                    <input type="hidden" name="data[<?= $index ?>][<?= esc($key) ?>]" value="<?= esc($value) ?>">
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <input type="hidden" name="data[<?= $index ?>][Cell Number]" value="<?= esc($row['Cell Number']) ?>">
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Second Table for Sheet 2 -->
                <?php if (!empty($data2)): ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <h5><?= esc($sheetName2) ?></h5>
                                <table class="table table-bordered table-hover table-striped table-sm small w-100 display pb-30" id="myTable2">
                                    <thead>
                                        <tr>
                                            <th>Cell Number</th>
                                            <?php foreach ($data2[0] as $key => $value): ?>
                                                <?php if ($key != 'Cell Number' && $key != 'errors2'): ?>
                                                    <th><?= esc($key) ?></th>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <th>Errors</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data2 as $index => $row): ?>
                                            <tr <?php if (!empty($row['errors2'])) echo 'class="error"'; ?>>
                                                <td><?= esc($row['Cell Number']) ?></td>
                                                <?php foreach ($row as $key => $value): ?>
                                                    <?php if ($key != 'errors2' && $key != 'Cell Number'): ?>
                                                        <td><?= esc($value) ?></td>
                                                        <input type="hidden" name="data2[<?= $index ?>][<?= esc($key) ?>]" value="<?= esc($value) ?>">
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                                <td>
                                                    <?php if (!empty($row['errors2'])): ?>
                                                        <ul>
                                                            <?php foreach ($row['errors2'] as $error): ?>
                                                                <li><?= esc($error) ?></li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                        <input type="hidden" name="data2[<?= $index ?>][errors2]" value='<?= json_encode($row['errors2']) ?>'>
                                                    <?php endif; ?>
                                                </td>
                                                <input type="hidden" name="data2[<?= $index ?>][Cell Number]" value="<?= esc($row['Cell Number']) ?>">
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Third Table for Sheet 3 -->
                <?php if (!empty($data3)): ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <h5><?= esc($sheetName3) ?></h5>
                                <table class="table table-bordered table-hover table-striped table-sm small w-100 display pb-30" id="myTable3">
                                    <thead>
                                        <tr>
                                            <th>Cell Number</th>
                                            <?php foreach ($data3[0] as $key => $value): ?>
                                                <?php if ($key != 'Cell Number' && $key != 'errors3'): ?>
                                                    <th><?= esc($key) ?></th>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <th>Errors</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data3 as $index => $row): ?>
                                            <tr <?php if (!empty($row['errors3'])) echo 'class="error"'; ?>>
                                                <td><?= esc($row['Cell Number']) ?></td>
                                                <?php foreach ($row as $key => $value): ?>
                                                    <?php if ($key != 'errors3' && $key != 'Cell Number'): ?>
                                                        <td><?= esc($value) ?></td>
                                                        <input type="hidden" name="data3[<?= $index ?>][<?= esc($key) ?>]" value="<?= esc($value) ?>">
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                                <td>
                                                    <?php if (!empty($row['errors3'])): ?>
                                                        <ul>
                                                            <?php foreach ($row['errors3'] as $error): ?>
                                                                <li><?= esc($error) ?></li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                        <input type="hidden" name="data3[<?= $index ?>][errors3]" value='<?= json_encode($row['errors3']) ?>'>
                                                    <?php endif; ?>
                                                </td>
                                                <input type="hidden" name="data3[<?= $index ?>][Cell Number]" value="<?= esc($row['Cell Number']) ?>">
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Fourth Table for Sheet 4 -->
                <?php if (!empty($data4)): ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <h5><?= esc($sheetName4) ?></h5>
                                <table class="table table-bordered table-hover table-striped table-sm small w-100 display pb-30" id="myTable4">
                                    <thead>
                                        <tr>
                                            <th>Cell Number</th>
                                            <?php foreach ($data4[0] as $key => $value): ?>
                                                <?php if ($key != 'Cell Number' && $key != 'errors4'): ?>
                                                    <th><?= esc($key) ?></th>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <th>Errors</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data4 as $index => $row): ?>
                                            <tr <?php if (!empty($row['errors4'])) echo 'class="error"'; ?>>
                                                <td><?= esc($row['Cell Number']) ?></td>
                                                <?php foreach ($row as $key => $value): ?>
                                                    <?php if ($key != 'errors4' && $key != 'Cell Number'): ?>
                                                        <td><?= esc($value) ?></td>
                                                        <input type="hidden" name="data4[<?= $index ?>][<?= esc($key) ?>]" value="<?= esc($value) ?>">
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                                <td>
                                                    <?php if (!empty($row['errors4'])): ?>
                                                        <ul>
                                                            <?php foreach ($row['errors4'] as $error): ?>
                                                                <li><?= esc($error) ?></li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                        <input type="hidden" name="data4[<?= $index ?>][errors4]" value='<?= json_encode($row['errors4']) ?>'>
                                                    <?php endif; ?>
                                                </td>
                                                <input type="hidden" name="data4[<?= $index ?>][Cell Number]" value="<?= esc($row['Cell Number']) ?>">
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <input type="hidden" name="tableData" id="tableData">

                <br><br>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var table = $('#myTable').DataTable({
                "order": [[0,"desc"]],
                "createdRow": function (row, data, dataIndex) {
                    if (data[0] === null || data[0] === '') {
                        $('td', row).eq(0).css('background-color', 'green');
                    }
                }
            });
            let table2 = $('#myTable2').DataTable();
            let table3 = $('#myTable3').DataTable();
            let table4 = $('#myTable4').DataTable();

            // Handle form submission
            $('#dataForm').on('submit', function (e) {
                e.preventDefault(); // Prevent the default form submission

                // Collect data from the table
                var tableData = [];

                // Use DataTables API to get all data
                table.rows().every(function (rowIdx, tableLoop, rowLoop) {
                    var row = {};
                    var data = this.data();
                    $('#myTable thead th').each(function (index) {
                        var key = $(this).text();
                        var value = data[index];
                        row[key] = value;
                    });
                    tableData.push(row);
                });
                console.log(tableData);

                var tableData2 = [];
                // Use DataTables API to get all data from table2
                table2.rows({ search: 'applied' }).every(function (rowIdx, tableLoop, rowLoop) {
                    var row = {};
                    var data = this.data();
                    $('#myTable2 thead th').each(function (index) {
                        var key = $(this).text();
                        var value = data[index];
                        row[key] = value;
                    });
                    tableData2.push(row);
                });

                var tableData3 = [];
                // Use DataTables API to get all data from table3
                table3.rows({ search: 'applied' }).every(function (rowIdx, tableLoop, rowLoop) {
                    var row = {};
                    var data = this.data();
                    $('#myTable3 thead th').each(function (index) {
                        var key = $(this).text();
                        var value = data[index];
                        row[key] = value;
                    });
                    tableData3.push(row);
                });

                var tableData4 = [];
                // Use DataTables API to get all data
                table4.rows({ search: 'applied' }).every(function (rowIdx, tableLoop, rowLoop) {
                    var row = {};
                    var data = this.data();
                    $('#myTable4 thead th').each(function (index) {
                        var key = $(this).text();
                        var value = data[index];
                        row[key] = value;
                    });
                    tableData4.push(row);
                });

                // Set the hidden input value
                $('#tableData').val(JSON.stringify({table1: tableData, table2: tableData2, table3: tableData3, table4: tableData4}));

                // Submit the form
                this.submit();
                // console.log(tableData2);
            });
        });
    </script>
</body>

<?= $this->endSection() ?>