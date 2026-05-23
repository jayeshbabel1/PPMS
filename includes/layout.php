<?php // includes/layout.php — PMS v4.0 ?>
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
          <div class="form-field"><label>Financial Year <span class="req">*</span></label><input class="input" type="text" name="financial_year" id="dlcFY" required placeholder="e.g. 2024-25" value="<?= e(currentFY()) ?>"></div>
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
  <div class="login-card">
    <h2>Sign in to your account</h2>
    <p class="lsub">Access is restricted to authorised users only.</p>
    <?php if ($err==1): ?><div class="alert alert-danger">Incorrect username or password.</div><?php endif; ?>
    <?php if (($_GET['info']??'')==='reset_sent'): ?><div class="alert alert-success">If that email exists, a reset link has been sent.</div><?php endif; ?>
    <?php if (($_GET['info']??'')==='pw_reset_done'): ?><div class="alert alert-success">Password reset successfully. Please sign in.</div><?php endif; ?>
    <form method="POST" action="index.php">
      <input type="hidden" name="action" value="login">
      <div class="form-field"><label>Username</label><input class="input" type="text" name="username" placeholder="Enter username" autocomplete="username" required autofocus></div>
      <div class="form-field"><label>Password</label><input class="input" type="password" name="password" placeholder="Enter password" autocomplete="current-password" required></div>
      <button type="submit" class="btn btn-primary btn-lg btn-full" style="margin-top:.3rem"><i class="bx bx-log-in"></i> Sign In</button>
    </form>
    <div style="text-align:center;margin-top:.9rem">
      <a href="#" onclick="showForgot();return false" style="font-size:.78rem;color:var(--primary-d)">Forgot password?</a>
    </div>
    <!-- Forgot password form (hidden) -->
    <div id="forgotForm" style="display:none;margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border)">
      <p style="font-size:.8rem;color:var(--t3);margin-bottom:.8rem">Enter your email address and we'll send you a reset link.</p>
      <form method="POST" action="index.php">
        <input type="hidden" name="action" value="forgot_password">
        <div class="form-field"><label>Email Address</label><input class="input" type="email" name="email" placeholder="your@email.com" required></div>
        <button type="submit" class="btn btn-primary btn-md btn-full"><i class="bx bx-envelope"></i> Send Reset Link</button>
      </form>
    </div>
      </div>

  <?php elseif ($page==='reset_password'): ?>
  <div class="login-card">
    <h2>Reset Your Password</h2>
    <?php if (!$resetData): ?>
    <div class="alert alert-danger">This reset link is invalid or has expired. <a href="index.php?page=login">Request a new one</a>.</div>
    <?php elseif ($err==='short'): ?>
    <div class="alert alert-danger">Password must be at least 6 characters.</div>
    <?php elseif ($err==='mismatch'): ?>
    <div class="alert alert-danger">Passwords do not match.</div>
    <?php else: ?>
    <p class="lsub">Hello <strong><?= e($resetData['full_name']?:$resetData['username']) ?></strong>, enter your new password below.</p>
    <form method="POST" action="index.php">
      <input type="hidden" name="action" value="do_reset_password">
      <input type="hidden" name="token" value="<?= e($token) ?>">
      <div class="form-field"><label>New Password</label><input class="input" type="password" name="new_password" placeholder="Min 6 characters" required autofocus></div>
      <div class="form-field"><label>Confirm Password</label><input class="input" type="password" name="confirm_password" placeholder="Repeat new password" required></div>
      <button type="submit" class="btn btn-primary btn-lg btn-full"><i class="bx bx-key"></i> Reset Password</button>
    </form>
    <?php endif; ?>
    <div style="text-align:center;margin-top:.9rem"><a href="index.php?page=login" style="font-size:.78rem"><i class="bx bx-arrow-back"></i> Back to Sign In</a></div>
  </div>
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
          <?php $tt=['home'=>'Dashboard','add'=>is_developer()&&!is_admin()?'Submit Plan':'Add Plan','edit'=>'Edit Plan','view'=>'Plan Detail','villages'=>'Revenue Villages','profile'=>'Profile','dlc'=>'DLC Rates','subscriptions'=>'Users &amp; Subscriptions','settings'=>'Settings','permissions'=>'Permission Matrix','approvals'=>'Plan Approvals'];
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
    $toastMap=['created'=>['success','Plan registered.'],'updated'=>['success','Plan updated.'],'deleted'=>['success','Plan deleted.'],'saved'=>['success','Saved.'],'pwchanged'=>['success','Password changed.'],'dlc_saved'=>['success','DLC rates saved.'],'dlc_deleted'=>['success','DLC record deleted.'],'sub_saved'=>['success','Subscription saved.'],'sub_deleted'=>['success','Subscription deleted.'],'user_created'=>['success','User created.'],'chain_deleted'=>['success','Chain doc removed.'],'upgrade_requested'=>['info','Upgrade request submitted.'],'upgrade_reviewed'=>['success','Request reviewed.'],'imported'=>['success','DLC imported: '.((int)($_GET['ok']??0)).' rows, '.((int)($_GET['skip']??0)).' skipped.'],'reviewed'=>['success','Plan reviewed.'],'dev_submitted'=>['info','Plan submitted for approval. Admin will review.'],'email_sent'=>['success','Test email sent successfully.']];
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
    ?>
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

  <!-- ═══════════════════════════════════════════
         VIEW PLAN DETAIL  (permission-gated)
    ═══════════════════════════════════════════ -->
    <?php elseif ($page === 'view'):
      $isBasic   = can_view_basic();
      $isAdvance = can_view_advance();
      $isDeveloperPlan = !empty($plan['is_developer_plan']);
    ?>

    <?php if (!$isBasic): ?>
    <!-- ── No subscription ── -->
    <div style="max-width:520px;margin:3rem auto;text-align:center">
      <div class="card">
        <div class="card-body" style="padding:3rem 2rem">
          <div style="font-size:3rem;margin-bottom:1rem">🔒</div>
          <h2 style="font-size:1.2rem;color:var(--t1);margin-bottom:.5rem">Subscription Required</h2>
          <p style="font-size:.85rem;color:var(--t3);margin-bottom:1.5rem;line-height:1.7">
            You need an active subscription to view plan details.<br>
            Please contact the administrator to activate your plan.
          </p>
          <a href="index.php" class="btn btn-ghost btn-md">← Back to Dashboard</a>
        </div>
      </div>
    </div>
    <?php else: ?>

    <!-- ── Subscription plan banner ── -->
    <?php if (!is_admin()): ?>
    <?php $curSub = get_active_subscription();
      ?>
    <div style="display:flex;align-items:center;gap:10px;padding:10px 16px;margin-bottom:1.2rem;
                border-radius:8px;border:1px solid <?= $isAdvance ? 'rgba(217,119,6,0.4)' : 'rgba(5,150,105,0.4)' ?>;
                background:<?= $isAdvance ? 'rgba(217,119,6,0.08)' : 'rgba(5,150,105,0.08)' ?>">
      <span style="font-size:1.1rem"><?= $isAdvance ? '⭐' : '✅' ?></span>
      <div>
        <span style="font-size:.78rem;font-weight:700;color:<?= $isAdvance ? 'var(--gold-s)' : '#34d399' ?>">
          <?= $isAdvance ? 'Advance Plan' : 'Basic Plan' ?> Active
        </span>
        <span style="font-size:.72rem;color:var(--t4);margin-left:8px">
          · Expires <?= $curSub ? date('d M Y', strtotime($curSub['end_date'])) : '—' ?>
        </span>
      </div>
      <?php if (!$isAdvance): ?>
      <span style="margin-left:auto;font-size:.72rem;color:var(--t4)">
        Upgrade to Advance for chain docs &amp; DLC access
      </span>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- ── Action bar ── -->
    <div style="display:flex;gap:10px;margin-bottom:1.5rem;align-items:center;flex-wrap:wrap">
      <a href="index.php" class="btn btn-ghost btn-sm"><i class="bx bx-arrow-back"></i> Back</a>
      <?php if (is_admin()): ?>
      <a href="index.php?page=edit&id=<?= $plan['id'] ?>" class="btn btn-secondary btn-sm"><i class="bx bx-edit"></i> Edit</a>
      <?php endif; ?>
      <?php if ($plan['google_location'] && $isBasic): ?>
      <a href="<?= e($plan['google_location']) ?>" target="_blank" rel="noopener" class="btn btn-ghost btn-sm"><i class="bx bx-map-pin"></i> Open in Maps</a>
      <?php endif; ?>
      <?php if ($plan['file_path'] && $isAdvance): ?>
      <a href="<?= e($plan['file_path']) ?>" download="<?= e($plan['file_name']) ?>" class="btn btn-secondary btn-sm"><i class="bx bx-download"></i> Download Plan</a>
      <?php endif; ?>
      <?php if (is_admin()): ?>
      <form method="POST" style="margin-left:auto" onsubmit="return confirm('Permanently delete this plan?')">
        <input type="hidden" name="action" value="delete_plan">
        <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
        <button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i> Delete</button>
      </form>
      <?php endif; ?>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem" class="view-layout">

      <!-- ── Left column ── -->
      <div>

        <!-- Plan image (Basic+) -->
        <?php if ($plan['file_type'] === 'image' && $plan['file_path'] && $isBasic): ?>
        <div class="card" style="margin-bottom:1.2rem;overflow:hidden">
          <img src="<?= e($plan['file_path']) ?>" alt="Plan"
               style="width:100%;display:block;max-height:360px;object-fit:cover">
          <?php if ($isAdvance): ?>
          <div style="padding:10px 14px;border-top:1px solid var(--line);text-align:right">
            <a href="<?= e($plan['file_path']) ?>" download="<?= e($plan['file_name']) ?>"
               class="btn btn-secondary btn-sm"><i class="bx bx-image-download"></i> Download Image</a>
          </div>
          <?php else: ?>
          <div style="padding:8px 14px;border-top:1px solid var(--line);
                      font-size:.72rem;color:var(--t4)">
            🔒 Download requires Advance plan
          </div>
          <?php endif; ?>
        </div>

        <?php elseif ($plan['file_type'] === 'pdf' && $plan['file_path']): ?>
        <div class="card" style="margin-bottom:1.2rem">
          <div style="padding:2rem;text-align:center;background:var(--slate)">
            <div style="font-size:3rem;margin-bottom:.8rem">📄</div>
            <p style="font-size:.85rem;color:var(--t2);margin-bottom:1rem"><?= e($plan['file_name']) ?></p>
            <?php if ($isAdvance): ?>
            <a href="<?= e($plan['file_path']) ?>" target="_blank" class="btn btn-secondary btn-sm"><i class="bx bx-file-pdf"></i> Open PDF</a>
            <a href="<?= e($plan['file_path']) ?>" download="<?= e($plan['file_name']) ?>"
               class="btn btn-ghost btn-sm" style="margin-left:8px"><i class="bx bx-download"></i> Download</a>
            <?php else: ?>
            <div style="font-size:.78rem;color:var(--t4);padding:8px 14px;border-radius:6px;
                        background:var(--navy);border:1px solid var(--line);display:inline-block">
              🔒 PDF access requires Advance plan
            </div>
            <?php endif; ?>
          </div>
        </div>

        <?php elseif (!$plan['file_path']): ?>
        <div class="card" style="margin-bottom:1.2rem">
          <div style="padding:3rem;text-align:center;background:var(--slate)">
            <div style="font-size:3rem;margin-bottom:.6rem"><i class="bx bx-map"></i></div>
            <p style="font-size:.82rem;color:var(--t4)">No file uploaded</p>
          </div>
        </div>
        <?php endif; ?>
        
         <!-- Approved map (dev plans) -->
        <?php if ($isDeveloperPlan&&$plan['approved_map_path']): ?>
        <div class="card" style="margin-bottom:1.1rem;overflow:hidden">
          <div style="background:var(--green-bg);border-bottom:1px solid var(--border);padding:6px 12px;font-size:.72rem;font-weight:700;color:var(--green)">APPROVED PLAN MAP</div>
          <?php if ($plan['approved_map_type']==='image'): ?>
          <img src="<?= e($plan['approved_map_path']) ?>" alt="Approved Map" class="zoomable" onclick="openZoom(this.src)" style="width:100%;display:block;max-height:260px;object-fit:contain;background:var(--surface2)">
          <?php else: ?>
          <div style="padding:1.5rem;text-align:center;background:var(--surface2)"><div style="font-size:2rem;margin-bottom:.5rem">[PDF]</div><p style="font-size:.82rem;margin-bottom:.7rem"><?= e($plan['approved_map_name']) ?></p></div>
          <?php endif; ?>
          <div style="padding:7px 12px;background:var(--green-bg);border-top:1px solid var(--border);text-align:right"><a href="<?= e($plan['approved_map_path']) ?>" download class="btn btn-success btn-sm">Download Approved Map</a></div>
        </div>
        <?php endif; ?>
       
     
        
        
        
        <!-- Map (Basic+) -->
        <?php if ($plan['google_location'] && $isBasic): ?>
        <?php $emb = embedUrl($plan['google_location']); ?>
        <div class="map-embed">
          <?php if ($emb): ?>
          <iframe src="<?= e($emb) ?>" allowfullscreen loading="lazy"
                  referrerpolicy="no-referrer-when-downgrade"></iframe>
          <?php else: ?>
          <div style="height:180px;display:flex;flex-direction:column;align-items:center;
                      justify-content:center;gap:8px;background:var(--slate);border-radius:var(--radius)">
            <span style="font-size:2rem">📍</span>
            <a href="<?= e($plan['google_location']) ?>" target="_blank" rel="noopener"
               style="font-size:.8rem;color:var(--blue-s)"><i class="bx bx-map-pin"></i> Open in Google Maps</a>
          </div>
          <?php endif; ?>
        </div>
        <?php endif; ?>
        
       
        
      </div>

      <!-- ── Right column ── -->
      <div>
        <!-- Plan info (always visible to Basic+) -->
        <div class="card" style="margin-bottom:1.2rem">
          <div class="card-header">
            <h3><i class="bx bx-info-circle"></i> Plan Information</h3>
                  </div>
          <div class="card-body">
            <div style="margin-bottom:1.2rem">
              <div style="font-size:1.15rem;font-weight:700;color:var(--t1);margin-bottom:3px">
                <?= e($plan['plan_name']) ?>
              </div>
            </div>
            <table style="width:100%;border-collapse:collapse">
              <?php $rows = [
                ['Aaraji Number',   $plan['aaraji_number']],
                ['Revenue Village', $plan['village_name'] ?: '—'],
                ['Tehsil',          $plan['tehsil'] ?: '—'],
                ['District',        $plan['district'] ?: '—'],
              ];
              if (is_admin()) $rows = array_merge($rows, [
                ['File Type',       $plan['file_type'] ? strtoupper($plan['file_type']) : '—'],
                ['Registered By',   $plan['created_by_name'] ?: '—'],
                ['Registered On',   date('d M Y', strtotime($plan['created_at']))],
              ]);
              foreach ($rows as [$lbl,$val]): ?>
              <tr>
                <td style="padding:7px 0;border-bottom:1px solid var(--line);
                           font-size:.67rem;font-weight:700;color:var(--t4);
                           text-transform:uppercase;letter-spacing:.08em;width:38%"><?= e($lbl) ?></td>
                <td style="padding:7px 0;border-bottom:1px solid var(--line);
                           font-size:.82rem;color:var(--t1);
                           font-family:'JetBrains Mono',monospace;text-align:right"><?= e($val) ?></td>
              </tr>
              <?php endforeach; ?>
            </table>
            <?php if ($plan['notes'] && is_admin()): ?>
            <div style="margin-top:1rem">
              <div style="font-size:.67rem;font-weight:700;color:var(--t4);
                          text-transform:uppercase;letter-spacing:.08em;margin-bottom:5px">Notes</div>
              <p style="font-size:.83rem;color:var(--t2);line-height:1.7"><?= nl2br(e($plan['notes'])) ?></p>
            </div>
            <?php endif; ?>
          </div>
        </div>
        <!-- DLC Rates card -->
        <?php
        // Developer plans: show DLC to ALL subscribed users (basic+)
        // Admin/Aaraji plans: show DLC to Advance users only
        
        $canSeeDlc = $isDeveloperPlan ? $isBasic : $isAdvance;
        ?>

        <?php if (!empty($planDlc) && $canSeeDlc): ?>
        <div class="card" style="margin-bottom:1.2rem">
          <div class="card-header">
            <div style="display:flex;align-items:center;gap:8px">
              <i class="bx bx-bar-chart-alt-2" style="font-size:1.1rem;color:var(--gold-s)"></i>
              <h3>DLC Rates</h3>
              <?php if ($isDeveloperPlan): ?>
              <span style="font-size:.65rem;background:#e8f0fe;color:#4a5fca;border:1px solid #c0c8f0;
                           border-radius:4px;padding:1px 7px;font-weight:700">Govt. Rate</span>
              <?php endif; ?>
            </div>
            <div style="display:flex;align-items:center;gap:7px;flex-wrap:wrap">
              <span class="badge badge-gold">FY <?= e($planDlc['financial_year']) ?></span>
              <span style="font-size:.68rem;color:var(--t3)">
                <i class="bx bx-calendar"></i>
                Eff. <?= date('d M Y', strtotime($planDlc['effective_from'])) ?>
              </span>
            </div>
          </div>
          <div class="card-body" style="padding:0">
            <?php if ($plan['village_name']): ?>
            <div style="padding:10px 14px 0;font-size:.75rem;color:var(--t3)">
              <i class="bx bx-building-house"></i>
              Government DLC rates for
              <strong style="color:var(--t1)"><?= e($plan['village_name']) ?></strong>
              <?php if (!empty($plan['tehsil'])): ?>
              — <?= e($plan['tehsil']) ?>
              <?php endif; ?>
            </div>
            <?php endif; ?>

            <table style="width:100%;border-collapse:collapse;margin-top:8px">
              <thead>
                <tr style="background:var(--surface2)">
                  <th style="padding:7px 14px;font-size:.65rem;font-weight:700;color:var(--t3);
                             text-align:left;text-transform:uppercase;letter-spacing:.06em;
                             border-bottom:1px solid var(--line)">Road Width</th>
                  <th style="padding:7px 14px;font-size:.65rem;font-weight:700;color:var(--t3);
                             text-align:right;text-transform:uppercase;letter-spacing:.06em;
                             border-bottom:1px solid var(--line)">Rs / sq.m</th>
                  <th style="padding:7px 14px;font-size:.65rem;font-weight:700;color:var(--t3);
                             text-align:right;text-transform:uppercase;letter-spacing:.06em;
                             border-bottom:1px solid var(--line)">Rs / sq.ft</th>
                </tr>
              </thead>
              <tbody>
              <?php
              $dlcRows = [
                ['30 ft Road',   $planDlc['road_30ft']],
                ['40 ft Road',   $planDlc['road_40ft']],
                ['60 ft Road',   $planDlc['road_60ft']],
                ['80 ft Road',   $planDlc['road_80ft']],
                ['100 ft Road',  $planDlc['road_100ft']],
                ['Near Highway', $planDlc['near_highway']],
              ];
              $dlcHasAny = false;
              foreach ($dlcRows as [$lbl, $val]):
                if ($val === null) continue;
                $dlcHasAny = true;
                $sqft = round($val / 10.76, 2);
              ?>
              <tr style="border-bottom:1px solid var(--surface2)">
                <td style="padding:9px 14px;font-size:.8rem;font-weight:500;color:var(--t2)">
                  <i class="bx bx-road" style="font-size:.85rem;color:var(--t3)"></i>
                  <?= e($lbl) ?>
                </td>
                <td style="padding:9px 14px;font-size:.84rem;font-weight:700;
                           color:var(--gold-s);text-align:right;font-family:'JetBrains Mono',monospace">
                  Rs <?= number_format((float)$val, 2) ?>
                </td>
                <td style="padding:9px 14px;font-size:.78rem;font-weight:600;
                           color:var(--t3);text-align:right;font-family:'JetBrains Mono',monospace">
                  Rs <?= number_format($sqft, 2) ?>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if (!$dlcHasAny): ?>
              <tr>
                <td colspan="3" style="padding:14px;text-align:center;font-size:.82rem;color:var(--t4)">
                  No DLC rates entered for this village yet.
                </td>
              </tr>
              <?php endif; ?>
              </tbody>
            </table>

            <div style="padding:8px 14px 10px;font-size:.68rem;color:var(--t4);
                        border-top:1px solid var(--surface2);display:flex;
                        align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px">
              <span><i class="bx bx-info-circle"></i> Sq.ft rate = Sq.m rate ÷ 10.76</span>
              <?php if ($isDeveloperPlan): ?>
              <span style="background:var(--surface2);border:1px solid var(--line);
                           border-radius:4px;padding:2px 8px;font-size:.65rem">
                <i class="bx bx-buildings"></i> Developer Plan
              </span>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <?php elseif (!empty($plan['village_name']) && empty($planDlc) && $canSeeDlc): ?>
        <!-- Village has no DLC data yet -->
        <div class="card" style="margin-bottom:1.2rem">
          <div class="card-header">
            <h3><i class="bx bx-bar-chart-alt-2"></i> DLC Rates</h3>
          </div>
          <div style="padding:1.3rem 1.4rem;display:flex;align-items:center;gap:12px">
            <i class="bx bx-data" style="font-size:1.8rem;color:var(--t4)"></i>
            <div>
              <div style="font-size:.83rem;font-weight:600;color:var(--t2)">
                No DLC rates available for
                <strong><?= e($plan['village_name']) ?></strong>
              </div>
              <div style="font-size:.72rem;color:var(--t4);margin-top:2px">
                <?php if (is_admin()): ?>
                <a href="index.php?page=dlc">Add DLC rates for this village →</a>
                <?php else: ?>
                DLC rates not yet entered by admin for this village.
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <?php elseif (!$canSeeDlc && !$isDeveloperPlan): ?>
        <!-- Locked for basic users on admin plans -->
        <div class="card" style="margin-bottom:1.2rem">
          <div class="card-header">
            <h3><i class="bx bx-bar-chart-alt-2"></i> DLC Rates</h3>
          </div>
          <div style="padding:1.3rem 1.4rem;display:flex;align-items:center;gap:12px;
                      background:var(--surface2)">
            <i class="bx bx-lock-alt" style="font-size:1.6rem;color:var(--t3)"></i>
            <div>
              <div style="font-size:.83rem;font-weight:600;color:var(--t2)">
                Advance Plan Required
              </div>
              <div style="font-size:.72rem;color:var(--t4);margin-top:2px">
                Upgrade to Advance plan to view DLC rates for this village.
              </div>
            </div>
          </div>
        </div>
        <?php endif; ?>
        
       
          <!-- Pricing (dev plans) -->
        <?php if ($isDeveloperPlan): $hasPricing=array_filter(['price_30ft','price_40ft','price_60ft','price_80ft','price_100ft','price_highway'],fn($f)=>$plan[$f]!==null); ?>
        <?php if ($hasPricing): ?>
        <div class="card" style="margin-bottom:1.1rem">
          <div class="card-header"><h3><i class="bx bx-purchase-tag"></i> Pricing (per <?= e($plan['price_unit']) ?>)</h3></div>
          <div class="card-body">
            <table style="width:100%;border-collapse:collapse">
              <?php foreach([['30 ft','price_30ft'],['40 ft','price_40ft'],['60 ft','price_60ft'],['80 ft','price_80ft'],['100 ft','price_100ft'],['Highway','price_highway']] as[$lbl,$fld]):
              if ($plan[$fld]===null) continue; ?>
              <tr><td style="padding:5px 0;border-bottom:1px solid var(--surface2);font-size:.79rem"><?= $lbl ?></td><td style="padding:5px 0;border-bottom:1px solid var(--surface2);text-align:right;font-weight:700;color:var(--primary-d);font-size:.82rem">Rs <?= number_format((float)$plan[$fld],2) ?>/<?= e($plan['price_unit']) ?></td></tr>
              <?php endforeach; ?>
            </table>
          </div>
        </div>
        <?php endif; ?>
        <?php if ($plan['brokerage_rate']): ?>
        <div class="card" style="margin-bottom:1.1rem"><div class="card-header"><h3><i class="bx bx-transfer"></i> Brokerage</h3></div><div class="card-body"><div style="font-size:1.2rem;font-weight:700;color:var(--gold-s);margin-bottom:.4rem"><?= number_format((float)$plan['brokerage_rate'],2) ?>%</div><?php if ($plan['brokerage_notes']): ?><p style="font-size:.81rem;color:var(--t2)"><?= e($plan['brokerage_notes']) ?></p><?php endif; ?></div></div>
        <?php endif; ?>
        	<?php endif; ?>
        
        
        <!-- Chain Documents (Advance+ only) -->
        <?php if ($isAdvance): ?>
        <?php if (!empty($chainDocs)): ?>
        <div class="card">
          <div class="card-header">
            <h3>🔗 Chain Documents</h3>
            <span class="badge badge-blue"><?= count($chainDocs) ?> file<?= count($chainDocs)!==1?'s':'' ?></span>
          </div>
          <div class="card-body" style="display:flex;flex-direction:column;gap:10px">
            <?php foreach ($chainDocs as $idx => $doc): ?>
            <div style="display:flex;align-items:center;gap:12px;background:var(--slate);
                        border:1px solid var(--line);border-radius:10px;padding:11px 14px">
              <div style="width:42px;height:42px;flex-shrink:0;border-radius:7px;
                          background:var(--navy2);border:1px solid var(--line2);
                          display:flex;align-items:center;justify-content:center;
                          font-size:1.2rem;overflow:hidden">
                <?php if ($doc['file_type']==='image'): ?>
                <img src="<?= e($doc['file_path']) ?>" alt=""
                     style="width:100%;height:100%;object-fit:cover;border-radius:6px">
                <?php else: ?>📄<?php endif; ?>
              </div>
              <div style="flex:1;min-width:0">
                <div style="font-size:.82rem;font-weight:600;color:var(--t1);
                            white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                  <?= e($doc['file_name']) ?>
                </div>
                <div style="font-size:.68rem;color:var(--t4);font-family:'JetBrains Mono',monospace;margin-top:2px">
                  <?= strtoupper($doc['file_type']) ?>
                  <?= $doc['file_size'] ? ' · '.round($doc['file_size']/1024).' KB' : '' ?>
                  · Doc #<?= $idx+1 ?>
                </div>
              </div>
              <div style="display:flex;gap:6px;flex-shrink:0">
                <a href="<?= e($doc['file_path']) ?>" target="_blank" class="btn btn-ghost btn-sm"><i class="bx bx-link-external"></i></a>
                <a href="<?= e($doc['file_path']) ?>" download="<?= e($doc['file_name']) ?>"
                   class="btn btn-secondary btn-sm"><i class="bx bx-download"></i></a>
                <?php if (is_admin()): ?>
                <form method="POST" onsubmit="return confirm('Delete this chain document?')">
                  <input type="hidden" name="action"     value="delete_chain_doc">
                  <input type="hidden" name="doc_id"     value="<?= $doc['id'] ?>">
                  <input type="hidden" name="plan_id"    value="<?= $plan['id'] ?>">
                  <input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
                  <button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button>
                </form>
                <?php endif; ?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php else: ?>
        <div class="card">
          <div class="card-header"><h3>🔗 Chain Documents</h3></div>
          <div style="padding:1.5rem;text-align:center;color:var(--t4);font-size:.82rem">
            No chain documents attached.
            <?php if(is_admin()): ?>
            <a href="index.php?page=edit&id=<?= $plan['id'] ?>" style="color:var(--blue-s);margin-left:6px">Add →</a>
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>
        <?php else: ?>
        <!-- Locked chain docs for basic users -->
        <div class="card">
          <div class="card-header"><h3>🔗 Chain Documents</h3></div>
          <div style="padding:1.5rem;display:flex;align-items:center;gap:12px">
            <span style="font-size:1.4rem">🔒</span>
            <div>
              <div style="font-size:.83rem;font-weight:600;color:var(--t2)">Advance Plan Required</div>
              <div style="font-size:.72rem;color:var(--t4)">Upgrade to view &amp; download chain documents</div>
            </div>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; /* end isBasic check */ ?>


    <?php /* ============================= VILLAGES ============================= */ ?>
    <?php elseif ($page==='villages'): ?>
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

    <?php /* ============================= DLC ============================= */ ?>
    <?php elseif ($page==='dlc'): ?>
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

    <?php /* ============================= APPROVALS ============================= */ ?>
    <?php elseif ($page==='approvals'): ?>
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

    <?php /* ============================= SUBSCRIPTIONS ============================= */ ?>
    <?php elseif ($page==='subscriptions'): ?>
    <?php $errMap=['missing'=>'User, dates required.','userdata'=>'Username and password (6+ chars) required.','username'=>'Username: letters, numbers, underscore only.','exists'=>'Username already taken.'];
    if (isset($errMap[$err])): ?><div class="alert alert-danger"><?= $errMap[$err] ?></div><?php endif; ?>

    <div class="stats-grid" style="grid-template-columns:repeat(4,1fr)">
      <?php $ss=$subStats??[]; foreach([['[S]','si-blue',$ss['total']??0,'Total'],['[A]','si-green',$ss['active']??0,'Active'],['[B]','si-gray',$ss['basic_count']??0,'Basic'],['[+]','si-gold',$ss['advance_count']??0,'Advance']] as[$ic,$sc,$sv,$sl]): ?>
      <div class="stat-card"><div class="stat-icon <?= $sc ?>"><?= $ic ?></div><div><div class="stat-val"><?= (int)$sv ?></div><div class="stat-lbl"><?= $sl ?></div></div></div>
      <?php endforeach; ?>
    </div>

    <?php if (!empty($upgradeReqs)): ?>
    <div class="card" style="margin-bottom:1.2rem">
      <div class="card-header"><h3><i class="bx bx-up-arrow-circle"></i> Pending Upgrade Requests</h3><span class="badge badge-red"><?= count($upgradeReqs) ?></span></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:9px">
        <?php foreach ($upgradeReqs as $req): ?>
        <div style="background:var(--gold-bg);border:1px solid #d4b090;border-radius:var(--r);padding:11px 13px">
          <div style="display:flex;align-items:flex-start;gap:9px;flex-wrap:wrap">
            <div style="flex:1;min-width:180px"><strong style="color:var(--t1)"><?= e($req['username']) ?></strong><?= $req['full_name']?' - '.e($req['full_name']):'' ?><div style="font-size:.75rem;color:var(--t2);margin-top:2px">Wants: <strong><?= ucfirst($req['request_plan']) ?></strong> (<?= $req['billing_cycle'] ?>) | Now: <?= ucfirst($req['current_plan']) ?></div><?php if ($req['message']): ?><div style="font-size:.72rem;color:var(--t3)"><?= e($req['message']) ?></div><?php endif; ?></div>
            <form method="POST" action="index.php" style="display:flex;gap:5px;align-items:flex-start;flex-wrap:wrap">
              <input type="hidden" name="action" value="review_upgrade"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><input type="hidden" name="request_id" value="<?= $req['id'] ?>">
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

    <div style="display:grid;grid-template-columns:1fr 1.6fr;gap:1.2rem">
      <div style="display:flex;flex-direction:column;gap:1.1rem">
        <div class="card"><div class="card-header"><h3><i class="bx bx-user-plus"></i> Create User Account</h3></div><div class="card-body">
          <form method="POST" action="index.php"><input type="hidden" name="action" value="save_user"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
            <div class="form-field"><label>Username <span class="req">*</span></label><input class="input" type="text" name="new_username" required placeholder="letters_numbers" autocomplete="off"></div>
            <div class="form-field"><label>Full Name</label><input class="input" type="text" name="new_fullname" placeholder="e.g. Ramesh Patil"></div>
            <div class="form-field"><label>Email</label><input class="input" type="email" name="new_email" placeholder="user@example.com"></div>
            <div class="form-field"><label>Password <span class="req">*</span></label><input class="input" type="password" name="new_password" placeholder="Min 6 characters" autocomplete="new-password"></div>
            <div class="form-field"><label>Role</label>
              <select class="input" name="new_role">
                <option value="viewer">Viewer (subscription-gated)</option>
                <option value="developer">Developer (submit plans)</option>
                <option value="admin">Admin (full access)</option>
              </select>
            </div>
            <button type="submit" class="btn btn-primary btn-md"><i class="bx bx-user-plus"></i> Create Account</button>
          </form>
        </div></div>
        <div class="card"><div class="card-header"><h3><i class="bx bx-credit-card"></i> Assign Subscription</h3></div><div class="card-body">
          <form method="POST" action="index.php"><input type="hidden" name="action" value="save_subscription"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><input type="hidden" name="sub_id" value="0">
            <div class="form-field"><label>User <span class="req">*</span></label><select class="input" name="sub_user_id" required><option value="">-- Select --</option><?php foreach ($viewerUsers as $vu): ?><option value="<?= $vu['id'] ?>"><?= e($vu['username']) ?> (<?= e($vu['role']) ?>)<?= $vu['full_name']?' - '.e($vu['full_name']):'' ?></option><?php endforeach; ?></select></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
              <div class="form-field"><label>Plan</label><select class="input" name="plan_type" id="planType" onchange="updateAmt()"><option value="basic">Basic</option><option value="advance">Advance</option></select></div>
              <div class="form-field"><label>Billing</label><select class="input" name="billing_cycle" id="billCycle" onchange="updateAmt()"><option value="monthly">Monthly</option><option value="yearly">Yearly</option></select></div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
              <div class="form-field"><label>Start <span class="req">*</span></label><input class="input" type="date" name="start_date" id="startDate" value="<?= date('Y-m-d') ?>" onchange="calcEnd()"></div>
              <div class="form-field"><label>End <span class="req">*</span></label><input class="input" type="date" name="end_date" id="endDate"></div>
            </div>
            <div class="form-field"><label>Amount (Rs)</label><input class="input" type="number" name="amount" id="amtField" step="0.01" min="0" placeholder="Auto-filled"></div>
            <div class="form-field"><label>Notes</label><input class="input" type="text" name="notes" placeholder="Payment ref etc."></div>
            <button type="submit" class="btn btn-primary btn-md"><i class="bx bx-credit-card"></i> Assign Subscription</button>
          </form>
        </div></div>
        <div class="card"><div class="card-header"><h3><i class="bx bx-purchase-tag"></i> Pricing Reference</h3></div><div class="card-body">
          <table style="width:100%;border-collapse:collapse">
            <?php foreach([['Basic','Monthly',PLAN_BASIC_MONTHLY],['Basic','Yearly',PLAN_BASIC_YEARLY],['Advance','Monthly',PLAN_ADVANCE_MONTHLY],['Advance','Yearly',PLAN_ADVANCE_YEARLY]] as[$pl,$cy,$am]): ?>
            <tr><td style="padding:5px 0;border-bottom:1px solid var(--surface2);font-size:.8rem"><?= $pl ?></td><td style="font-size:.77rem;color:var(--t3)"><?= $cy ?></td><td style="text-align:right;font-weight:700;color:var(--gold-s);font-size:.82rem">Rs<?= number_format($am) ?></td></tr>
            <?php endforeach; ?>
          </table>
        </div></div>
      </div>
      <div style="display:flex;flex-direction:column;gap:1.1rem">
        <div class="card"><div class="card-header"><h3><i class="bx bx-group"></i> All Users</h3><span class="badge badge-blue"><?= count($allUsers) ?></span></div>
          <div class="table-wrap"><table>
            <thead><tr><th>User</th><th>Role</th><th>Plan</th><th>Expires</th></tr></thead>
            <tbody><?php foreach ($allUsers as $au): ?>
              <tr><td><strong style="color:var(--t1)"><?= e($au['username']) ?></strong><?= $au['full_name']?'<br><small style="color:var(--t3)">'.e($au['full_name']).'</small>':'' ?><?= $au['email']?'<br><small style="color:var(--t4)">'.e($au['email']).'</small>':'' ?></td>
              <td><span class="badge <?= $au['role']==='admin'?'badge-blue':($au['role']==='developer'?'badge-dev':'badge-gray') ?>"><?= ucfirst($au['role']) ?></span></td>
              <td><?php if ($au['role']==='admin'): ?><span class="badge badge-blue">Admin</span><?php elseif ($au['role']==='developer'): ?><span class="badge badge-dev">Developer</span><?php elseif ($au['plan_type']&&$au['sub_active']): ?><span class="badge <?= $au['plan_type']==='advance'?'badge-gold':'badge-green' ?>"><?= ucfirst($au['plan_type']) ?></span><?php else: ?><span class="badge badge-gray">None</span><?php endif; ?></td>
              <td style="font-size:.73rem;color:var(--t3)"><?= $au['end_date']?date('d M Y',strtotime($au['end_date'])):'--' ?></td></tr>
            <?php endforeach; ?></tbody>
          </table></div>
        </div>
        <div class="card"><div class="card-header"><h3><i class="bx bx-history"></i> Subscription History</h3><span class="badge badge-blue"><?= count($allSubs) ?></span></div>
          <?php if (empty($allSubs)): ?><div style="padding:1.2rem;text-align:center;color:var(--t3);font-size:.82rem">No subscriptions yet.</div>
          <?php else: ?><div class="table-wrap"><table>
            <thead><tr><th>User</th><th>Plan</th><th>Cycle</th><th>End</th><th>Rs</th><th>Status</th><th></th></tr></thead>
            <tbody><?php foreach ($allSubs as $sub): $isExp=strtotime($sub['end_date'])<time(); $act=$sub['is_active']&&!$isExp; ?>
              <tr><td><strong><?= e($sub['username']) ?></strong></td>
              <td><span class="badge <?= $sub['plan_type']==='advance'?'badge-gold':'badge-green' ?>"><?= ucfirst($sub['plan_type']) ?></span></td>
              <td style="font-size:.74rem;color:var(--t3)"><?= ucfirst($sub['billing_cycle']) ?></td>
              <td style="font-size:.73rem;color:<?= $isExp?'var(--red)':'var(--t2)' ?>"><?= date('d M Y',strtotime($sub['end_date'])) ?></td>
              <td style="font-weight:700;color:var(--gold-s);font-size:.75rem"><?= $sub['amount']?'Rs'.number_format((float)$sub['amount'],0):'--' ?></td>
              <td><?php if ($act): ?><span class="badge badge-green">Active</span><?php elseif ($isExp): ?><span class="badge badge-gray">Expired</span><?php else: ?><span class="badge badge-red"><i class="bx bx-toggle-left"></i> Off</span><?php endif; ?></td>
              <td><form method="POST" style="display:inline"><input type="hidden" name="action" value="toggle_subscription"><input type="hidden" name="sub_id" value="<?= $sub['id'] ?>"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><button type="submit" class="btn btn-secondary btn-sm"><?= $sub['is_active']?'Off':'On' ?></button></form>
              <form method="POST" style="display:inline" onsubmit="return confirm('Delete?')"><input type="hidden" name="action" value="delete_subscription"><input type="hidden" name="sub_id" value="<?= $sub['id'] ?>"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button></form></td></tr>
            <?php endforeach; ?></tbody>
          </table></div><?php endif; ?>
        </div>
      </div>
    </div>
    <script>
    var prices={basic:{monthly:<?= PLAN_BASIC_MONTHLY ?>,yearly:<?= PLAN_BASIC_YEARLY ?>},advance:{monthly:<?= PLAN_ADVANCE_MONTHLY ?>,yearly:<?= PLAN_ADVANCE_YEARLY ?>}};
    function updateAmt(){var p=document.getElementById('planType').value,c=document.getElementById('billCycle').value,a=document.getElementById('amtField');if(a)a.value=prices[p][c];calcEnd();}
    function calcEnd(){var s=document.getElementById('startDate').value,c=document.getElementById('billCycle').value,e=document.getElementById('endDate');if(!s||!e)return;var d=new Date(s);c==='yearly'?d.setFullYear(d.getFullYear()+1):d.setMonth(d.getMonth()+1);d.setDate(d.getDate()-1);e.value=d.toISOString().split('T')[0];}
    updateAmt();
    </script>

    <?php /* ============================= PERMISSIONS ============================= */ ?>
    <?php elseif ($page==='permissions'): ?>
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

    <?php /* ============================= SETTINGS ============================= */ ?>
    <?php elseif ($page==='settings'): ?>
    <?php if ($msg==='saved'): ?><div class="alert alert-success">Settings saved successfully.</div><?php endif; ?>
    <?php if ($msg==='email_sent'): ?><div class="alert alert-success">Test email sent successfully!</div><?php endif; ?>
    <?php if ($err): ?><div class="alert alert-danger">Error: <?= e($err) ?></div><?php endif; ?>

    <form method="POST" action="index.php">
      <input type="hidden" name="action" value="save_settings">
      <input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">

      <!-- Marquee -->
      <div class="card" style="margin-bottom:1.1rem">
        <div class="card-header"><h3><i class="bx bx-broadcast"></i> Scrolling Announcement (Marquee)</h3></div>
        <div class="card-body">
          <div class="form-field"><label>Marquee Text</label><textarea class="input" name="marquee_text" placeholder="Enter announcement text..."><?= e($S['marquee_text']??'') ?></textarea></div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:.9rem">
            <div class="form-field"><label>Enable Marquee</label>
              <div style="display:flex;align-items:center;gap:10px;padding:8px 0"><input type="checkbox" name="marquee_enabled" value="1" <?= ($S['marquee_enabled']??'1')==='1'?'checked':'' ?> style="width:16px;height:16px;cursor:pointer;accent-color:var(--primary)"><label style="font-size:.83rem;color:var(--t2);text-transform:none;letter-spacing:0;cursor:pointer">Show marquee on all pages</label></div>
            </div>
            <div class="form-field"><label>Scroll Speed (seconds)</label><input class="input" type="number" name="marquee_speed" min="10" max="300" step="5" value="<?= e($S['marquee_speed']??'60') ?>" placeholder="60"><small style="color:var(--t4);font-size:.7rem">Lower = faster. Default: 60</small></div>
          </div>
          <div style="background:var(--primary);color:var(--btn-text);padding:5px 0;border-radius:var(--r);overflow:hidden;margin-top:.5rem"><span style="display:inline-block;padding:0 16px;font-size:.8rem"><?= e($S['marquee_text']??'Preview text') ?></span></div>
        </div>
      </div>

      <!-- Footer -->
      <div class="card" style="margin-bottom:1.1rem">
        <div class="card-header"><h3><i class="bx bx-text"></i> Footer Text</h3></div>
        <div class="card-body">
          <div class="form-field"><label>Footer Text (shown in sidebar below Sign Out)</label><textarea class="input" name="footer_text" placeholder="PMS By Mingosoft Technologies &amp;copy; 2025"><?= e($S['footer_text']??'') ?></textarea><small style="color:var(--t4);font-size:.7rem">HTML allowed (e.g. &amp;amp;copy; for copyright)</small></div>
        </div>
      </div>

      <!-- Email -->
      <div class="card" style="margin-bottom:1.1rem">
        <div class="card-header"><h3><i class="bx bx-envelope"></i> Email Configuration (SMTP)</h3></div>
        <div class="card-body">
          <div class="form-grid">
            <div class="form-field"><label>Mail Method</label><select class="input" name="mail_method"><option value="smtp" <?= ($S['mail_method']??'')==='smtp'?'selected':'' ?>>SMTP</option><option value="php" <?= ($S['mail_method']??'')==='php'?'selected':'' ?>>PHP mail()</option></select></div>
            <div class="form-field"><label>SMTP Host</label><input class="input" type="text" name="mail_host" placeholder="smtp.gmail.com" value="<?= e($S['mail_host']??'') ?>"></div>
            <div class="form-field"><label>SMTP Port</label><input class="input" type="number" name="mail_port" placeholder="587" value="<?= e($S['mail_port']??'587') ?>"><small style="color:var(--t4);font-size:.7rem">587 (TLS) or 465 (SSL)</small></div>
            <div class="form-field"><label>SMTP Username</label><input class="input" type="text" name="mail_user" placeholder="user@gmail.com" value="<?= e($S['mail_user']??'') ?>"></div>
            <div class="form-field"><label>SMTP Password</label><input class="input" type="password" name="mail_pass" placeholder="App password" value="<?= e($S['mail_pass']??'') ?>"></div>
            <div class="form-field"><label>From Email</label><input class="input" type="email" name="mail_from" placeholder="noreply@yoursite.com" value="<?= e($S['mail_from']??'') ?>"></div>
            <div class="form-field"><label>From Name</label><input class="input" type="text" name="mail_from_name" placeholder="PMS System" value="<?= e($S['mail_from_name']??'PMS System') ?>"></div>
            <div class="form-field"><label>Admin Email (for error notifications)</label><input class="input" type="email" name="mail_admin_email" placeholder="admin@yoursite.com" value="<?= e($S['mail_admin_email']??'') ?>"></div>
          </div>
          <div class="form-field"><label>Error Notification</label>
            <div style="display:flex;align-items:center;gap:10px;padding:8px 0"><input type="checkbox" name="mail_error_notify" value="1" <?= ($S['mail_error_notify']??'0')==='1'?'checked':'' ?> style="width:16px;height:16px;cursor:pointer;accent-color:var(--primary)"><label style="font-size:.83rem;color:var(--t2);text-transform:none;letter-spacing:0;cursor:pointer">Send email to admin when PHP errors occur</label></div>
          </div>
        </div>
      </div>

      <!-- Theme -->
      <div class="card" style="margin-bottom:1.1rem">
        <div class="card-header"><h3><i class="bx bx-palette"></i> Color Theme Customization</h3></div>
        <div class="card-body">
          <div class="alert alert-info" style="font-size:.77rem">Changes apply immediately after saving. Default theme: #81A6C6 / #F3E3D0 / #D2C4B4</div>
          <?php $themeFields=[
            ['theme_primary',   'Primary / Button Color (accent)'],
            ['theme_bg',        'Page Background Color'],
            ['theme_surface',   'Card / Surface Color (white areas)'],
            ['theme_border',    'Border / Divider Color'],
            ['theme_btn_text',  'Button Text Color'],
            ['theme_heading',   'Heading Text Color'],
            ['theme_text',      'Body Text Color'],
            ['theme_sidebar_bg','Sidebar Background Color'],
            ['theme_topbar_bg', 'Top Bar Background Color'],
          ];
          foreach ($themeFields as [$key,$label]):
            $val=$S[$key]??'#81A6C6';
          ?>
          <div class="color-row">
            <label><?= $label ?></label>
            <input type="color" value="<?= e($val) ?>" oninput="document.getElementById('<?= $key ?>_text').value=this.value" onchange="document.getElementById('<?= $key ?>_text').value=this.value">
            <input class="input" type="text" name="<?= $key ?>" id="<?= $key ?>_text" value="<?= e($val) ?>" placeholder="#81A6C6" maxlength="7" oninput="syncColor(this)" style="max-width:120px">
          </div>
          <?php endforeach; ?>
          <div style="margin-top:.8rem;display:flex;gap:9px;flex-wrap:wrap">
            <button type="button" onclick="resetTheme()" class="btn btn-ghost btn-sm"><i class="bx bx-reset"></i> Reset to Default</button>
          </div>
        </div>
      </div>

      <div style="display:flex;gap:9px;flex-wrap:wrap">
        <button type="submit" class="btn btn-primary btn-md"><i class="bx bx-save"></i> Save All Settings</button>
        <a href="index.php" class="btn btn-ghost btn-md"><i class="bx bx-x"></i> Cancel</a>
      </div>
    </form>

    <!-- Test email button (separate form) -->
    <div style="margin-top:1rem">
      <form method="POST" action="index.php" style="display:inline">
        <input type="hidden" name="action" value="test_email">
        <input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
        <button type="submit" class="btn btn-secondary btn-sm"><i class="bx bx-envelope"></i> Send Test Email</button>
      </form>
      <span style="font-size:.74rem;color:var(--t3);margin-left:8px">Sends test email to the admin email address above</span>
    </div>

    <?php /* ============================= PROFILE ============================= */ ?>
    <?php elseif ($page==='profile'): ?>
    <?php $pwErrMap=['wrongpw'=>'Current password incorrect.','short'=>'New password must be 6+ characters.','mismatch'=>'Passwords do not match.'];
    if ($msg==='pwchanged'): ?><div class="alert alert-success">Password changed successfully.</div>
    <?php elseif (isset($pwErrMap[$err])): ?><div class="alert alert-danger"><?= $pwErrMap[$err] ?></div><?php endif; ?>
    <?php if ($msg==='upgrade_requested'): ?><div class="alert alert-success">Upgrade request submitted. Admin will review shortly.</div>
    <?php elseif ($err==='already_requested'): ?><div class="alert alert-warning">You already have a pending upgrade request.</div><?php endif; ?>

    <div class="two-col">
      <div style="display:flex;flex-direction:column;gap:1.1rem">
        <div class="card"><div class="card-header"><h3><i class="bx bx-user"></i> Account Info</h3></div><div class="card-body">
          <div style="display:flex;align-items:center;gap:11px;margin-bottom:1.1rem">
            <div style="width:44px;height:44px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:var(--btn-text);font-weight:700;flex-shrink:0"><?= strtoupper(substr($user['username'],0,1)) ?></div>
            <div><div style="font-size:.95rem;font-weight:700;color:var(--t1)"><?= e($user['full_name']?:$user['username']) ?></div><div style="font-size:.73rem;color:var(--t3)"><?= e($user['username']) ?> - <?= ucfirst($user['role']) ?><?= $userEmail?' - '.e($userEmail):'' ?></div></div>
          </div>
          <table style="width:100%;border-collapse:collapse">
            <?php foreach([['Username',$user['username']],['Full Name',$user['full_name']?:'--'],['Email',$userEmail?:'--'],['Role',ucfirst($user['role'])]] as[$l,$v]): ?>
            <tr><td style="padding:5px 0;border-bottom:1px solid var(--surface2);font-size:.67rem;font-weight:700;color:var(--t3);text-transform:uppercase"><?= $l ?></td><td style="padding:5px 0;border-bottom:1px solid var(--surface2);font-size:.81rem;color:var(--t1);text-align:right"><?= e($v) ?></td></tr>
            <?php endforeach; ?>
          </table>
        </div></div>
        <div class="card"><div class="card-header"><h3><i class="bx bx-lock-alt"></i> Change Password</h3></div><div class="card-body">
          <form method="POST" action="index.php"><input type="hidden" name="action" value="change_password"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
            <div class="form-field"><label>Current Password</label><input class="input" type="password" name="current_password" required placeholder="Current password"></div>
            <div class="form-field"><label>New Password</label><input class="input" type="password" name="new_password" required placeholder="Min 6 characters"></div>
            <div class="form-field"><label>Confirm Password</label><input class="input" type="password" name="confirm_password" required placeholder="Repeat new password"></div>
            <button type="submit" class="btn btn-primary btn-md"><i class="bx bx-lock-alt"></i> Update Password</button>
          </form>
        </div></div>
      </div>
      <div style="display:flex;flex-direction:column;gap:1.1rem">
        <div class="card"><div class="card-header"><h3><i class="bx bx-credit-card"></i> My Subscription</h3></div><div class="card-body">
          <?php if ($mySub): ?>
          <div style="text-align:center;padding:.9rem 0;margin-bottom:1rem"><div style="font-size:2.2rem;margin-bottom:.4rem">[SUB]</div><div style="font-size:1.1rem;font-weight:700;color:var(--t1)"><?= ucfirst($mySub['plan_type']) ?> Plan</div><div style="font-size:.77rem;color:var(--t3);margin-top:2px"><?= ucfirst($mySub['billing_cycle']) ?> - Expires <?= date('d M Y',strtotime($mySub['end_date'])) ?></div></div>
          <?php else: ?><div style="text-align:center;padding:1.2rem;color:var(--t3)"><div style="font-size:2rem;margin-bottom:.4rem">[--]</div><div style="font-size:.84rem">No active subscription</div></div><?php endif; ?>
          <?php if (!is_admin()&&!is_developer()&&(!$mySub||$mySub['plan_type']==='basic')): ?>
          <div class="divider"></div>
          <h4 style="font-size:.84rem;margin-bottom:.8rem;color:var(--t1)">Request Upgrade</h4>
          <form method="POST" action="index.php"><input type="hidden" name="action" value="request_upgrade"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
              <div class="form-field"><label>Plan</label><select class="input" name="request_plan"><?php if (!$mySub): ?><option value="basic">Basic</option><?php endif; ?><option value="advance">Advance</option></select></div>
              <div class="form-field"><label>Billing</label><select class="input" name="billing_cycle"><option value="monthly">Monthly</option><option value="yearly">Yearly</option></select></div>
            </div>
            <div class="form-field"><label>Message (optional)</label><textarea class="input" name="message" style="min-height:50px" placeholder="Notes for admin..."></textarea></div>
            <button type="submit" class="btn btn-primary btn-md"><i class="bx bx-send"></i> Submit Request</button>
          </form>
          <?php endif; ?>
        </div></div>
        <?php if (!empty($myRequests)): ?>
        <div class="card"><div class="card-header"><h3><i class="bx bx-up-arrow-circle"></i> My Upgrade Requests</h3></div><div class="card-body" style="display:flex;flex-direction:column;gap:7px">
          <?php foreach ($myRequests as $req): ?>
          <div style="background:var(--surface2);border:1px solid var(--border);border-radius:var(--r);padding:9px 11px;display:flex;align-items:center;justify-content:space-between;gap:9px;flex-wrap:wrap">
            <div><div style="font-size:.81rem;font-weight:600;color:var(--t1)"><?= ucfirst($req['request_plan']) ?> - <?= ucfirst($req['billing_cycle']) ?></div><div style="font-size:.69rem;color:var(--t3)"><?= date('d M Y',strtotime($req['created_at'])) ?></div><?php if ($req['admin_note']): ?><div style="font-size:.71rem;color:var(--t2);margin-top:1px">Note: <?= e($req['admin_note']) ?></div><?php endif; ?></div>
            <span class="badge <?= $req['status']==='approved'?'badge-green':($req['status']==='rejected'?'badge-red':'badge-gold') ?>"><?= ucfirst($req['status']) ?></span>
          </div>
          <?php endforeach; ?>
        </div></div>
        <?php endif; ?>
      </div>
    </div>

    <?php endif; /* end page switch */ ?>

    </main>
  </div>
</div>
<?php endif; /* end app/login */ ?>
<script src="assets/js/app.js"></script>
</body>
</html>
