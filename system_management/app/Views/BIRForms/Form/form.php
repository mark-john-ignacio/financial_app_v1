
            <div class="mb-3">
                <label for="form_code" class="form-label">Form Code</label>
                <input type="text" class="form-control" id="form_code" name="form_code" required value="<?= old("form_code", esc($form->form_code)) ?>">
            </div>
            <div class="mb-3">
                <label for="form_name" class="form-label">Form Name</label>
                <textarea class="form-control" id="form_name" name="form_name" required><?= old("form_name", esc($form->form_name)) ?></textarea>    
            </div>
            <div class="mb-3">
                <label for="filter" class="form-label">Form Filter</label>
                <select class="form-select" id="filter" name="filter">
                    <option value="Annual" <?= old('filter',esc($form->filter ?? "")) == "Annual" ? 'selected' : "" ?>>Annual</option>
                    <option value="Quarterly" <?= old('filter',esc($form->filter ?? "")) == "Quarterly" ? 'selected' : "" ?>>Quarterly</option>
                    <option value="Monthly" <?= old('filter',esc($form->filter ?? "")) == "Monthly" ? 'selected' : "" ?>>Monthly</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="cstatus" class="form-label">Status</label>
                <select class="form-select" id="cstatus" name="cstatus" required>
                    <option value="ACTIVE" <?= old('cstatus',esc($form->cstatus ?? "")) == "ACTIVE" ? 'selected' : "" ?>>Active</option>
                    <option value="INACTIVE" <?= old('cstatus',esc($form->cstatus ?? "")) == "INACTIVE" ? 'selected' : "" ?>>Inactive</option>
                </select>
            </div>
            <!-- <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" class="form-control" id="image" name="image" required>
            </div> -->
            

