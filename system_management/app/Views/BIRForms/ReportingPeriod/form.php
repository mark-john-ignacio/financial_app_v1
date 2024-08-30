
<div class="mb-3">
                <label for="company_code" class="form-label">Company Code</label>
                <input type="text" class="form-control" id="company_code" name="company_code" required value="<?= old("company_code", esc($company->company_code)) ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="company_name" class="form-label">Company Name</label>
                <textarea class="form-control" id="company_name" name="company_name" required disabled><?= old("company_name", esc($company->company_name)) ?></textarea>    
            </div>
            <div class="mb-3">
                <label for="reporting_period" class="form-label">Reporting Period</label>
                <select class="form-select" id="reporting_period" name="reporting_period">
                    <option value="fiscal" <?= old('reporting_period',esc($company->reporting_period ?? "")) == "fiscal" ? 'selected' : "" ?>>Fiscal</option>
                    <option value="calendar" <?= old('reporting_period',esc($company->reporting_period ?? "")) == "calendar" ? 'selected' : "" ?>>Calendar</option>
                </select>
            </div>

            <div class="mb-3" id="fiscal_month_container">
                <label for="company_name" class="form-label">Fiscal Month</label>
                <select class="form-select" id="fiscal_month" name="fiscal_month">
                    <option value="01" <?= old('fiscal_month', esc($company->fiscal_month ?? "")) == "01" ? 'selected' : "" ?>>January</option>
                    <option value="02" <?= old('fiscal_month', esc($company->fiscal_month ?? "")) == "02" ? 'selected' : "" ?>>February</option>
                    <option value="03" <?= old('fiscal_month', esc($company->fiscal_month ?? "")) == "03" ? 'selected' : "" ?>>March</option>
                    <option value="04" <?= old('fiscal_month', esc($company->fiscal_month ?? "")) == "04" ? 'selected' : "" ?>>April</option>
                    <option value="05" <?= old('fiscal_month', esc($company->fiscal_month ?? "")) == "05" ? 'selected' : "" ?>>May</option>
                    <option value="06" <?= old('fiscal_month', esc($company->fiscal_month ?? "")) == "06" ? 'selected' : "" ?>>June</option>
                    <option value="07" <?= old('fiscal_month', esc($company->fiscal_month ?? "")) == "07" ? 'selected' : "" ?>>July</option>
                    <option value="8" <?= old('fiscal_month', esc($company->fiscal_month ?? "")) == "08" ? 'selected' : "" ?>>August</option>
                    <option value="09" <?= old('fiscal_month', esc($company->fiscal_month ?? "")) == "09" ? 'selected' : "" ?>>September</option>
                    <option value="10" <?= old('fiscal_month', esc($company->fiscal_month ?? "")) == "10" ? 'selected' : "" ?>>October</option>
                    <option value="11" <?= old('fiscal_month', esc($company->fiscal_month ?? "")) == "11" ? 'selected' : "" ?>>November</option>
                    <option value="12" <?= old('fiscal_month', esc($company->fiscal_month ?? "")) == "12" ? 'selected' : "" ?>>December</option>
                </select>                   
            </div>
            

<script>
    $(document).ready(function() {
        function toggleFiscalMonth(){
            const reportingPeriod = $("#reporting_period").val();
            if (reportingPeriod === "calendar") {
                $("#fiscal_month_container").hide();
                $("#fiscal_month").val("");
            } else {
                $("#fiscal_month_container").show();
                $("#fiscal_month").val("01");
            }
        }
        toggleFiscalMonth();
        $("#reporting_period").change(function(){
            toggleFiscalMonth();
        });

    });
</script>