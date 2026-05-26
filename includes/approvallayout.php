<?php if (!empty($pending)): ?>
    <div class="alert alert-warning"><?= count($pending) ?> developer plan(s) awaiting review.</div>
    <div class="card" style="margin-bottom:1.3rem">
      <div class="card-header"><h3><i class="bx bx-time"></i> Pending Approvals</h3><span class="badge badge-gold"><?= count($pending) ?></span></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:10px">
        <?php foreach ($pending as $dp): ?>
        <div style="background:var(--gold-bg);border:1px solid #d4b090;border-radius:var(--r);padding:12px 14px">
          <div style="display:flex;align-items:flex-start;gap:10px;flex-wrap:wrap">
            <div style="flex:1;min-width:180px">
              <strong style="color:var(--t1)"><?= e($dp['plan_name']) ?></strong> <span style="font-size:.74rem;color:var(--gold-s)"># <?= e($dp['aaraji_number']) ?></span>
              <div style="font-size:.75rem;color:var(--t2);margin-top:2px">Developer: <strong><?= e($dp['dev_fullname']?:$dp['dev_name']) ?></strong> | Village: <?= e($dp['village_name']??'--') ?></div>
              <?php if ($dp['contact_number']): ?><div style="font-size:.72rem;color:var(--t3)">Tel: <?= e($dp['contact_number']) ?></div><?php endif; ?>
              <div style="font-size:.68rem;color:var(--t4);margin-top:2px"><?= date('d M Y H:i',strtotime($dp['created_at'])) ?></div>
              <div style="margin-top:6px;display:flex;gap:5px"><?php if ($dp['file_path']): ?><a href="<?= e($dp['file_path']) ?>" target="_blank" class="btn btn-ghost btn-sm"><i class="bx bx-show"></i> View Plan</a><?php endif; ?><?php if ($dp['approved_map_path']): ?><a href="<?= e($dp['approved_map_path']) ?>" target="_blank" class="btn btn-ghost btn-sm"><i class="bx bx-map"></i> View Map</a><?php endif; ?></div>
            </div>
            <form method="POST" action="index.php" style="display:flex;gap:6px;align-items:flex-start;flex-wrap:wrap;flex-shrink:0">
              <input type="hidden" name="action" value="review_dev_plan"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><input type="hidden" name="plan_id" value="<?= $dp['id'] ?>">
              <input class="input" type="text" name="admin_note" placeholder="Note (optional)" style="width:140px;font-size:.77rem">
              <button type="submit" name="status" value="approved" class="btn btn-success btn-sm"><i class="bx bx-check-circle"></i> Approve</button>
              <button type="submit" name="status" value="rejected" class="btn btn-danger btn-sm"><i class="bx bx-x-circle"></i> Reject</button>
            </form>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <div class="card">
      <div class="card-header"><h3><i class="bx bx-check-circle"></i> Approved Developer Plans</h3><span class="badge badge-green"><?= count($approved) ?></span></div>
      <?php if (empty($approved)): ?><div style="padding:1.5rem;text-align:center;color:var(--t3);font-size:.82rem">No approved plans yet.</div>
      <?php else: ?>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Plan</th><th>Developer</th><th>Village</th><th>Sponsored</th><th>Approved</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($approved as $dp): ?>
            <tr>
              <td><strong style="color:var(--t1)"><?= e($dp['plan_name']) ?></strong><br><small style="color:var(--primary-d)"># <?= e($dp['aaraji_number']) ?></small></td>
              <td style="font-size:.79rem"><?= e($dp['dev_name']) ?></td>
              <td style="font-size:.79rem"><?= e($dp['village_name']??'--') ?></td>
              <td><?= $dp['is_sponsored']?'<span class="badge badge-gold">'.e($dp['sponsored_label']).'</span>':'<span style="color:var(--t4);font-size:.74rem">No</span>' ?></td>
              <td style="font-size:.72rem;color:var(--t3)"><?= $dp['approved_at']?date('d M Y',strtotime($dp['approved_at'])):'--' ?></td>
              <td>
                <div style="display:flex;gap:5px;flex-wrap:wrap">
                  <a href="index.php?page=view&id=<?= $dp['id'] ?>" class="btn btn-primary btn-sm"><i class="bx bx-show-alt"></i> View</a>
                  <form method="POST" style="display:flex;align-items:center;gap:4px">
                    <input type="hidden" name="action" value="toggle_sponsored"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><input type="hidden" name="plan_id" value="<?= $dp['id'] ?>">
                    <input class="input" type="text" name="sponsored_label" value="<?= e($dp['sponsored_label']??'Sponsored') ?>" style="width:90px;font-size:.72rem;padding:4px 7px">
                    <button type="submit" class="btn btn-sm <?= $dp['is_sponsored']?'btn-secondary':'btn-gold' ?>"><?= $dp['is_sponsored']?'Un-Feature':'Feature' ?></button>
                  </form>
                  <form method="POST" onsubmit="return confirm('Delete?')"><input type="hidden" name="action" value="delete_plan"><input type="hidden" name="plan_id" value="<?= $dp['id'] ?>"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button></form>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>