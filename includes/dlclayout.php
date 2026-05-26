<?php if (is_admin()): ?>
    <div style="display:flex;gap:9px;margin-bottom:1.1rem;flex-wrap:wrap;align-items:center">
      <button onclick="openDlcModal()" class="btn btn-primary btn-sm"><i class="bx bx-plus"></i> Add DLC Rate</button>
      <a href="index.php?action=export_dlc<?= $filterVid?'&village='.$filterVid:'' ?><?= $filterFy?'&fy='.urlencode($filterFy):'' ?>" class="btn btn-success btn-sm"><i class="bx bx-table"></i> Export Excel</a>
      <form method="POST" action="index.php" enctype="multipart/form-data" style="display:flex;align-items:center;gap:7px">
        <input type="hidden" name="action" value="import_dlc_csv"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
        <input type="file" name="dlc_csv" accept=".csv,.txt" style="font-size:.74rem;background:var(--surface);border:1px solid var(--border);border-radius:var(--r);padding:4px 8px;max-width:190px" required>
        <button type="submit" class="btn btn-secondary btn-sm"><i class="bx bx-upload"></i> Import CSV</button>
      </form>
    </div>
    <div class="alert alert-info" style="font-size:.74rem">CSV: Village Name, Financial Year, Effective From, 30 ft Road, 40 ft Road, 60 ft Road, 80 ft Road, 100 ft Road, Near Highway, Notes</div>
    <?php endif; ?>
    <?php if ($err==='missing'): ?><div class="alert alert-danger">Village, FY and Effective From required.</div><?php endif; ?>
    <form method="GET" action="index.php" style="display:flex;gap:7px;margin-bottom:1.1rem;flex-wrap:wrap">
      <input type="hidden" name="page" value="dlc">
      <select class="input" name="village" style="width:auto;min-width:145px"><option value="">All Villages</option><?php foreach ($villagesAll as $v): ?><option value="<?= $v['id'] ?>" <?= $filterVid==$v['id']?'selected':'' ?>><?= e($v['name']) ?></option><?php endforeach; ?></select>
      <select class="input" name="fy" style="width:auto;min-width:110px">
        <option value="<?= e($fyDefault) ?>" <?= $filterFy===$fyDefault&&!in_array($fyDefault,$fyList)?'selected':'' ?>><?= e($fyDefault) ?> (Current)</option>
        <?php foreach ($fyList as $fy): ?><option value="<?= e($fy) ?>" <?= $filterFy===$fy?'selected':'' ?>><?= e($fy) ?><?= $fy===$fyDefault?' *':'' ?></option><?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-secondary btn-sm"><i class="bx bx-filter-alt"></i> Filter</button>
      <?php if ($filterVid||($filterFy&&$filterFy!==$fyDefault)): ?><a href="index.php?page=dlc" class="btn btn-ghost btn-sm"><i class="bx bx-x"></i> Clear</a><?php endif; ?>
    </form>
    <div class="card">
      <div class="card-header"><h3><i class="bx bx-bar-chart-alt-2"></i> DLC Records <span style="font-size:.75rem;color:var(--t3);font-weight:400">— FY: <?= e($filterFy?:'All') ?></span></h3><span class="badge badge-blue"><?= count($dlcList) ?></span></div>
      <?php if (empty($dlcList)): ?><div style="padding:2rem;text-align:center;color:var(--t3);font-size:.82rem">No DLC records for selected filters.<?= is_admin()?' Click "Add DLC Rate".':'' ?></div>
      <?php else: ?>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Village</th><th>FY</th><th>Eff. Date</th><th>30ft</th><th>40ft</th><th>60ft</th><th>80ft</th><th>100ft</th><th>Highway</th><?php if (is_admin()): ?><th>Actions</th><?php endif; ?></tr></thead>
          <tbody>
            <?php foreach ($dlcList as $dr): ?>
            <tr>
              <td><strong style="color:var(--t1)"><?= e($dr['village_name']) ?></strong><?= $dr['tehsil']?'<br><small style="color:var(--t3)">'.e($dr['tehsil']).'</small>':'' ?></td>
              <td><span class="badge badge-gold"><?= e($dr['financial_year']) ?></span></td>
              <td style="font-size:.74rem;color:var(--t3);white-space:nowrap"><?= date('d M Y',strtotime($dr['effective_from'])) ?></td>
              <?php foreach (['road_30ft','road_40ft','road_60ft','road_80ft','road_100ft','near_highway'] as $f): ?>
              <td style="text-align:right"><?php if ($dr[$f]!==null): ?><div class="dlc-sqm"><?= fmtSqm((float)$dr[$f]) ?></div><div class="dlc-sqft">(<?= fmtSqft((float)$dr[$f]) ?>)</div><?php else: ?><span style="color:var(--t4)">--</span><?php endif; ?></td>
              <?php endforeach; ?>
              <?php if (is_admin()): ?><td><div style="display:flex;gap:4px"><button onclick="openDlcEdit(<?= htmlspecialchars(json_encode($dr),ENT_QUOTES) ?>)" class="btn btn-secondary btn-sm"><i class="bx bx-edit-alt"></i> Edit</button><form method="POST" onsubmit="return confirm('Delete?')"><input type="hidden" name="action" value="delete_dlc"><input type="hidden" name="dlc_id" value="<?= $dr['id'] ?>"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button></form></div></td><?php endif; ?>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>