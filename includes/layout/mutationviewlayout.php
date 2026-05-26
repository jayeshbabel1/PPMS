<?php
$statusMap = [
    'submitted'             => ['label'=>'Submitted',             'badge'=>'badge-blue',  'icon'=>'bx-send'],
    'processing'            => ['label'=>'Processing',            'badge'=>'badge-gold',  'icon'=>'bx-loader'],
    'demand_note_generated' => ['label'=>'Demand Note Generated', 'badge'=>'badge-gold',  'icon'=>'bx-file'],
    'demand_note_paid'      => ['label'=>'Demand Note Paid',      'badge'=>'badge-green', 'icon'=>'bx-check-circle'],
    'assigned_to_user'      => ['label'=>'Assigned to User',      'badge'=>'badge-dev',   'icon'=>'bx-user-check'],
    'disposed'              => ['label'=>'Disposed',              'badge'=>'badge-gray',  'icon'=>'bx-archive'],
];
$st = $statusMap[$mutApp['status']] ?? ['label'=>ucfirst($mutApp['status']),'badge'=>'badge-gray','icon'=>'bx-circle'];
?>

<!-- Header bar -->
<div style="display:flex;align-items:center;gap:10px;margin-bottom:1.4rem;flex-wrap:wrap">
  <a href="index.php?page=mutation" class="btn btn-ghost btn-sm"><i class="bx bx-arrow-back"></i> Back</a>
  <div style="flex:1">
    <div style="display:flex;align-items:center;gap:9px;flex-wrap:wrap">
      <h2 style="font-size:1rem;font-weight:700;color:var(--t1);margin:0;font-family:'JetBrains Mono',monospace"><?= e($mutApp['app_number']) ?></h2>
      <span class="badge <?= $st['badge'] ?>"><i class="bx <?= $st['icon'] ?>"></i> <?= $st['label'] ?></span>
      <?php if ($mutApp['payment_verified']): ?>
      <span class="badge badge-green"><i class="bx bx-check-shield"></i> Payment Verified</span>
      <?php elseif ($mutApp['txn_number']||$mutApp['payment_screenshot_path']): ?>
      <span class="badge badge-gold">Payment Pending Review</span>
      <?php endif; ?>
    </div>
    <div style="font-size:.72rem;color:var(--t3);margin-top:3px">Applied on <?= date('d M Y',strtotime($mutApp['created_at'])) ?></div>
  </div>
  <?php if (is_admin()): ?>
  <div style="display:flex;gap:6px;flex-wrap:wrap">
    <?php if (!$mutApp['payment_verified'] && ($mutApp['txn_number']||$mutApp['payment_screenshot_path'])): ?>
    <form method="POST">
      <input type="hidden" name="action" value="verify_mutation_payment">
      <input type="hidden" name="mutation_id" value="<?= $mutApp['id'] ?>">
      <input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
      <button type="submit" class="btn btn-success btn-sm"><i class="bx bx-check-shield"></i> Verify Payment</button>
    </form>
    <?php endif; ?>
    <form method="POST" onsubmit="return confirm('Delete this application permanently?')">
      <input type="hidden" name="action" value="delete_mutation">
      <input type="hidden" name="mutation_id" value="<?= $mutApp['id'] ?>">
      <input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
      <button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i> Delete</button>
    </form>
  </div>
  <?php endif; ?>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.3rem" class="view-layout">

  <!-- Left column -->
  <div style="display:flex;flex-direction:column;gap:1.1rem">

    <!-- Transferees -->
    <div class="card">
      <div class="card-header">
        <h3><i class="bx bx-group"></i> Transferees</h3>
        <span class="badge badge-blue"><?= count($transferees) ?></span>
      </div>
      <div class="card-body" style="padding:0">
        <?php foreach ($transferees as $idx=>$t): ?>
        <div style="padding:12px 14px;<?= $idx>0?'border-top:1px solid var(--border)':'' ?>">
          <div style="display:flex;align-items:center;gap:9px;margin-bottom:7px">
            <div style="width:32px;height:32px;background:var(--primary-bg);border:1px solid #a0bcd4;border-radius:50%;
                        display:flex;align-items:center;justify-content:center;font-size:.78rem;font-weight:700;
                        color:var(--primary-d);flex-shrink:0"><?= $idx+1 ?></div>
            <div>
              <div style="font-size:.88rem;font-weight:700;color:var(--t1)"><?= e($t['full_name']) ?></div>
              <div style="font-size:.69rem;color:var(--t3);text-transform:capitalize"><?= e($t['gender']) ?></div>
            </div>
          </div>
          <table style="width:100%;border-collapse:collapse">
            <?php
            $trows=[];
            if ($t['contact'])   $trows[]=['Contact', $t['contact']];
            if ($t['email'])     $trows[]=['Email',   $t['email']];
            if ($t['aadhaar_no'])$trows[]=['Aadhaar', $t['aadhaar_no']];
            if ($t['address'])   $trows[]=['Address', $t['address']];
            foreach ($trows as [$lbl,$val]):
            ?>
            <tr>
              <td style="padding:4px 0;border-bottom:1px solid var(--surface2);font-size:.65rem;font-weight:700;color:var(--t4);text-transform:uppercase;width:30%"><?= $lbl ?></td>
              <td style="padding:4px 0 4px 8px;border-bottom:1px solid var(--surface2);font-size:.79rem;color:var(--t1);text-align:right"><?= e($val) ?></td>
            </tr>
            <?php endforeach; ?>
          </table>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Property Details -->
    <div class="card">
      <div class="card-header"><h3><i class="bx bx-buildings"></i> Property Details</h3></div>
      <div class="card-body">
        <table style="width:100%;border-collapse:collapse">
          <?php foreach([
            ['Aaraji Number', $mutApp['aaraji_number']],
            ['Village',       $mutApp['village_name']??'--'],
            ['Applied On',    date('d M Y',strtotime($mutApp['created_at']))],
            ['App. Fee',      $mutApp['application_fee']?'Rs '.number_format((float)$mutApp['application_fee'],2):'--'],
          ] as [$lbl,$val]): ?>
          <tr>
            <td style="padding:6px 0;border-bottom:1px solid var(--surface2);font-size:.67rem;font-weight:700;color:var(--t4);text-transform:uppercase"><?= $lbl ?></td>
            <td style="padding:6px 0;border-bottom:1px solid var(--surface2);font-size:.82rem;color:var(--t1);text-align:right;font-family:'JetBrains Mono',monospace"><?= e($val) ?></td>
          </tr>
          <?php endforeach; ?>
        </table>

        <?php if ($mutApp['registry_path']): ?>
        <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border)">
          <div style="font-size:.67rem;font-weight:700;color:var(--t4);text-transform:uppercase;margin-bottom:7px">Registry Document</div>
          <div style="display:flex;align-items:center;gap:10px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--r);padding:9px 12px">
            <span style="font-size:1.4rem"><?= $mutApp['registry_path']&&strpos($mutApp['registry_path'],'.pdf')!==false?'📄':'🖼️' ?></span>
            <div style="flex:1;min-width:0">
              <div style="font-size:.8rem;font-weight:600;color:var(--t1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= e($mutApp['registry_name']) ?></div>
            </div>
            <a href="<?= e($mutApp['registry_path']) ?>" target="_blank" class="btn btn-ghost btn-sm"><i class="bx bx-link-external"></i></a>
            <a href="<?= e($mutApp['registry_path']) ?>" download="<?= e($mutApp['registry_name']) ?>" class="btn btn-secondary btn-sm"><i class="bx bx-download"></i></a>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Chain Documents -->
    <?php if (!empty($mutChainDocs)): ?>
    <div class="card">
      <div class="card-header">
        <h3><i class="bx bx-link-alt"></i> Chain Documents</h3>
        <span class="badge badge-blue"><?= count($mutChainDocs) ?></span>
      </div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:8px">
        <?php foreach ($mutChainDocs as $idx=>$doc): ?>
        <div style="display:flex;align-items:center;gap:10px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--r);padding:9px 12px">
          <span style="font-size:1.2rem"><?= $doc['file_type']==='pdf'?'📄':'🖼️' ?></span>
          <div style="flex:1;min-width:0">
            <div style="font-size:.8rem;font-weight:600;color:var(--t1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= e($doc['file_name']) ?></div>
            <div style="font-size:.67rem;color:var(--t4)">Doc #<?= $idx+1 ?><?= $doc['file_size']?' · '.round($doc['file_size']/1024).' KB':'' ?></div>
          </div>
          <a href="<?= e($doc['file_path']) ?>" target="_blank" class="btn btn-ghost btn-sm"><i class="bx bx-link-external"></i></a>
          <a href="<?= e($doc['file_path']) ?>" download="<?= e($doc['file_name']) ?>" class="btn btn-secondary btn-sm"><i class="bx bx-download"></i></a>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <!-- Right column -->
  <div style="display:flex;flex-direction:column;gap:1.1rem">

    <!-- Status & Admin Actions -->
    <div class="card">
      <div class="card-header"><h3><i class="bx bx-git-branch"></i> Application Status</h3></div>
      <div class="card-body">
        <div style="display:flex;align-items:center;gap:10px;padding:10px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--r);margin-bottom:1rem">
          <i class="bx <?= $st['icon'] ?>" style="font-size:1.4rem;color:var(--primary-d)"></i>
          <div>
            <div style="font-size:.84rem;font-weight:700;color:var(--t1)"><?= $st['label'] ?></div>
            <div style="font-size:.7rem;color:var(--t3)">Current status</div>
          </div>
        </div>
        <?php if ($mutApp['status_note']): ?>
        <div style="background:var(--primary-bg);border:1px solid #a0bcd4;border-radius:var(--r);padding:9px 12px;font-size:.79rem;color:var(--primary-d);margin-bottom:1rem">
          <i class="bx bx-message-dots"></i> <?= nl2br(e($mutApp['status_note'])) ?>
        </div>
        <?php endif; ?>
        <?php if (is_admin()): ?>
        <form method="POST" action="index.php">
          <input type="hidden" name="action" value="update_mutation_status">
          <input type="hidden" name="mutation_id" value="<?= $mutApp['id'] ?>">
          <input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
          <div class="form-field"><label>Update Status</label>
            <select class="input" name="new_status">
              <?php foreach ($statusMap as $sk=>$sv): ?>
              <option value="<?= $sk ?>" <?= $mutApp['status']===$sk?'selected':'' ?>><?= $sv['label'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-field"><label>Status Note (optional)</label>
            <textarea class="input" name="status_note" style="min-height:60px" placeholder="Add a note for the applicant..."><?= e($mutApp['status_note']??'') ?></textarea>
          </div>
          <?php if ($mutApp['status']==='assigned_to_user'): ?>
          <div class="form-field">
            <label>Upload Assignment File (optional)</label>
            <div class="upload-zone" style="padding:.7rem">
              <input type="file" name="assigned_file" accept="image/*,.pdf">
              <p style="font-size:.77rem">Attach assigned document</p>
            </div>
          </div>
          <?php endif; ?>
          <button type="submit" class="btn btn-primary btn-md btn-full"><i class="bx bx-save"></i> Update Status</button>
        </form>
        <?php endif; ?>
      </div>
    </div>

    <!-- Payment Details -->
    <div class="card">
      <div class="card-header">
        <h3><i class="bx bx-receipt"></i> Payment Details</h3>
        <?php if ($mutApp['payment_verified']): ?>
        <span class="badge badge-green"><i class="bx bx-check"></i> Verified</span>
        <?php elseif ($mutApp['txn_number']||$mutApp['payment_screenshot_path']): ?>
        <span class="badge badge-gold">Awaiting Review</span>
        <?php else: ?>
        <span class="badge badge-gray">Not Submitted</span>
        <?php endif; ?>
      </div>
      <div class="card-body">
        <table style="width:100%;border-collapse:collapse">
          <?php
          $prows=[];
          if ($mutApp['application_fee']) $prows[]=['Fee',      'Rs '.number_format((float)$mutApp['application_fee'],2)];
          if ($mutApp['txn_number'])      $prows[]=['Txn / UTR', $mutApp['txn_number']];
          if ($mutApp['txn_date'])        $prows[]=['Txn Date',  date('d M Y',strtotime($mutApp['txn_date']))];
          if ($mutApp['txn_type'])        $prows[]=['Method',    $mutApp['txn_type']];
          foreach ($prows as [$lbl,$val]):
          ?>
          <tr>
            <td style="padding:6px 0;border-bottom:1px solid var(--surface2);font-size:.67rem;font-weight:700;color:var(--t4);text-transform:uppercase"><?= $lbl ?></td>
            <td style="padding:6px 0;border-bottom:1px solid var(--surface2);font-size:.82rem;color:var(--t1);text-align:right;font-family:'JetBrains Mono',monospace"><?= e($val) ?></td>
          </tr>
          <?php endforeach; ?>
        </table>
        <?php if ($mutApp['payment_screenshot_path']): ?>
        <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border)">
          <div style="font-size:.67rem;font-weight:700;color:var(--t4);text-transform:uppercase;margin-bottom:7px">Payment Screenshot</div>
          <div style="display:flex;align-items:center;gap:10px;background:var(--green-bg);border:1px solid #9acc9a;border-radius:var(--r);padding:9px 12px">
            <span style="font-size:1.4rem">📸</span>
            <div style="flex:1;min-width:0">
              <div style="font-size:.79rem;font-weight:600;color:var(--green);white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= e($mutApp['payment_screenshot_name']) ?></div>
            </div>
            <a href="<?= e($mutApp['payment_screenshot_path']) ?>" target="_blank" class="btn btn-ghost btn-sm"><i class="bx bx-link-external"></i></a>
            <a href="<?= e($mutApp['payment_screenshot_path']) ?>" download class="btn btn-success btn-sm"><i class="bx bx-download"></i></a>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Assigned File -->
    <?php if ($mutApp['assigned_file_path']): ?>
    <div class="card">
      <div class="card-header"><h3><i class="bx bx-user-check"></i> Assigned Document</h3></div>
      <div class="card-body">
        <div style="display:flex;align-items:center;gap:10px;background:var(--dev-bg,#e8f0fe);border:1px solid #c0c8f0;border-radius:var(--r);padding:9px 12px">
          <span style="font-size:1.4rem">📋</span>
          <div style="flex:1;min-width:0">
            <div style="font-size:.8rem;font-weight:600;color:#4a5fca;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= e($mutApp['assigned_file_name']) ?></div>
          </div>
          <a href="<?= e($mutApp['assigned_file_path']) ?>" target="_blank" class="btn btn-ghost btn-sm"><i class="bx bx-link-external"></i></a>
          <a href="<?= e($mutApp['assigned_file_path']) ?>" download class="btn btn-secondary btn-sm"><i class="bx bx-download"></i></a>
        </div>
      </div>
    </div>
    <?php endif; ?>

  </div>
</div>
