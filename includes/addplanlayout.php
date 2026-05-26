<?php if (isset($_GET['err'])&&$_GET['err']==='missing'): ?><div class="alert alert-danger">Plan name and Aaraji number are required.</div><?php endif; ?>
    <?php if ($isDeveloperForm): ?><div class="alert alert-info" style="font-size:.8rem">Your plan will be submitted for admin approval before appearing publicly.</div><?php endif; ?>

    <div class="card" style="max-width:820px">
      <div class="card-header">
        <h3><?= $page==='edit'?'Edit Plan: '.e($editPlan['plan_name']??''):($isDeveloperForm?'Submit Developer Plan':'Register New Plan') ?></h3>
        <a href="index.php" class="btn btn-ghost btn-sm">Back</a>
      </div>
      <div class="card-body">
        <form method="POST" action="index.php" enctype="multipart/form-data">
          <input type="hidden" name="action" value="save_plan">
          <input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
          <?php if ($page==='edit'): ?><input type="hidden" name="plan_id" value="<?= (int)$editPlan['id'] ?>"><?php endif; ?>
          <?php if ($isDeveloperForm): ?><input type="hidden" name="is_developer_plan" value="1"><?php endif; ?>
          <?php if (is_admin()): ?>
          <div class="form-field" style="margin-bottom:.9rem">
            <label>Plan Type</label>
            <select class="input" name="is_developer_plan" onchange="toggleDevFields(this.value)" style="width:auto;min-width:200px">
              <option value="0" <?= (!$editIsDev)?'selected':'' ?>>Admin / Aaraji Plan</option>
              <option value="1" <?= $editIsDev?'selected':'' ?>>Developer Plan</option>
            </select>
          </div>
          <?php endif; ?>

          <div class="section-heading">Plan Details</div>
          <div class="form-grid">
            <div class="form-field"><label>Plan Name <span class="req">*</span></label><input class="input" type="text" name="plan_name" required placeholder="e.g. Green Valley Plot A" value="<?= e($editPlan['plan_name']??'') ?>"></div>
            <div class="form-field"><label>Aaraji Number <span class="req">*</span></label><input class="input" type="text" name="aaraji_number" required placeholder="e.g. ARJ/2024/0012" value="<?= e($editPlan['aaraji_number']??'') ?>"></div>
            <div class="form-field"><label>Revenue Village</label>
              <select class="input" name="village_id"><option value="">-- Select --</option>
                <?php foreach ($villagesAll as $v): ?><option value="<?= $v['id'] ?>" <?= (($editPlan['village_id']??0)==$v['id'])?'selected':'' ?>><?= e($v['name']) ?><?= $v['tehsil']?' - '.e($v['tehsil']):'' ?></option><?php endforeach; ?>
              </select>
            </div>
            <!-- Developer contact (shown for dev forms) -->
            <div class="form-field dev-field" id="contactField" style="<?= (!$isDeveloperForm&&!$editIsDev)?'display:none':'' ?>">
              <label>Contact Number</label><input class="input" type="text" name="contact_number" placeholder="e.g. +91 98765 43210" value="<?= e($editPlan['contact_number']??'') ?>">
            </div>
            <div class="form-field fg-full">
              <label>Google Maps Location URL</label>
              <input class="input" type="url" name="google_location" id="locInput" placeholder="Paste Google Maps link..." value="<?= e($editPlan['google_location']??'') ?>" style="padding-right:44px" oninput="previewLoc()">
            </div>
          </div>

          <div class="section-heading">Plan Files</div>
          <div class="form-grid">
            <div class="form-field">
              <label>Plan Image or PDF</label>
              <div class="upload-zone" id="uploadZone"><input type="file" name="plan_file" accept="image/*,.pdf" id="fileInput"><div style="font-size:1.5rem;margin-bottom:.4rem">[FILE]</div><p>Click to upload</p><small>JPG, PNG, WEBP, PDF - Max <?= MAX_FILE_MB ?>MB</small></div>
              <div class="upload-preview" id="uploadPreview"><span id="prevIcon">[F]</span><span id="prevName"></span><button type="button" onclick="clearUpload()" style="margin-left:auto;background:none;border:none;color:var(--red);cursor:pointer;font-weight:700">X</button></div>
              <div id="filePreviewBox" style="display:none;margin-top:10px;border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden;background:var(--surface2)"><img id="filePreviewImg" src="" alt="" style="width:100%;max-height:240px;object-fit:contain;display:none"><div id="filePreviewPdf" style="display:none;padding:1.4rem;text-align:center"><div style="font-size:2.5rem;margin-bottom:.4rem">[PDF]</div><div id="filePreviewPdfName" style="font-size:.82rem;color:var(--t2)"></div></div></div>
              <?php if (!empty($editPlan['file_name'])): ?><p style="margin-top:6px;font-size:.72rem;color:var(--t3)">Current: <strong><?= e($editPlan['file_name']) ?></strong></p><?php if ($editPlan['file_type']==='image'&&$editPlan['file_path']): ?><div style="margin-top:7px;border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden"><img src="<?= e($editPlan['file_path']) ?>" alt="" style="width:100%;max-height:200px;object-fit:contain;background:var(--surface2)"></div><?php endif; endif; ?>
            </div>
            <div class="form-field dev-field" id="approvedMapField" style="<?= (!$isDeveloperForm&&!$editIsDev)?'display:none':'' ?>">
              <label>Approved Plan Map <span style="font-size:.65rem;font-weight:400;color:var(--t4);text-transform:none;letter-spacing:0">(from authority)</span></label>
              <div class="upload-zone"><input type="file" name="approved_map" accept="image/*,.pdf"><div style="font-size:2.2rem;margin-bottom:.4rem;color:var(--green)"><i class="bx bx-map-alt"></i></div><p style="font-size:.78rem">Upload government approved map</p><small>Max <?= MAX_FILE_MB ?>MB</small></div>
              <?php if (!empty($editPlan['approved_map_name'])): ?><p style="margin-top:5px;font-size:.72rem;color:var(--t3)">Current: <strong><?= e($editPlan['approved_map_name']) ?></strong></p><?php endif; ?>
            </div>
          </div>

          <!-- Chain documents (admin plans) -->
          <?php if (!$isDeveloperForm): ?>
          <div class="form-field">
            <label>Chain Documents <span style="font-weight:400;font-size:.68rem;color:var(--t3);text-transform:none;letter-spacing:0">(PDF or multiple images)</span></label>
            <?php if (!empty($chainDocs)): ?>
            <div style="margin-bottom:9px">
              <?php foreach ($chainDocs as $doc): ?>
              <div style="display:flex;align-items:center;gap:9px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--r);padding:7px 11px;margin-bottom:5px">
                <span><?= $doc['file_type']==='pdf'?'[PDF]':'[IMG]' ?></span>
                <div style="flex:1;min-width:0"><div style="font-size:.78rem;color:var(--t1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= e($doc['file_name']) ?></div></div>
                <a href="<?= e($doc['file_path']) ?>" target="_blank" class="btn btn-ghost btn-sm">Open</a>
                <?php if (is_admin()): ?><form method="POST" onsubmit="return confirm('Delete?')"><input type="hidden" name="action" value="delete_chain_doc"><input type="hidden" name="doc_id" value="<?= $doc['id'] ?>"><input type="hidden" name="plan_id" value="<?= (int)$editPlan['id'] ?>"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button></form><?php endif; ?>
              </div>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <div class="upload-zone" id="chainZone"><input type="file" name="chain_docs[]" id="chainInput" accept="image/*,.pdf" multiple><div style="font-size:2.2rem;margin-bottom:.4rem;color:var(--primary-d)"><i class="bx bx-folder-plus"></i></div><p>Click or drag files here</p><small>Multiple files allowed</small></div>
            <div id="chainPreviewList" style="display:none;flex-direction:column;gap:5px;margin-top:7px"></div>
          </div>
          <?php endif; ?>

          <!-- Developer pricing fields -->
          <div class="dev-field" id="pricingSection" style="<?= (!$isDeveloperForm&&!$editIsDev)?'display:none':'' ?>">
            <div class="section-heading">Pricing by Road Width</div>
            <div class="form-field" style="margin-bottom:.5rem">
              <label>Price Unit</label>
              <select class="input" name="price_unit" style="width:auto;min-width:130px"><option value="sq.ft" <?= ($editPlan['price_unit']??'sq.ft')==='sq.ft'?'selected':'' ?>>Per sq.ft</option><option value="sq.m" <?= ($editPlan['price_unit']??'')==='sq.m'?'selected':'' ?>>Per sq.m</option></select>
            </div>
            <div style="background:var(--surface2);border:1px solid var(--border);border-radius:var(--r);padding:12px;margin-bottom:.9rem">
              <?php foreach([['price_30ft','30 ft Road'],['price_40ft','40 ft Road'],['price_60ft','60 ft Road'],['price_80ft','80 ft Road'],['price_100ft','100 ft Road'],['price_highway','Near Highway']] as[$fn,$fl]): ?>
              <div style="display:flex;align-items:center;gap:10px;margin-bottom:7px">
                <label style="width:100px;flex-shrink:0;font-size:.73rem;font-weight:600;color:var(--t2);margin:0"><?= $fl ?></label>
                <input class="input" type="number" name="<?= $fn ?>" step="0.01" min="0" placeholder="0.00" value="<?= $editPlan?e($editPlan[$fn]??''):'' ?>">
              </div>
              <?php endforeach; ?>
            </div>
            <div class="section-heading">Brokerage</div>
            <div class="form-grid">
              <div class="form-field"><label>Brokerage Rate (%)</label><input class="input" type="number" name="brokerage_rate" step="0.01" min="0" max="100" placeholder="e.g. 2.5" value="<?= e($editPlan['brokerage_rate']??'') ?>"></div>
              <div class="form-field"><label>Brokerage Notes</label><input class="input" type="text" name="brokerage_notes" placeholder="e.g. Negotiable for bulk deals" value="<?= e($editPlan['brokerage_notes']??'') ?>"></div>
            </div>
          </div>

          <div class="form-field"><label>Notes (optional)</label><textarea class="input" name="notes" placeholder="Additional details..."><?= e($editPlan['notes']??'') ?></textarea></div>

          <div class="divider"></div>
          <div style="display:flex;gap:9px;flex-wrap:wrap">
            <a href="index.php" class="btn btn-ghost btn-md"><i class="bx bx-x"></i> Cancel</a>
            <button type="submit" class="btn btn-primary btn-md"><?= $page==='edit'?'Update Plan':($isDeveloperForm?'Submit for Approval':'Register Plan') ?></button>
          </div>
        </form>
      </div>
    </div>