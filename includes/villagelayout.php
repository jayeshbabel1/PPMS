  <div class="two-col">
      <?php if (is_admin()): ?>
      <div class="card"><div class="card-header"><h3><i class="bx bx-plus-circle"></i> Add Revenue Village</h3></div><div class="card-body">
        <form method="POST" action="index.php"><input type="hidden" name="action" value="save_village"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
          <div class="form-field"><label>Village Name <span class="req">*</span></label><input class="input" type="text" name="village_name" required placeholder="e.g. Malegaon"></div>
          <div class="form-field"><label>Tehsil</label><input class="input" type="text" name="tehsil" placeholder="e.g. Sinnar"></div>
          <div class="form-field"><label>District</label><input class="input" type="text" name="district" placeholder="e.g. Nashik"></div>
          <button type="submit" class="btn btn-primary btn-md"><i class="bx bx-check"></i> Save Village</button>
        </form>
      </div></div>
      <?php endif; ?>
      <div class="card"><div class="card-header"><h3><i class="bx bx-building-house"></i> All Revenue Villages</h3><span class="badge badge-blue"><?= count($villages) ?></span></div>
        <div class="table-wrap"><?php if (empty($villages)): ?><div style="padding:1.5rem;text-align:center;color:var(--t3)">No villages yet.</div><?php else: ?>
        <table><thead><tr><th>Village</th><th>Tehsil</th><th>Plans</th><?php if (is_admin()): ?><th></th><?php endif; ?></tr></thead><tbody>
          <?php foreach ($villages as $v): ?>
          <tr><td><strong style="color:var(--t1)"><?= e($v['name']) ?></strong><?php if ($v['district']): ?><br><small style="color:var(--t3)"><?= e($v['district']) ?></small><?php endif; ?></td><td><?= e($v['tehsil']??'--') ?></td><td><span class="badge badge-gray"><?= (int)$v['plan_count'] ?></span></td>
            <?php if (is_admin()): ?><td><form method="POST" onsubmit="return confirm('Delete village?')"><input type="hidden" name="action" value="delete_village"><input type="hidden" name="village_id" value="<?= $v['id'] ?>"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button></form></td><?php endif; ?>
          </tr>
          <?php endforeach; ?>
        </tbody></table><?php endif; ?>
      </div></div>
    </div>