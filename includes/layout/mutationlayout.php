<?php
$statusMap = [
    'submitted'               => ['label'=>'Submitted',             'badge'=>'badge-blue'],
    'processing'              => ['label'=>'Processing',            'badge'=>'badge-gold'],
    'demand_note_generated'   => ['label'=>'Demand Note Generated', 'badge'=>'badge-gold'],
    'demand_note_paid'        => ['label'=>'Demand Note Paid',      'badge'=>'badge-green'],
    'assigned_to_user'        => ['label'=>'Assigned to User',      'badge'=>'badge-dev'],
    'disposed'                => ['label'=>'Disposed',              'badge'=>'badge-gray'],
];
?>

<!-- Top bar -->
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:1.2rem">
  <div>
    <h2 style="font-size:1rem;font-weight:700;color:var(--t1);margin:0">Mutation Applications</h2>
    <p style="font-size:.76rem;color:var(--t3);margin:3px 0 0">Track all property mutation applications</p>
  </div>
  <a href="index.php?page=mutation_apply" class="btn btn-primary btn-md">
    <i class="bx bx-plus"></i> Apply New Mutation
  </a>
</div>

<!-- Stats -->
<div class="stats-grid" style="grid-template-columns:repeat(auto-fit,minmax(120px,1fr));margin-bottom:1.3rem">
  <?php
  $total    = count($mutations);
  $pending  = count(array_filter($mutations, fn($m)=>in_array($m['status'],['submitted','processing','demand_note_generated'])));
  $active   = count(array_filter($mutations, fn($m)=>in_array($m['status'],['demand_note_paid','assigned_to_user'])));
  $disposed = count(array_filter($mutations, fn($m)=>$m['status']==='disposed'));
  foreach([
    ['[M]','si-blue',$total,   'Total'],
    ['[P]','si-gold',$pending, 'Pending'],
    ['[A]','si-green',$active, 'In Progress'],
    ['[D]','si-gray',$disposed,'Disposed'],
  ] as[$ic,$sc,$sv,$sl]):
  ?>
  <div class="stat-card">
    <div class="stat-icon <?= $sc ?>"><?= $ic ?></div>
    <div><div class="stat-val"><?= $sv ?></div><div class="stat-lbl"><?= $sl ?></div></div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Filter bar -->
<form method="GET" action="index.php" style="display:flex;gap:7px;flex-wrap:wrap;margin-bottom:1.1rem;align-items:center">
  <input type="hidden" name="page" value="mutation">
  <select class="input" name="status" style="width:auto;min-width:170px" onchange="this.form.submit()">
    <option value="">All Statuses</option>
    <?php foreach ($statusMap as $k=>$v): ?>
    <option value="<?= $k ?>" <?= ($filterStatus??'')===$k?'selected':'' ?>><?= $v['label'] ?></option>
    <?php endforeach; ?>
  </select>
  <?php if (!empty($filterStatus)): ?>
  <a href="index.php?page=mutation" class="btn btn-ghost btn-sm"><i class="bx bx-x"></i> Clear</a>
  <?php endif; ?>
</form>

<!-- Table -->
<div class="card">
  <div class="card-header">
    <h3><i class="bx bx-file-blank"></i> All Applications</h3>
    <span class="badge badge-blue"><?= count($mutations) ?></span>
  </div>

  <?php if (empty($mutations)): ?>
  <div style="padding:3rem;text-align:center;color:var(--t3)">
    <div style="font-size:3rem;margin-bottom:.7rem"><i class="bx bx-file-blank"></i></div>
    <div style="font-weight:600;color:var(--t2);margin-bottom:.3rem">No mutation applications yet</div>
    <div style="font-size:.8rem;margin-bottom:1.2rem">Start by applying for a new mutation</div>
    <a href="index.php?page=mutation_apply" class="btn btn-primary btn-md"><i class="bx bx-plus"></i> Apply Now</a>
  </div>

  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>App No.</th>
          <th>Aaraji / Village</th>
          <th>Transferees</th>
          <th>Applied On</th>
          <th>Payment</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($mutations as $m):
          $st = $statusMap[$m['status']] ?? ['label'=>ucfirst($m['status']),'badge'=>'badge-gray'];
        ?>
        <tr>
          <td>
            <strong style="color:var(--primary-d);font-family:'JetBrains Mono',monospace;font-size:.8rem"><?= e($m['app_number']) ?></strong>
            <?php if ($m['application_fee']): ?>
            <div style="font-size:.68rem;color:var(--t4);margin-top:1px">Fee: Rs <?= number_format((float)$m['application_fee'],2) ?></div>
            <?php endif; ?>
          </td>
          <td>
            <div style="font-size:.82rem;font-weight:600;color:var(--t1)"><?= e($m['aaraji_number']) ?></div>
            <?php if ($m['village_name']): ?>
            <div style="font-size:.7rem;color:var(--t3)"><?= e($m['village_name']) ?></div>
            <?php endif; ?>
          </td>
          <td>
            <?php
            $names = array_filter(explode('||', $m['transferee_names']??''));
            if ($names):
              foreach (array_slice($names,0,2) as $n):
            ?>
            <div style="font-size:.78rem;color:var(--t2)"><?= e($n) ?></div>
            <?php endforeach;
            if (count($names)>2): ?>
            <div style="font-size:.67rem;color:var(--t4)">+<?= count($names)-2 ?> more</div>
            <?php endif; else: ?>
            <span style="color:var(--t4);font-size:.75rem">--</span>
            <?php endif; ?>
          </td>
          <td style="font-size:.73rem;color:var(--t3);white-space:nowrap"><?= date('d M Y',strtotime($m['created_at'])) ?></td>
          <td>
            <?php if ($m['payment_verified']): ?>
            <span class="badge badge-green"><i class="bx bx-check"></i> Verified</span>
            <?php elseif ($m['txn_number']||$m['payment_screenshot_path']): ?>
            <span class="badge badge-gold">Pending Review</span>
            <?php else: ?>
            <span class="badge badge-gray">Not Submitted</span>
            <?php endif; ?>
          </td>
          <td><span class="badge <?= $st['badge'] ?>"><?= $st['label'] ?></span></td>
          <td>
            <div style="display:flex;gap:4px">
              <a href="index.php?page=mutation_view&id=<?= $m['id'] ?>" class="btn btn-primary btn-sm"><i class="bx bx-show-alt"></i> View</a>
              <?php if (is_admin()): ?>
              <div style="position:relative;display:inline-block" class="status-dropdown-wrap">
                <button class="btn btn-secondary btn-sm" onclick="toggleStatusDrop(<?= $m['id'] ?>)">
                  <i class="bx bx-cog"></i>
                </button>
                <div id="sdrop_<?= $m['id'] ?>" style="display:none;position:absolute;right:0;top:100%;margin-top:3px;background:var(--surface);border:1px solid var(--border);border-radius:var(--r);box-shadow:var(--sh-md);z-index:50;min-width:190px;padding:4px 0">
                  <?php foreach ($statusMap as $sk=>$sv): ?>
                  <form method="POST" action="index.php">
                    <input type="hidden" name="action" value="update_mutation_status">
                    <input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
                    <input type="hidden" name="mutation_id" value="<?= $m['id'] ?>">
                    <input type="hidden" name="new_status" value="<?= $sk ?>">
                    <button type="submit" style="width:100%;text-align:left;background:none;border:none;padding:7px 14px;font-size:.78rem;color:var(--t2);cursor:pointer;display:flex;align-items:center;gap:7px"
                            <?= $m['status']===$sk?'style="width:100%;text-align:left;background:var(--primary-bg);border:none;padding:7px 14px;font-size:.78rem;color:var(--primary-d);cursor:pointer;font-weight:700;display:flex;align-items:center;gap:7px"':'' ?>>
                      <span class="badge <?= $sv['badge'] ?>" style="font-size:.58rem"><?= $sv['label'] ?></span>
                    </button>
                  </form>
                  <?php endforeach; ?>
                </div>
              </div>
              <form method="POST" onsubmit="return confirm('Delete this application?')">
                <input type="hidden" name="action" value="delete_mutation">
                <input type="hidden" name="mutation_id" value="<?= $m['id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
                <button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button>
              </form>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<script>
function toggleStatusDrop(id) {
  document.querySelectorAll('[id^="sdrop_"]').forEach(function(el){
    if (el.id !== 'sdrop_'+id) el.style.display='none';
  });
  var d = document.getElementById('sdrop_'+id);
  if (d) d.style.display = d.style.display==='none' ? 'block' : 'none';
}
document.addEventListener('click', function(e){
  if (!e.target.closest('.status-dropdown-wrap')) {
    document.querySelectorAll('[id^="sdrop_"]').forEach(function(el){ el.style.display='none'; });
  }
});
</script>
