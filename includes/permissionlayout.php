<?php if ($msg==='saved'): ?><div class="alert alert-success">Permissions saved successfully.</div><?php endif; ?>

    <div class="card">
      <div class="card-header"><h3><i class="bx bx-shield-quarter"></i> Permission Matrix</h3><span class="badge badge-blue">v<?= APP_VER ?></span></div>
      <div class="card-body" style="padding-bottom:.5rem">
        <p style="font-size:.82rem;color:var(--t3);margin-bottom:.9rem">Check/uncheck to grant or revoke access. Admin column is always locked to YES.</p>
        <form method="POST" action="index.php">
          <input type="hidden" name="action" value="save_permissions">
          <input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
          <?php foreach ($perms as $p): ?><input type="hidden" name="features[]" value="<?= e($p['feature']) ?>"><?php endforeach; ?>
          <div class="table-wrap">
            <table class="perm-table">
              <thead><tr><th>Feature / Action</th><th>Admin</th><th>Developer</th><th>Adv. Viewer</th><th>Basic Viewer</th></tr></thead>
              <tbody>
                <?php $lastGroup=''; foreach ($perms as $p):
                if ($p['group']!==$lastGroup): $lastGroup=$p['group']; ?>
                <tr class="perm-group"><td colspan="5"><?= strtoupper(e($p['group'])) ?></td></tr>
                <?php endif; ?>
                <tr>
                  <td><?= e($p['Label']) ?></td>
                  <td><span class="perm-locked">YES (locked)</span></td>
                  <td><input type="checkbox" name="perm_developer[<?= e($p['feature']) ?>]" value="1" <?= $p['developer']?'checked':'' ?>></td>
                  <td><input type="checkbox" name="perm_adv[<?= e($p['feature']) ?>]" value="1" <?= $p['adv_viewer']?'checked':'' ?>></td>
                  <td><input type="checkbox" name="perm_bas[<?= e($p['feature']) ?>]" value="1" <?= $p['bas_viewer']?'checked':'' ?>></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div style="margin-top:1rem;display:flex;gap:9px">
            <button type="submit" class="btn btn-primary btn-md"><i class="bx bx-check-shield"></i> Save Permissions</button>
            <a href="index.php" class="btn btn-ghost btn-md"><i class="bx bx-x"></i> Cancel</a>
          </div>
        </form>
      </div>
    </div>