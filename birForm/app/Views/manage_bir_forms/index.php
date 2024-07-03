<?= $this->extend("layouts/default")?>
<?= $this->section("title")?>Pin Verify<?= $this->endSection() ?>

<?= $this->section("content")?>
<div id="app">
    <div class="container mt-5">
        <h1>Associate Forms with Years</h1>
        <div class="mb-3">
            <label for="year" class="form-label">Year:</label>
            <select v-model="selectedYear" @change="fetchForms" class="form-select">
                <option v-for="year in years" :value="year">{{ year }}</option>
            </select>
        </div>
        <div class="table-responsive" v-if="forms.length">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 20px;"></th>
                        <th>Form Code</th>
                        <th>Form Name</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="form in forms">
                        <td>
                            <input type="checkbox" :value="form.form_code" class="form-check-input">
                        </td>
                        <td>{{ form.form_code }}</td>
                        <td>{{ form.form_name }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const show_link = '<?= site_url('manage-bir-forms/show') ?>';
new Vue({
    el: '#app',
    data: {
        years: <?= json_encode($years) ?>,
        forms: [],
        selectedYear: '',
    },
    methods: {
        fetchForms() {
            if (!this.selectedYear) return;
            axios.post(show_link, { year_id: this.selectedYear })
                .then(response => {
                    this.forms = response.data.registered_forms;
                })
                .catch(error => {
                    console.error("There was an error fetching the forms: ", error);
                });
        }
    }
});
</script>
<?= $this->endSection()?>