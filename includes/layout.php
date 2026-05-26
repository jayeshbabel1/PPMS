<?php 
// includes/layout.php — PMS v3.0 
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
<title><?= e(APP_BRAND) ?><?= !in_array($page,['home','login'])?' — '.ucfirst(str_replace('_',' ',$page)):'' ?></title>
   <style><?= get_theme_css() ?>
  /* Login */
<?php if (in_array($page,['login','reset_password'])): ?>
body{display:flex;align-items:center;justify-content:center;min-height:100vh;padding:1.5rem;background:linear-gradient(135deg,var(--bg) 0%,var(--sec) 100%);}
<?php endif; ?>
  </style>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
</head>
<body>
<!-- Zoom overlay -->
<div id="imgZoom" onclick="closeZoom(event)">
  <button class="zoom-close-btn" onclick="closeZoomBtn()">X</button>
  <img id="zoomImg" src="" alt="zoom">
  <div class="zoom-ctrls">
    <button onclick="zoomIn()">+</button>
    <button onclick="zoomReset()">O</button>
    <button onclick="zoomOut()">-</button>
  </div>
</div>

<!-- DLC Modal -->
<div class="modal-overlay" id="dlcModal">
  <div class="modal-box">
    <div class="modal-hdr"><h3 id="dlcMTitle">Add DLC Rate</h3><button class="modal-close" onclick="closeDlcModal()"><i class="bx bx-x"></i></button></div>
    <div class="modal-body">
      <form method="POST" action="index.php">
        <input type="hidden" name="action" value="save_dlc">
        <input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
        <input type="hidden" name="dlc_id" id="dlcId" value="0">
        <div class="form-field"><label>Revenue Village <span class="req">*</span></label>
          <select class="input" name="village_id" id="dlcV" required>
            <option value="">-- Select Village --</option>
            <?php foreach (($villagesAll??db()->query('SELECT * FROM revenue_villages ORDER BY name')->fetchAll()) as $v): ?>
            <option value="<?= $v['id'] ?>"><?= e($v['name']) ?><?= $v['tehsil']?' - '.e($v['tehsil']):'' ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:9px">
          <div class="form-field"><label>Financial Year <span class="req">*</span></label><input class="input" type="text" name="financial_year" id="dlcFY" required placeholder="e.g. 2025-26" value="<?echo currentFY(); ?>"></div>
          <div class="form-field"><label>Effective From <span class="req">*</span></label><input class="input" type="date" name="effective_from" id="dlcEf" required></div>
        </div>
        <div style="background:var(--surface2);border:1px solid var(--border);border-radius:var(--r);padding:12px;margin-bottom:.9rem">
          <div style="font-size:.7rem;font-weight:700;color:var(--t3);text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px">DLC Rates (Rs per sq.m)</div>
          <?php foreach([['road_30ft','30 ft Road'],['road_40ft','40 ft Road'],['road_60ft','60 ft Road'],['road_80ft','80 ft Road'],['road_100ft','100 ft Road'],['near_highway','Near Highway']] as[$fn,$fl]): ?>
          <div style="display:flex;align-items:center;gap:9px;margin-bottom:7px">
            <label style="width:100px;flex-shrink:0;font-size:.73rem;font-weight:600;color:var(--t2);margin:0"><?= $fl ?></label>
            <input class="input" type="number" name="<?= $fn ?>" id="dlc_<?= $fn ?>" step="0.01" min="0" placeholder="0.00">
          </div>
          <?php endforeach; ?>
        </div>
        <div class="form-field"><label>Notes</label><textarea class="input" name="notes" id="dlcNotes" style="min-height:50px" placeholder="Optional..."></textarea></div>
        <div style="display:flex;gap:9px">
          <button type="button" onclick="closeDlcModal()" class="btn btn-ghost btn-md"><i class="bx bx-x"></i> Cancel</button>
          <button type="submit" class="btn btn-primary btn-md" id="dlcSubmitBtn"><i class="bx bx-save"></i> Save DLC Rate</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php /* ====== LOGIN / RESET PASSWORD ====== */ ?>
<?php if (in_array($page,['login','reset_password'])): ?>
<div class="login-wrap">
  <div class="login-brand">
    <div class="login-brand-icon"><i class="bx bx-map-pin" style="font-size:1.8rem"></i></div>
    <h1><?= e(APP_NAME) ?></h1>
    <div class="sub"><?= e(APP_BRAND) ?></div>
  </div>

  <?php if ($page==='login'): ?>
  <?php
	require __DIR__.'/layout/loginlayout.php';
	?>

  <?php elseif ($page==='reset_password'): ?>
  <?php
	require __DIR__.'/layout/resetpasswordlayout.php';
	?>
  <?php endif; ?>

<?php /* ====== APP SHELL ====== */ ?>
<?php else: ?>

<?php if ($gMarqueeEnabled==='1'&&trim($gMarqueeText)!==''): ?>
<div class="mqbar"><span class="mqinner" style="animation-duration:<?= max(10,(int)$gMarqueeSpeed) ?>s">&nbsp;&nbsp;&nbsp;<?= e($gMarqueeText) ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= e($gMarqueeText) ?>&nbsp;&nbsp;&nbsp;</span></div>
<?php endif; ?>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
<div class="app-shell">
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
      <div class="brand-icon"><i class="bx bx-map-pin" style="font-size:1.2rem"></i></div>
      <div class="brand-text"><h2><?= e(APP_NAME) ?></h2><span>Mingosoft Technologies</span></div>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-section">Main</div>
      <a class="nav-item <?= $page==='home'?'active':'' ?>" href="index.php"><span class="ni"><i class="bx bxs-dashboard"></i></span> Dashboard</a>
      <?php if (is_admin()||is_developer()): ?>
      <a class="nav-item <?= ($page==='add'||$page==='edit')?'active':'' ?>" href="index.php?page=add"><span class="ni">[+]</span> <?= is_developer()&&!is_admin()?'Submit Plan':'Add Plan' ?></a>
      <?php endif; ?>
      <div class="nav-section">Data</div>
      <a class="nav-item <?= $page==='villages'?'active':'' ?>" href="index.php?page=villages"><span class="ni"><i class="bx bx-building-house"></i></span> Revenue Villages</a>
      <a class="nav-item <?= $page==='dlc'?'active':'' ?>" href="index.php?page=dlc"><span class="ni"><i class="bx bx-bar-chart-alt-2"></i></span> DLC Rates</a>
      <a class="nav-item <?= in_array($page,['mutation','mutation_apply','mutation_view'])?'active':'' ?>" href="index.php?page=mutation">
    <span class="ni"><i class="bx bx-transfer-alt"></i></span> Mutation
  </a>
      <?php if (is_admin()): ?>
      <?php $pendCount=0; try{$pendCount=(int)db()->query("SELECT COUNT(*) FROM plans WHERE is_developer_plan=1 AND dev_status='pending'")->fetchColumn();}catch(Throwable){} ?>
      <div class="nav-section">Admin</div>
      <a class="nav-item <?= $page==='approvals'?'active':'' ?>" href="index.php?page=approvals">
        <span class="ni"><i class="bx bx-bell"></i></span> Plan Approvals
        <?php if ($pendCount>0): ?><span style="background:var(--red);color:#fff;border-radius:100px;padding:1px 6px;font-size:.62rem;margin-left:4px"><?= $pendCount ?></span><?php endif; ?>
      </a>
      <a class="nav-item <?= $page==='subscriptions'?'active':'' ?>" href="index.php?page=subscriptions"><span class="ni"><i class="bx bx-group"></i></span> Users &amp; Subs</a>
      <a class="nav-item <?= $page==='permissions'?'active':'' ?>" href="index.php?page=permissions"><span class="ni"><i class="bx bx-shield-quarter"></i></span> Permissions</a>
      <a class="nav-item <?= $page==='settings'?'active':'' ?>" href="index.php?page=settings"><span class="ni"><i class="bx bx-cog"></i></span> Settings</a>
      <?php endif; ?>
      <div class="nav-section">Account</div>
      <a class="nav-item <?= $page==='profile'?'active':'' ?>" href="index.php?page=profile"><span class="ni"><i class="bx bx-user-circle"></i></span> Profile</a>
    </nav>
    <div class="sidebar-footer">
      <?php $planLabel=get_plan_label(); $mySub=get_active_subscription(); ?>
      <div class="sidebar-plan-pill">
        <i class="bx bx-star" style="font-size:1rem;color:var(--primary-d)"></i>
        <div><div class="spp-label"><?= $planLabel ?></div><?php if ($mySub): ?><div class="spp-exp">Expires <?= date('d M Y',strtotime($mySub['end_date'])) ?></div><?php endif; ?></div>
      </div>
      <div class="user-info">
        <div class="user-avatar"><?= strtoupper(substr($user['username'],0,1)) ?></div>
        <div><div class="uname"><?= e($user['full_name']?:$user['username']) ?></div><div class="urole"><?= e($user['role']) ?></div></div>
      </div>
      <form method="POST" style="margin-bottom:7px"><input type="hidden" name="action" value="logout"><button type="submit" class="btn btn-ghost btn-sm btn-full">Sign Out</button></form>
      <?php if (trim($gFooterText)!==''): ?><div class="sidebar-footer-text"><?= $gFooterText ?></div><?php endif; ?>
    </div>
  </aside>

  <div class="main-wrap">
    <header class="topbar">
      <div class="topbar-left">
        <button class="sidebar-toggle" onclick="toggleSidebar()" title="Menu">&#9776;</button>
        <span class="topbar-title">
          <?php $tt=['home'=>'Dashboard','add'=>is_developer()&&!is_admin()?'Submit Plan':'Add Plan','edit'=>'Edit Plan','view'=>'Plan Detail','villages'=>'Revenue Villages','profile'=>'Profile','dlc'=>'DLC Rates','subscriptions'=>'Users &amp; Subscriptions','settings'=>'Settings','permissions'=>'Permission Matrix','approvals'=>'Plan Approvals','mut_submitted' => ['success','Mutation application submitted successfully.'],'mutation'=> 'Mutation Applications','mutation_apply' => 'Apply for Mutation','mutation_view'  => 'Mutation Detail'];
          echo $tt[$page]??ucfirst($page); ?>
        </span>
      </div>
      <div class="topbar-right">
        <span class="topbar-date"><?= date('d M Y') ?></span>
        <?php if (is_admin()||is_developer()): ?><a href="index.php?page=add" class="btn btn-primary btn-sm"><?= is_developer()&&!is_admin()?'+ Submit Plan':'+ Add Plan' ?></a><?php endif; ?>
      </div>
    </header>

    <main class="page-content">

    <?php /* Toast messages */
    $toastMap=['created'=>['success','Plan registered.'],'updated'=>['success','Plan updated.'],'deleted'=>['success','Plan deleted.'],'saved'=>['success','Saved.'],'pwchanged'=>['success','Password changed.'],'dlc_saved'=>['success','DLC rates saved.'],'dlc_deleted'=>['success','DLC record deleted.'],'sub_saved'=>['success','Subscription saved.'],'sub_deleted'=>['success','Subscription deleted.'],'user_created'=>['success','User created.'],'chain_deleted'=>['success','Chain doc removed.'],'upgrade_requested'=>['info','Upgrade request submitted.'],'upgrade_reviewed'=>['success','Request reviewed.'],'imported'=>['success','DLC imported: '.((int)($_GET['ok']??0)).' rows, '.((int)($_GET['skip']??0)).' skipped.'],'reviewed'=>['success','Plan reviewed.'],'dev_submitted'=>['info','Plan submitted for approval. Admin will review.'],'email_sent'=>['success','Test email sent successfully.'],'mut_submitted' => ['success','Mutation application submitted successfully.'],'mut_updated'   => ['success','Application updated.']];
    if (isset($toastMap[$msg])): [$tc,$tm]=$toastMap[$msg]; ?>
    <div class="toast-wrap" id="toastWrap"><div class="toast <?= $tc ?>"><?= $tm ?> <button onclick="this.closest('.toast-wrap').remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;color:var(--t3);font-size:1rem;font-weight:700">X</button></div></div>
    <script>setTimeout(function(){var t=document.getElementById('toastWrap');if(t)t.remove();},5000);</script>
    <?php endif;
    if ($err && !in_array($page,['profile','reset_password','settings'])): ?>
    <div class="alert alert-danger"><?= e($err) ?></div>
    <?php endif; ?>

    <?php /* ============================= HOME ============================= */ ?>
    <?php if ($page==='home'): ?>

    <!-- Sponsored Developer Plans -->
    <?php if (!empty($sponsoredPlans)): ?>
    <div class="sponsored-strip">
      <div class="sponsored-strip-title">
        <span class="label">Featured Properties</span>
        <div style="height:1px;flex:1;background:#d4b090"></div>
      </div>
      <div class="sponsored-strip-grid">
        <?php foreach ($sponsoredPlans as $sp): ?>
        <a href="index.php?page=view&id=<?= $sp['id'] ?>" class="sp-card" style="text-decoration:none">
          <div class="sp-thumb"><?php if ($sp['file_type']==='image'&&$sp['file_path']): ?><img src="<?= e($sp['file_path']) ?>" alt=""><?php else: ?><span><i class="bx bx-buildings" style="font-size:1.6rem;color:#4a5fca"></i></span><?php endif; ?></div>
          <div class="sp-body">
            <div class="sp-name"><?= e($sp['plan_name']) ?></div>
            <?php if ($sp['village_name']): ?><div style="font-size:.69rem;color:var(--t3)"><?= e($sp['village_name']) ?></div><?php endif; ?>
            <?php $minPrice=null; foreach(['price_30ft','price_40ft','price_60ft','price_80ft','price_100ft','price_highway'] as $pf){ if($sp[$pf]!==null&&($minPrice===null||$sp[$pf]<$minPrice))$minPrice=$sp[$pf]; } ?>
            <?php if ($minPrice): ?><div class="sp-price">From Rs<?= number_format($minPrice,0) ?>/<?= e($sp['price_unit']) ?></div><?php endif; ?>
            <div style="font-size:.66rem;color:var(--gold-s);margin-top:3px"><i class="bx bxs-star" style="color:var(--gold-s)"></i> <?= e($sp['sponsored_label']) ?></div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
      
<!-- Stats -->
    <div class="stats-grid">
      <?php foreach([['[P]','si-blue',$stats['admin_plans']??0,'ALL Plans'],['[D]','si-gold',$stats['dev_plans']??0,'Developer Plans'],['[L]','si-green',$stats['located']??0,'Located'],['[V]','si-gray',$stats['villages']??0,'Villages']] as[$ic,$sc,$sv,$sl]): ?>
      <div class="stat-card"><div class="stat-icon <?= $sc ?>"><?= $ic ?></div><div><div class="stat-val"><?= (int)$sv ?></div><div class="stat-lbl"><?= $sl ?></div></div></div>
      <?php endforeach; ?>
    </div>

    <!-- Tabs -->
    <div class="tab-bar">
      <a class="tab-btn <?= $tab==='admin'?'active':'' ?>" href="index.php?page=home&tab=admin<?= $q?'&q='.urlencode($q):'' ?><?= $vf?'&village='.$vf:'' ?>">All Plans</a>

    </div>

    <!-- Search & filter -->
    <form method="GET" action="index.php">
      <input type="hidden" name="page" value="home">
      <input type="hidden" name="tab" value="<?= e($tab) ?>">
      <div class="search-bar">
        <div class="search-input-wrap"><span class="search-icon-pos">*</span><input class="input" type="text" name="q" value="<?= e($q) ?>" placeholder="Search plan name, aaraji, village..."></div>
        <select class="input" name="village" style="width:auto;min-width:145px"><option value="">All Villages</option><?php foreach ($villagesAll as $v): ?><option value="<?= $v['id'] ?>" <?= $vf==$v['id']?'selected':'' ?>><?= e($v['name']) ?></option><?php endforeach; ?></select>
        <button type="submit" class="btn btn-primary btn-md"><i class="bx bx-search"></i> Search</button>
        <?php if ($q||$vf||$tf): ?><a href="index.php?page=home&tab=<?= e($tab) ?>" class="btn btn-ghost btn-md"><i class="bx bx-x"></i> Clear</a><?php endif; ?>
      </div>
      <div class="filter-row">
        <span style="font-size:.68rem;color:var(--t4);text-transform:uppercase;letter-spacing:.06em">Filter:</span>
        <?php foreach([''  =>'All','image'=>'Images','pdf'=>'PDFs','location'=>'Located'] as $val=>$lbl):
          $url='index.php?page=home&tab='.urlencode($tab).($q?'&q='.urlencode($q):'').($vf?'&village='.$vf:'').($val?'&type='.$val:''); ?>
        <a href="<?= $url ?>" class="chip <?= $tf===$val?'active':'' ?>"><?= $lbl ?></a>
        <?php endforeach; ?>
      </div>
    </form>

<?php if (empty($plans)): ?>
    <div style="text-align:center;padding:3rem 1rem;color:var(--t3)"><div style="font-size:3rem;margin-bottom:.7rem;color:var(--t4)"><i class="bx bx-search-alt"></i></div><h3 style="margin-bottom:.4rem">No <?= $tab==='developer'?'developer':'' ?> plans<?= ($q||$vf||$tf)?' found':' yet' ?></h3><p style="font-size:.82rem"><?= ($q||$vf||$tf)?'Try a different search.':(is_admin()||is_developer()?'Add the first plan.':'No plans available.') ?></p></div>
    <?php else: ?>
    <div class="plans-grid">
      <?php foreach ($plans as $plan):
        $isDev=$plan['is_developer_plan'];$isSpon=$plan['is_sponsored'];
      ?>
      <div class="plan-card <?= $isDev?'dev-card':'' ?> <?= $isSpon?'sponsored':'' ?>">
        <div class="plan-thumb">
          <?php if ($plan['file_type']==='image'&&$plan['file_path']): ?><img src="<?= e($plan['file_path']) ?>" alt="">
          <?php elseif ($plan['file_type']==='pdf'): ?><i class="bx bxs-file-pdf" style="font-size:2.5rem;color:#e53e3e"></i>
          <?php else: ?><span>[MAP]</span><?php endif; ?>
          <?php if ($plan['file_type']): ?><span class="thumb-tag"><?= strtoupper($plan['file_type']) ?></span><?php endif; ?>
          <?php if ($isSpon): ?><span class="thumb-sponsored"><?= e($plan['sponsored_label']) ?></span><?php endif; ?>
        </div>
        <div class="plan-card-body">
          <div class="plan-name" title="<?= e($plan['plan_name']) ?>"><?= e($plan['plan_name']) ?></div>
          <div class="plan-aaraji"># <?= e($plan['aaraji_number']) ?></div>
          <?php if ($plan['village_name']): ?><div class="plan-village"><?= e($plan['village_name']) ?><?= $plan['tehsil']?' - '.e($plan['tehsil']):'' ?></div><?php endif; ?>
          <?php if ($isDev): ?>
          <div class="plan-dev-badge"><i class="bx bx-buildings"></i> <?= e($plan['dev_fullname']?:$plan['dev_username']) ?></div>
          <?php if ($plan['contact_number']): ?><div style="font-size:.71rem;color:var(--t3)">Contact No.: <?= e($plan['contact_number']) ?></div><?php endif; ?>
          <?php else: ?>
          <div class="plan-loc"><?= $plan['google_location']?'Location: '.e(substr($plan['google_location'],0,40)).(strlen($plan['google_location'])>40?'...':''):'<span style="color:var(--t4)">No location</span>' ?></div>
          <?php endif; ?>
        </div>
        <div class="plan-card-footer">
          <a href="index.php?page=view&id=<?= $plan['id'] ?>" class="btn btn-primary btn-sm"><i class="bx bx-show-alt"></i> View</a>
          <?php if ($plan['google_location']): ?><a href="<?= e($plan['google_location']) ?>" target="_blank" rel="noopener" class="btn btn-ghost btn-sm">Map</a><?php endif; ?>
          <?php if (is_admin()||(!$isDev&&is_admin())): ?>
          <a href="index.php?page=edit&id=<?= $plan['id'] ?>" class="btn btn-secondary btn-sm"><i class="bx bx-edit-alt"></i> Edit</a>
          <?php elseif ($isDev&&$plan['created_by']==current_user()['id']): ?>
          <a href="index.php?page=edit&id=<?= $plan['id'] ?>" class="btn btn-secondary btn-sm"><i class="bx bx-edit-alt"></i> Edit</a>
          <?php endif; ?>
          <?php if (is_admin()): ?>
          <form method="POST" style="display:inline" onsubmit="return confirm('Delete this plan?')"><input type="hidden" name="action" value="delete_plan"><input type="hidden" name="plan_id" value="<?= $plan['id'] ?>"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button></form>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php $tp=(int)ceil($total/$pp); if ($tp>1): $base='index.php?page=home&tab='.urlencode($tab).($q?'&q='.urlencode($q):'').($vf?'&village='.$vf:'').($tf?'&type='.$tf:''); ?>
    <div class="pagination">
      <a href="<?= $base ?>&p=<?= max(1,$cp-1) ?>" class="pag-btn <?= $cp<=1?'disabled':'' ?>">Prev</a>
      <?php for ($i=1;$i<=$tp;$i++): ?><a href="<?= $base ?>&p=<?= $i ?>" class="pag-btn <?= $i==$cp?'active':'' ?>"><?= $i ?></a><?php endfor; ?>
      <a href="<?= $base ?>&p=<?= min($tp,$cp+1) ?>" class="pag-btn <?= $cp>=$tp?'disabled':'' ?>">Next</a>
    </div>
    <?php endif; endif; ?>

    <?php /* ============================= ADD / EDIT ============================= */ ?>
    <?php elseif ($page==='add'||$page==='edit'): ?>
    <?php
    $isDeveloperForm = is_developer()&&!is_admin(); // developer-only shows dev fields
    $editIsDev = $editPlan&&$editPlan['is_developer_plan'];
 require __DIR__.'/layout/addplanlayout.php';
    ?>
  <!-- ═══════════════════════════════════════════
         VIEW PLAN DETAIL  (permission-gated)
    ═══════════════════════════════════════════ -->
    <?php elseif ($page === 'view'):
      $isBasic   = can_view_basic();
      $isAdvance = can_view_advance();
      $isDeveloperPlan = !empty($plan['is_developer_plan']);
      require __DIR__.'/layout/plandetaillayout.php';
    ?>

    <?php /* ============================= VILLAGES ============================= */ ?>
    <?php elseif ($page==='villages'): ?>
  <?php
require __DIR__.'/layout/villagelayout.php';
?>

    <?php /* ============================= DLC ============================= */ ?>
    <?php elseif ($page==='dlc'): ?>
    <?php
require __DIR__.'/layout/dlclayout.php';
?>

    <?php /* ============================= APPROVALS ============================= */ ?>
    <?php elseif ($page==='approvals'): ?>
     <?php
require __DIR__.'/layout/approvallayout.php';
?>

    <?php /* ============================= SUBSCRIPTIONS ============================= */ ?>
    <?php elseif ($page==='subscriptions'): ?>
     <?php
require __DIR__.'/layout/subscriptionlayout.php';
?>

    <?php /* ============================= PERMISSIONS ============================= */ ?>
    <?php elseif ($page==='permissions'): ?>
     <?php
require __DIR__.'/layout/permissionlayout.php';
?>

    <?php /* ============================= SETTINGS ============================= */ ?>
    <?php elseif ($page==='settings'): ?>
    <?php
require __DIR__.'/layout/settingslayout.php';
?>

    <?php /* ============================= PROFILE ============================= */ ?>
    <?php elseif ($page==='profile'): ?>
<?php
require __DIR__.'/layout/profilelayout.php';
?>
    <?php elseif ($page==='mutation'): ?>
  <?php require __DIR__.'/layout/mutationlayout.php'; ?>

  <?php elseif ($page==='mutation_apply'): ?>
  <?php require __DIR__.'/layout/mutationformlayout.php'; ?>

  <?php elseif ($page==='mutation_view'): ?>
  <?php require __DIR__.'/layout/mutationviewlayout.php'; ?>

    <?php endif; /* end page switch */ ?>

    </main>
  </div>
</div>
<?php endif; /* end app/login */ ?>
    <script>
var CURRENT_FY = "<?php echo currentFY(); ?>";
</script>
<script src="assets/js/app.js"></script>
</body>
</html>