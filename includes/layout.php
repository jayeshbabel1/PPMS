<?php // includes/layout.php — PMS v3.0 ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
<title><?= e(APP_BRAND) ?><?= $page!=='home'?' — '.ucfirst($page):'' ?></title>
<style>
/* ============================================================
   DESIGN TOKENS — Light Professional Theme
   Colors: #81A6C6  #AACDDC  #F3E3D0  #D2C4B4
============================================================ */
:root{
  --c1:#81A6C6; --c2:#AACDDC; --c3:#F3E3D0; --c4:#D2C4B4;
  --bg:#F3E3D0; --bg2:#FAF5EF; --surface:#FFFFFF; --surface2:#F8F2EC; --surface3:#EFE8DF;
  --border:#D2C4B4; --border2:#C4B4A0;
  --primary:#81A6C6; --primary-h:#6B94B8; --primary-d:#5A82A6; --primary-bg:#EBF2F8;
  --sec:#AACDDC; --sec-bg:#E8F4F8;
  --gold:#C8956C; --gold-bg:#FBF0E8; --gold-s:#A87040;
  --green:#5A9E6F; --green-bg:#EAF5EE;
  --red:#C05050; --red-bg:#FAEAEA;
  --t1:#2C3A4A; --t2:#4A5E70; --t3:#7A8F9E; --t4:#B0BEC8;
  --sh:0 2px 8px rgba(44,58,74,.10); --sh-md:0 4px 20px rgba(44,58,74,.13); --sh-lg:0 8px 40px rgba(44,58,74,.16);
  --r:8px; --r-sm:5px; --r-lg:12px; --r-xl:18px;
  --sidebar:240px; --hdr:58px;
}
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
html{scroll-behavior:smooth;}
body{background:var(--bg);color:var(--t2);font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.6;min-height:100vh;}
a{color:var(--primary-d);text-decoration:none;}
a:hover{text-decoration:underline;}
h1,h2,h3,h4{color:var(--t1);}

/* ── Marquee bar ── */
.marquee-bar{background:var(--primary);color:#fff;padding:5px 0;overflow:hidden;white-space:nowrap;font-size:.8rem;font-weight:600;letter-spacing:.02em;}
.marquee-inner{display:inline-block;animation:marqueeScroll linear infinite;}
@keyframes marqueeScroll{0%{transform:translateX(100vw);}100%{transform:translateX(-100%);}}

/* ── Buttons — text-based for browser compat ── */
.btn{display:inline-flex;align-items:center;justify-content:center;gap:5px;border:1px solid transparent;border-radius:var(--r);font-family:Arial,Helvetica,sans-serif;font-weight:600;cursor:pointer;transition:all .15s;white-space:nowrap;text-decoration:none;line-height:1.3;}
.btn-sm{font-size:.76rem;padding:5px 12px;}
.btn-md{font-size:.84rem;padding:8px 16px;}
.btn-lg{font-size:.9rem;padding:11px 22px;}
.btn-full{width:100%;justify-content:center;}
.btn-primary{background:var(--primary);color:#fff;border-color:var(--primary);}
.btn-primary:hover{background:var(--primary-h);border-color:var(--primary-h);color:#fff;text-decoration:none;}
.btn-secondary{background:var(--surface2);color:var(--t2);border-color:var(--border);}
.btn-secondary:hover{background:var(--border);color:var(--t1);text-decoration:none;}
.btn-danger{background:var(--red-bg);color:var(--red);border-color:#e0a0a0;}
.btn-danger:hover{background:var(--red);color:#fff;text-decoration:none;}
.btn-ghost{background:transparent;color:var(--t3);border-color:var(--border);}
.btn-ghost:hover{background:var(--surface2);color:var(--t1);text-decoration:none;}
.btn-success{background:var(--green-bg);color:var(--green);border-color:#9acc9a;}
.btn-success:hover{background:var(--green);color:#fff;text-decoration:none;}
.btn-icon{background:var(--surface2);border:1px solid var(--border);color:var(--t2);padding:5px 8px;border-radius:var(--r-sm);font-size:.75rem;cursor:pointer;transition:all .15s;}
.btn-icon:hover{background:var(--primary-bg);border-color:var(--primary);color:var(--primary);}

/* ── Inputs ── */
.input,select.input,textarea.input{background:var(--surface);border:1px solid var(--border);border-radius:var(--r);padding:9px 12px;color:var(--t1);font-family:Arial,Helvetica,sans-serif;font-size:.86rem;outline:none;transition:border-color .15s,box-shadow .15s;width:100%;}
.input::placeholder{color:var(--t4);}
.input:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(129,166,198,.2);}
textarea.input{resize:vertical;min-height:70px;}
select.input{cursor:pointer;}

/* ── Alerts ── */
.alert{border-radius:var(--r);padding:10px 14px;font-size:.81rem;display:flex;align-items:flex-start;gap:8px;border:1px solid;margin-bottom:1rem;}
.alert-danger{background:var(--red-bg);border-color:#e0a0a0;color:var(--red);}
.alert-success{background:var(--green-bg);border-color:#9acc9a;color:var(--green);}
.alert-info{background:var(--primary-bg);border-color:#a0bcd4;color:var(--primary-d);}
.alert-warning{background:var(--gold-bg);border-color:#d4b090;color:var(--gold-s);}

/* ── Badges ── */
.badge{display:inline-block;border-radius:4px;padding:2px 8px;font-size:.65rem;font-weight:700;letter-spacing:.04em;text-transform:uppercase;}
.badge-blue{background:var(--primary-bg);color:var(--primary-d);border:1px solid #a0bcd4;}
.badge-gold{background:var(--gold-bg);color:var(--gold-s);border:1px solid #d4b090;}
.badge-green{background:var(--green-bg);color:var(--green);border:1px solid #9acc9a;}
.badge-red{background:var(--red-bg);color:var(--red);border:1px solid #e0a0a0;}
.badge-gray{background:var(--surface2);color:var(--t3);border:1px solid var(--border);}

/* ── Cards ── */
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden;box-shadow:var(--sh);}
.card-header{padding:.9rem 1.2rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:1rem;background:var(--surface2);}
.card-header h3{font-size:.9rem;font-weight:700;color:var(--t1);}
.card-body{padding:1.2rem;}

/* ── Form groups ── */
.form-field{display:flex;flex-direction:column;gap:5px;margin-bottom:.85rem;}
.form-field label{font-size:.7rem;font-weight:700;color:var(--t3);letter-spacing:.06em;text-transform:uppercase;}
.form-field label .req{color:var(--primary);margin-left:2px;}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:.9rem;}
.fg-full{grid-column:1/-1;}

/* ── Table ── */
.table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch;}
table{width:100%;border-collapse:collapse;}
thead th{padding:9px 11px;text-align:left;font-size:.68rem;font-weight:700;color:var(--t3);text-transform:uppercase;letter-spacing:.06em;border-bottom:2px solid var(--border);background:var(--surface2);white-space:nowrap;}
tbody td{padding:9px 11px;font-size:.82rem;color:var(--t2);border-bottom:1px solid var(--surface2);vertical-align:middle;}
tbody tr:hover td{background:var(--bg2);}
tbody tr:last-child td{border-bottom:none;}

/* ── Stats ── */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:12px;margin-bottom:1.5rem;}
.stat-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--r-lg);padding:1rem 1.1rem;display:flex;align-items:center;gap:11px;box-shadow:var(--sh);}
.stat-icon{width:38px;height:38px;flex-shrink:0;border-radius:var(--r);display:flex;align-items:center;justify-content:center;font-size:1.1rem;}
.si-blue{background:var(--primary-bg);}
.si-gold{background:var(--gold-bg);}
.si-green{background:var(--green-bg);}
.si-gray{background:var(--surface2);}
.stat-val{font-size:1.35rem;font-weight:700;color:var(--t1);line-height:1;}
.stat-lbl{font-size:.65rem;color:var(--t3);text-transform:uppercase;letter-spacing:.05em;margin-top:2px;}

/* ── Plans grid ── */
.plans-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(270px,1fr));gap:14px;}
.plan-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden;transition:all .15s;display:flex;flex-direction:column;box-shadow:var(--sh);}
.plan-card:hover{border-color:var(--primary);transform:translateY(-2px);box-shadow:var(--sh-md);}
.plan-thumb{height:125px;background:var(--surface2);display:flex;align-items:center;justify-content:center;font-size:1.8rem;border-bottom:1px solid var(--border);position:relative;overflow:hidden;}
.plan-thumb img{width:100%;height:100%;object-fit:cover;}
.thumb-tag{position:absolute;bottom:6px;left:6px;background:rgba(44,58,74,.75);border-radius:3px;padding:2px 6px;font-size:.6rem;font-weight:700;text-transform:uppercase;color:#fff;}
.plan-card-body{padding:11px 13px;flex:1;display:flex;flex-direction:column;}
.plan-name{font-size:.88rem;font-weight:700;color:var(--t1);margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.plan-aaraji{font-size:.72rem;color:var(--primary-d);margin-bottom:6px;font-weight:600;}
.plan-village{display:inline-flex;align-items:center;gap:4px;background:var(--gold-bg);border:1px solid #d4b090;border-radius:4px;padding:2px 7px;font-size:.67rem;font-weight:600;color:var(--gold-s);margin-bottom:6px;}
.plan-loc{font-size:.73rem;color:var(--t3);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:auto;}
.plan-card-footer{padding:8px 13px;border-top:1px solid var(--border);display:flex;gap:5px;flex-wrap:wrap;}

/* ── Filter chips ── */
.filter-row{display:flex;gap:7px;flex-wrap:wrap;align-items:center;margin-bottom:1.1rem;}
.chip{background:var(--surface);border:1px solid var(--border);border-radius:100px;padding:4px 12px;font-size:.74rem;font-weight:500;color:var(--t3);cursor:pointer;transition:all .15s;text-decoration:none;display:inline-block;}
.chip:hover{color:var(--t1);border-color:var(--border2);text-decoration:none;}
.chip.active{background:var(--primary-bg);border-color:#a0bcd4;color:var(--primary-d);}

/* ── Pagination ── */
.pagination{display:flex;align-items:center;gap:5px;margin-top:1.5rem;flex-wrap:wrap;}
.pag-btn{background:var(--surface);border:1px solid var(--border);color:var(--t3);border-radius:var(--r-sm);padding:5px 11px;font-size:.77rem;font-weight:500;text-decoration:none;transition:all .15s;display:inline-block;}
.pag-btn:hover{background:var(--primary-bg);border-color:var(--primary);color:var(--primary);text-decoration:none;}
.pag-btn.active{background:var(--primary);border-color:var(--primary);color:#fff;}
.pag-btn.disabled{opacity:.4;pointer-events:none;}

/* ── Upload ── */
.upload-zone{border:2px dashed var(--border2);border-radius:var(--r-lg);padding:1.4rem;text-align:center;cursor:pointer;background:var(--surface2);position:relative;transition:all .15s;}
.upload-zone:hover,.upload-zone.drag-over{border-color:var(--primary);background:var(--primary-bg);}
.upload-zone input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;}
.upload-zone p{font-size:.82rem;color:var(--t3);}
.upload-zone small{font-size:.7rem;color:var(--t4);}
.upload-preview{margin-top:8px;display:none;align-items:center;gap:8px;background:var(--primary-bg);border:1px solid #a0bcd4;border-radius:var(--r);padding:7px 11px;font-size:.79rem;color:var(--primary-d);}
.upload-preview.show{display:flex;}

/* ── Sidebar ── */
.sidebar{width:var(--sidebar);background:var(--surface);border-right:1px solid var(--border);display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;z-index:200;transition:transform .25s ease;overflow-y:auto;box-shadow:var(--sh);}
.sidebar-brand{padding:1rem;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px;min-height:var(--hdr);}
.brand-icon{width:34px;height:34px;flex-shrink:0;background:var(--primary);border-radius:var(--r);display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#fff;}
.brand-text h2{font-size:.88rem;font-weight:700;color:var(--t1);line-height:1.25;}
.brand-text span{font-size:.6rem;color:var(--t3);text-transform:uppercase;letter-spacing:.06em;}
.sidebar-nav{flex:1;padding:.7rem .5rem;}
.nav-section{font-size:.6rem;font-weight:700;color:var(--t4);letter-spacing:.1em;text-transform:uppercase;padding:0 .6rem;margin:.7rem 0 .3rem;}
.nav-item{display:flex;align-items:center;gap:8px;padding:7px 10px;border-radius:var(--r);color:var(--t3);font-size:.82rem;font-weight:500;text-decoration:none;cursor:pointer;border:none;background:none;width:100%;transition:all .15s;margin-bottom:1px;}
.nav-item .ni{font-size:.88rem;width:18px;flex-shrink:0;text-align:center;}
.nav-item:hover{background:var(--primary-bg);color:var(--primary-d);text-decoration:none;}
.nav-item.active{background:var(--primary-bg);color:var(--primary-d);border:1px solid #a0bcd4;}
.sidebar-footer{padding:.7rem;border-top:1px solid var(--border);}
.user-info{display:flex;align-items:center;gap:8px;padding:7px 9px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--r);margin-bottom:7px;}
.user-avatar{width:26px;height:26px;flex-shrink:0;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.7rem;color:#fff;font-weight:700;}
.uname{font-size:.78rem;font-weight:700;color:var(--t1);}
.urole{font-size:.62rem;color:var(--t3);text-transform:uppercase;letter-spacing:.04em;}
.sidebar-plan-pill{padding:6px 9px;border-radius:var(--r);border:1px solid var(--border);background:var(--surface2);display:flex;align-items:center;gap:7px;margin-bottom:7px;}
.spp-label{font-size:.72rem;font-weight:700;color:var(--primary-d);}
.spp-exp{font-size:.62rem;color:var(--t4);}
.sidebar-footer-text{text-align:center;font-size:.65rem;color:var(--t4);padding:5px 0;line-height:1.5;}

/* ── App shell ── */
.app-shell{display:flex;min-height:100vh;}
.main-wrap{flex:1;margin-left:var(--sidebar);min-height:100vh;display:flex;flex-direction:column;}
.topbar{height:var(--hdr);background:var(--surface);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;padding:0 1.4rem;position:sticky;top:0;z-index:100;box-shadow:var(--sh);}
.topbar-left{display:flex;align-items:center;gap:9px;}
.topbar-title{font-size:.9rem;font-weight:700;color:var(--t1);}
.topbar-right{display:flex;align-items:center;gap:7px;}
.topbar-date{font-size:.7rem;color:var(--t3);background:var(--surface2);border:1px solid var(--border);border-radius:100px;padding:3px 10px;}
.page-content{padding:1.4rem;flex:1;}
.sidebar-toggle{display:none;background:var(--surface2);border:1px solid var(--border);color:var(--t2);border-radius:var(--r);padding:5px 9px;cursor:pointer;font-size:1rem;line-height:1;}
.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(44,58,74,.4);z-index:199;}
.sidebar-overlay.show{display:block;}

/* ── Toast ── */
.toast-wrap{position:fixed;bottom:1.2rem;right:1.2rem;z-index:9999;}
.toast{background:var(--surface);border:1px solid var(--border);border-radius:var(--r-lg);padding:10px 15px;display:flex;align-items:center;gap:9px;font-size:.82rem;font-weight:600;color:var(--t1);box-shadow:var(--sh-lg);min-width:230px;}
.toast.success{border-left:3px solid var(--green);}
.toast.error{border-left:3px solid var(--red);}
.toast.info{border-left:3px solid var(--primary);}

/* ── Map embed ── */
.map-embed{border-radius:var(--r-lg);overflow:hidden;border:1px solid var(--border);}
.map-embed iframe{width:100%;height:230px;border:none;display:block;}

/* ── Locked section ── */
.locked-section{padding:1.1rem;display:flex;align-items:center;gap:11px;background:var(--surface2);}
.lock-icon{font-size:1.4rem;flex-shrink:0;}

/* ── DLC table ── */
.dlc-sqm{font-weight:600;color:var(--t1);font-size:.8rem;}
.dlc-sqft{font-size:.67rem;color:var(--t3);}

/* ── Search bar ── */
.search-bar{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:.9rem;}
.search-input-wrap{position:relative;flex:1;min-width:190px;}
.search-icon-pos{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--t4);pointer-events:none;font-size:.9rem;}
.search-input-wrap .input{padding-left:32px;}

/* ── Divider ── */
.divider{height:1px;background:var(--border);margin:1.1rem 0;}

/* ── Image zoom overlay ── */
#imgZoomOverlay{position:fixed;inset:0;background:rgba(0,0,0,.88);z-index:9999;display:none;align-items:center;justify-content:center;}
#imgZoomOverlay.show{display:flex;}
#imgZoomOverlay img{max-width:90vw;max-height:88vh;border-radius:var(--r-lg);cursor:default;}
.zoom-close-btn{position:absolute;top:1rem;right:1rem;background:rgba(255,255,255,.2);border:none;color:#fff;font-size:1.3rem;width:36px;height:36px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;}
.zoom-controls{position:absolute;bottom:1.5rem;left:50%;transform:translateX(-50%);display:flex;gap:9px;}
.zoom-controls button{background:rgba(255,255,255,.2);border:none;color:#fff;font-size:.9rem;width:36px;height:36px;border-radius:50%;cursor:pointer;}
.zoom-controls button:hover{background:rgba(255,255,255,.35);}
.zoomable{cursor:zoom-in;}

/* ── Modal / Popup overlay ── */
.modal-overlay{position:fixed;inset:0;background:rgba(44,58,74,.45);z-index:500;display:none;align-items:center;justify-content:center;padding:1rem;}
.modal-overlay.show{display:flex;}
.modal-box{background:var(--surface);border:1px solid var(--border);border-radius:var(--r-xl);box-shadow:var(--sh-lg);width:100%;max-width:580px;max-height:90vh;overflow-y:auto;position:relative;animation:modalIn .22s ease;}
@keyframes modalIn{from{opacity:0;transform:scale(.94) translateY(12px);}to{opacity:1;transform:scale(1) translateY(0);}}
.modal-header{padding:.9rem 1.2rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:var(--surface2);}
.modal-header h3{font-size:.9rem;font-weight:700;color:var(--t1);}
.modal-close{background:none;border:none;font-size:1.1rem;cursor:pointer;color:var(--t3);padding:2px 6px;border-radius:4px;}
.modal-close:hover{background:var(--red-bg);color:var(--red);}
.modal-body{padding:1.2rem;}

/* ── Settings page ── */
.settings-section{background:var(--surface);border:1px solid var(--border);border-radius:var(--r-lg);padding:1.2rem;margin-bottom:1.2rem;}
.settings-section h4{font-size:.85rem;font-weight:700;color:var(--t1);margin-bottom:.9rem;padding-bottom:.5rem;border-bottom:1px solid var(--border);}

/* ── Two column ── */
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:1.2rem;}

/* ── Login ── */
<?php if($page==='login'): ?>
body{display:flex;align-items:center;justify-content:center;min-height:100vh;padding:1.5rem;background:linear-gradient(135deg,var(--bg) 0%,var(--sec) 100%);}
<?php endif; ?>
.login-wrap{width:100%;max-width:400px;animation:fadeUp .4s ease both;}
@keyframes fadeUp{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
.login-brand{text-align:center;margin-bottom:1.8rem;}
.login-brand-icon{width:60px;height:60px;background:var(--primary);border-radius:16px;display:inline-flex;align-items:center;justify-content:center;font-size:26px;margin-bottom:.7rem;box-shadow:0 6px 20px rgba(129,166,198,.45);}
.login-brand h1{font-size:1.5rem;font-weight:700;color:var(--t1);}
.login-brand .sub{font-size:.72rem;color:var(--t3);text-transform:uppercase;letter-spacing:.08em;margin-top:3px;}
.login-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--r-xl);padding:1.8rem;box-shadow:var(--sh-lg);}
.login-card h2{font-size:1rem;font-weight:700;color:var(--t1);margin-bottom:3px;}
.login-card .login-sub{font-size:.78rem;color:var(--t3);margin-bottom:1.4rem;}

/* ── Responsive ── */
@media(max-width:768px){
  :root{--sidebar:265px;}
  .sidebar{transform:translateX(-265px);}
  .sidebar.open{transform:translateX(0);}
  .main-wrap{margin-left:0;}
  .sidebar-toggle{display:flex;align-items:center;}
  .page-content{padding:1rem;}
  .plans-grid{grid-template-columns:1fr;}
  .form-grid{grid-template-columns:1fr;}
  .fg-full{grid-column:1;}
  .stats-grid{grid-template-columns:1fr 1fr;}
  .two-col{grid-template-columns:1fr;}
  .topbar{padding:0 .9rem;}
  .topbar-date{display:none;}
  table{font-size:.76rem;}
  thead th{padding:7px 8px;}
  tbody td{padding:7px 8px;}
  .plan-card-footer .btn{font-size:.7rem;padding:5px 7px;}
  div[style*="grid-template-columns:1fr 1.6fr"],
  div[style*="grid-template-columns:1fr 1.7fr"],
  div[style*="grid-template-columns:1fr 2fr"]{display:block!important;}
  div[style*="grid-template-columns:1fr 1.6fr"]>*,
  div[style*="grid-template-columns:1fr 1.7fr"]>*,
  div[style*="grid-template-columns:1fr 2fr"]>*{margin-bottom:1rem;}
}
@media(max-width:480px){
  .stats-grid{grid-template-columns:1fr 1fr;}
  .chip{font-size:.68rem;padding:3px 9px;}
  .login-card{padding:1.3rem;}
}
</style>
</head>
<body>

<?php /* ══ IMAGE ZOOM OVERLAY ══ */ ?>
<div id="imgZoomOverlay" onclick="closeZoom(event)">
  <button class="zoom-close-btn" onclick="closeZoomBtn()">X</button>
  <img id="zoomImg" src="" alt="Zoom">
  <div class="zoom-controls">
    <button onclick="zoomIn()">+</button>
    <button onclick="zoomReset()">O</button>
    <button onclick="zoomOut()">-</button>
  </div>
</div>

<?php /* ══ DLC MODAL POPUP ══ */ ?>
<div class="modal-overlay" id="dlcModal">
  <div class="modal-box">
    <div class="modal-header">
      <h3 id="dlcModalTitle">Add DLC Rate</h3>
      <button class="modal-close" onclick="closeDlcModal()" title="Close">X</button>
    </div>
    <div class="modal-body">
      <form method="POST" action="index.php" id="dlcForm">
        <input type="hidden" name="action" value="save_dlc">
        <input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
        <input type="hidden" name="dlc_id" id="dlcFormId" value="0">

        <div class="form-field">
          <label>Revenue Village <span class="req">*</span></label>
          <select class="input" name="village_id" id="dlcVillage" required>
            <option value="">-- Select Village --</option>
            <?php
            $villagesForDlc=[];
            try{ $villagesForDlc=db()->query('SELECT * FROM revenue_villages ORDER BY name')->fetchAll(); }catch(Throwable){}
            foreach($villagesForDlc as $v):
            ?><option value="<?= $v['id'] ?>"><?= e($v['name']) ?><?= $v['tehsil'] ? ' - '.e($v['tehsil']) : '' ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:9px">
          <div class="form-field">
            <label>Financial Year <span class="req">*</span></label>
            <input class="input" type="text" name="financial_year" id="dlcFY" required placeholder="e.g. 2024-25" value="<?= e(currentFY()) ?>">
          </div>
          <div class="form-field">
            <label>Effective From <span class="req">*</span></label>
            <input class="input" type="date" name="effective_from" id="dlcEf" required>
          </div>
        </div>

        <div style="background:var(--surface2);border:1px solid var(--border);border-radius:var(--r);padding:12px;margin-bottom:.9rem">
          <div style="font-size:.7rem;font-weight:700;color:var(--t3);text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px">DLC Rates (Rs per sq.m)</div>
          <?php foreach([['road_30ft','30 ft Road'],['road_40ft','40 ft Road'],['road_60ft','60 ft Road'],['road_80ft','80 ft Road'],['road_100ft','100 ft Road'],['near_highway','Near Highway']] as [$fn,$fl]): ?>
          <div style="display:flex;align-items:center;gap:9px;margin-bottom:7px">
            <label style="width:100px;flex-shrink:0;font-size:.72rem;font-weight:600;color:var(--t2);margin:0"><?= $fl ?></label>
            <div style="position:relative;flex:1">
              <span style="position:absolute;left:9px;top:50%;transform:translateY(-50%);color:var(--t3);font-size:.82rem;pointer-events:none">Rs</span>
              <input class="input" type="number" name="<?= $fn ?>" id="dlc_<?= $fn ?>" step="0.01" min="0" placeholder="0.00" style="padding-left:28px">
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <div class="form-field">
          <label>Notes</label>
          <textarea class="input" name="notes" id="dlcNotes" style="min-height:55px" placeholder="Optional remarks..."></textarea>
        </div>

        <div style="display:flex;gap:9px;margin-top:.5rem">
          <button type="button" onclick="closeDlcModal()" class="btn btn-ghost btn-md">Cancel</button>
          <button type="submit" class="btn btn-primary btn-md" id="dlcSubmitBtn">Save DLC Rate</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php /* ══ LOGIN ══ */ ?>
<?php if ($page==='login'): ?>
<div class="login-wrap">
  <div class="login-brand">
    <div class="login-brand-icon">M</div>
    <h1><?= e(APP_NAME) ?></h1>
    <div class="sub"><?= e(APP_BRAND) ?></div>
  </div>
  <div class="login-card">
    <h2>Sign in to your account</h2>
    <p class="login-sub">Access is restricted to authorised users only.</p>
    <?php if ($err==1): ?><div class="alert alert-danger">Incorrect username or password. Please try again.</div><?php endif; ?>
    <form method="POST" action="index.php">
      <input type="hidden" name="action" value="login">
      <div class="form-field"><label>Username</label><input class="input" type="text" name="username" placeholder="Enter username" autocomplete="username" required autofocus></div>
      <div class="form-field"><label>Password</label><input class="input" type="password" name="password" placeholder="Enter password" autocomplete="current-password" required></div>
      <button type="submit" class="btn btn-primary btn-lg btn-full" style="margin-top:.3rem">Sign In</button>
    </form>
    <p style="text-align:center;margin-top:.9rem;font-size:.72rem;color:var(--t4)">Default: <code>admin</code> / <code>admin@123</code> &nbsp;(change after first login)</p>
  </div>
</div>

<?php /* ══ APP SHELL ══ */ ?>
<?php else: ?>

<?php if ($gMarqueeEnabled==='1' && trim($gMarqueeText)!==''): ?>
<div class="marquee-bar">
  <span class="marquee-inner" style="animation-duration:<?= max(10,(int)$gMarqueeSpeed) ?>s">&nbsp;&nbsp;&nbsp;<?= e($gMarqueeText) ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= e($gMarqueeText) ?>&nbsp;&nbsp;&nbsp;</span>
</div>
<?php endif; ?>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<div class="app-shell">
  <!-- ═══ SIDEBAR ═══ -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
      <div class="brand-icon">M</div>
      <div class="brand-text"><h2><?= e(APP_NAME) ?></h2><span>Mingosoft Technologies</span></div>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-section">Main</div>
      <a class="nav-item <?= $page==='home'?'active':'' ?>" href="index.php"><span class="ni">[D]</span> Dashboard</a>
      <?php if (is_admin()): ?>
      <a class="nav-item <?= $page==='add'?'active':'' ?>" href="index.php?page=add"><span class="ni">[+]</span> Add Plan</a>
      <?php endif; ?>
      <div class="nav-section">Data</div>
      <a class="nav-item <?= $page==='villages'?'active':'' ?>" href="index.php?page=villages"><span class="ni">[V]</span> Revenue Villages</a>
      <a class="nav-item <?= $page==='dlc'?'active':'' ?>" href="index.php?page=dlc"><span class="ni">[R]</span> DLC Rates</a>
      <?php if (is_developer()||is_admin()): ?>
      <div class="nav-section">Developer</div>
      <a class="nav-item <?= in_array($page,['dev_plan','dev_plans'])?'active':'' ?>" href="index.php?page=dev_plans"><span class="ni">[P]</span> My Dev Plans</a>
      <a class="nav-item <?= $page==='dev_plan'?'active':'' ?>" href="index.php?page=dev_plan"><span class="ni">[+]</span> Add Dev Plan</a>
      <?php endif; ?>
      <?php if (is_admin()): ?>
      <div class="nav-section">Admin</div>
      <?php
        $pendCnt=0;
        try{$pendCnt=(int)db()->query("SELECT COUNT(*) FROM developer_plans WHERE status='pending'")->fetchColumn();}catch(Throwable){}
      ?>
      <a class="nav-item <?= $page==='dev_approvals'?'active':'' ?>" href="index.php?page=dev_approvals"><span class="ni">[!]</span> Plan Approvals<?= $pendCnt>0?" <span style='background:var(--red);color:#fff;border-radius:100px;padding:1px 6px;font-size:.62rem;margin-left:4px'>$pendCnt</span>":'' ?></a>
      <a class="nav-item <?= $page==='subscriptions'?'active':'' ?>" href="index.php?page=subscriptions"><span class="ni">[$]</span> Subscriptions</a>
      <a class="nav-item <?= $page==='settings'?'active':'' ?>" href="index.php?page=settings"><span class="ni">[S]</span> Settings</a>
      <?php endif; ?>
      <div class="nav-section">Account</div>
      <a class="nav-item <?= $page==='permissions'?'active':'' ?>" href="index.php?page=permissions"><span class="ni">[?]</span> Permissions</a>
      <a class="nav-item <?= $page==='profile'?'active':'' ?>" href="index.php?page=profile"><span class="ni">[U]</span> Profile</a>
    </nav>
    <div class="sidebar-footer">
      <?php $planLabel=get_plan_label(); $mySub=get_active_subscription(); ?>
      <div class="sidebar-plan-pill">
        <span style="font-size:.85rem">*</span>
        <div>
          <div class="spp-label"><?= $planLabel ?></div>
          <?php if ($mySub): ?><div class="spp-exp">Expires <?= date('d M Y',strtotime($mySub['end_date'])) ?></div><?php endif; ?>
        </div>
      </div>
      <div class="user-info">
        <div class="user-avatar"><?= strtoupper(substr($user['username'],0,1)) ?></div>
        <div><div class="uname"><?= e($user['full_name']?:$user['username']) ?></div><div class="urole"><?= e($user['role']) ?></div></div>
      </div>
      <form method="POST" style="margin-bottom:7px"><input type="hidden" name="action" value="logout"><button type="submit" class="btn btn-ghost btn-sm btn-full">Sign Out</button></form>
      <?php if (trim($gFooterText)!==''): ?>
      <div class="sidebar-footer-text"><?= $gFooterText ?></div>
      <?php endif; ?>
    </div>
  </aside>

  <!-- ═══ MAIN WRAP ═══ -->
  <div class="main-wrap">
    <header class="topbar">
      <div class="topbar-left">
        <button class="sidebar-toggle" onclick="toggleSidebar()" title="Menu">=</button>
        <span class="topbar-title">
          <?php $titles=['home'=>'Dashboard','add'=>'Register New Plan','edit'=>'Edit Plan','view'=>'Plan Detail','villages'=>'Revenue Villages','profile'=>'Profile','dlc'=>'DLC Rates','subscriptions'=>'Subscriptions &amp; Users','settings'=>'Settings','dev_plan'=>'Developer Plan Form','dev_plans'=>'My Developer Plans','dev_approvals'=>'Plan Approvals','dev_view'=>'Developer Plan Detail','permissions'=>'Permission Matrix'];
          echo $titles[$page]??ucfirst($page); ?>
        </span>
      </div>
      <div class="topbar-right">
        <span class="topbar-date"><?= date('d M Y') ?></span>
        <?php if (is_admin()): ?><a href="index.php?page=add" class="btn btn-primary btn-sm">+ Add Plan</a><?php endif; ?>
      </div>
    </header>

    <main class="page-content">

    <?php /* Toast */
    $toastMap=['created'=>['success','Plan registered.'],'updated'=>['success','Plan updated.'],'deleted'=>['success','Plan deleted.'],'saved'=>['success','Saved.'],'pwchanged'=>['success','Password changed.'],'dlc_saved'=>['success','DLC rates saved.'],'dlc_deleted'=>['success','DLC record deleted.'],'sub_saved'=>['success','Subscription saved.'],'sub_deleted'=>['success','Subscription deleted.'],'user_created'=>['success','User created.'],'chain_deleted'=>['success','Chain doc removed.'],'upgrade_requested'=>['info','Upgrade request submitted.'],'upgrade_reviewed'=>['success','Request reviewed.'],'imported'=>['success','DLC imported: '.((int)($_GET['ok']??0)).' rows, '.((int)($_GET['skip']??0)).' skipped.'],'reviewed'=>['success','Developer plan reviewed.'],'updated'=>['success','Updated.'],'dev_created'=>['success','Developer plan submitted for approval.'],'dev_updated'=>['success','Developer plan updated.'],'dev_deleted'=>['success','Developer plan deleted.']];
    if (isset($toastMap[$msg])): [$tc,$tm]=$toastMap[$msg]; ?>
    <div class="toast-wrap" id="toastWrap">
      <div class="toast <?= $tc ?>"><span><?= $tm ?></span><button onclick="this.closest('.toast-wrap').remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;color:var(--t3);font-size:1rem">X</button></div>
    </div>
    <script>setTimeout(function(){var t=document.getElementById('toastWrap');if(t)t.remove();},5000);</script>
    <?php endif; ?>

    <!-- ══════════════════════════════════════
         HOME / DASHBOARD
    ══════════════════════════════════════ -->
    <?php if ($page==='home'): ?>

    <!-- SPONSORED / FEATURED DEVELOPER PLANS -->
    <?php if (!empty($sponsoredPlans)): ?>
    <div style="margin-bottom:1.5rem">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:.9rem">
        <div style="background:var(--gold);color:#fff;padding:3px 10px;border-radius:100px;font-size:.7rem;font-weight:700;letter-spacing:.04em;text-transform:uppercase">Featured Properties</div>
        <div style="height:1px;flex:1;background:var(--border)"></div>
      </div>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px">
        <?php foreach ($sponsoredPlans as $sp): ?>
        <div style="background:linear-gradient(135deg,#FBF0E8,#fff);border:2px solid var(--gold);border-radius:var(--r-lg);overflow:hidden;box-shadow:0 4px 16px rgba(200,149,108,.2);position:relative">
          <div style="position:absolute;top:8px;right:8px;z-index:2;background:var(--gold);color:#fff;padding:2px 9px;border-radius:100px;font-size:.65rem;font-weight:700;letter-spacing:.04em"><?= e($sp['sponsored_label']) ?></div>
          <?php if ($sp['file_type']==='image'&&$sp['file_path']): ?>
          <div style="height:115px;overflow:hidden"><img src="<?= e($sp['file_path']) ?>" alt="" style="width:100%;height:100%;object-fit:cover"></div>
          <?php else: ?>
          <div style="height:70px;background:var(--gold-bg);display:flex;align-items:center;justify-content:center;font-size:1.6rem">[DEV]</div>
          <?php endif; ?>
          <div style="padding:10px 12px">
            <div style="font-weight:700;color:var(--t1);font-size:.88rem;margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= e($sp['plan_name']) ?></div>
            <div style="font-size:.72rem;color:var(--gold-s);font-weight:600;margin-bottom:4px"># <?= e($sp['aaraji_number']) ?></div>
            <?php if ($sp['village_name']): ?><div style="font-size:.72rem;color:var(--t3);margin-bottom:6px"><?= e($sp['village_name']) ?></div><?php endif; ?>
            <div style="display:flex;align-items:center;justify-content:space-between;gap:6px">
              <?php if ($sp['price_30ft']||$sp['price_60ft']): ?>
              <div style="font-size:.7rem;color:var(--gold-s);font-weight:600">From Rs<?= number_format((float)min(array_filter([$sp['price_30ft'],$sp['price_40ft'],$sp['price_60ft'],$sp['price_80ft'],$sp['price_100ft'],$sp['price_highway']], fn($v)=>$v!==null && $v>0)),0) ?>/<?= e($sp['price_unit']) ?></div>
              <?php endif; ?>
              <a href="index.php?page=dev_view&id=<?= $sp['id'] ?>" class="btn btn-primary btn-sm" style="font-size:.72rem;padding:4px 10px">View</a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <div class="stats-grid">
      <?php foreach([['[P]','si-blue',$stats['total']??0,'Total Plans'],['[I]','si-gold',$stats['images']??0,'Images'],['[D]','si-gray',$stats['pdfs']??0,'PDFs'],['[L]','si-green',$stats['located']??0,'Located'],['[V]','si-gold',$stats['villages']??0,'Villages']] as [$ic,$sc,$sv,$sl]): ?>
      <div class="stat-card"><div class="stat-icon <?= $sc ?>"><?= $ic ?></div><div><div class="stat-val"><?= (int)$sv ?></div><div class="stat-lbl"><?= $sl ?></div></div></div>
      <?php endforeach; ?>
    </div>

    <form method="GET" action="index.php">
      <input type="hidden" name="page" value="home">
      <div class="search-bar">
        <div class="search-input-wrap"><span class="search-icon-pos">*</span><input class="input" type="text" name="q" value="<?= e($q) ?>" placeholder="Search plan name, aaraji, village..."></div>
        <select class="input" name="village" style="width:auto;min-width:145px">
          <option value="">All Villages</option>
          <?php foreach ($villagesAll as $v): ?><option value="<?= $v['id'] ?>" <?= $vf==$v['id']?'selected':'' ?>><?= e($v['name']) ?></option><?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary btn-md">Search</button>
        <?php if ($q||$vf||$tf): ?><a href="index.php" class="btn btn-ghost btn-md">Clear</a><?php endif; ?>
      </div>
      <div class="filter-row">
        <span style="font-size:.68rem;color:var(--t4);text-transform:uppercase;letter-spacing:.06em">Filter:</span>
        <?php foreach([''  =>'All','image'=>'Images','pdf'=>'PDFs','location'=>'Located'] as $val=>$lbl):
          $url='index.php?page=home'.($q?'&q='.urlencode($q):'').($vf?'&village='.$vf:'').($val?'&type='.$val:''); ?>
        <a href="<?= $url ?>" class="chip <?= $tf===$val?'active':'' ?>"><?= $lbl ?></a>
        <?php endforeach; ?>
      </div>
    </form>

    <?php if (empty($plans)): ?>
    <div style="text-align:center;padding:3rem 1rem;color:var(--t3)">
      <div style="font-size:2.5rem;margin-bottom:.7rem"><?= ($q||$vf||$tf)?'?':'[MAP]' ?></div>
      <h3 style="margin-bottom:.4rem"><?= ($q||$vf||$tf)?'No results':'No plans yet' ?></h3>
      <p style="font-size:.82rem">
<?= ($q||$vf||$tf)
    ? 'Try a different search.'
    : (is_admin()
        ? 'Add your first plan.'
        : 'No plans available.')
?>
</p>
    </div>
    <?php else: ?>
    <div class="plans-grid">
      <?php foreach ($plans as $plan): ?>
      <div class="plan-card">
        <div class="plan-thumb">
          <?php if ($plan['file_type']==='image'&&$plan['file_path']): ?><img src="<?= e($plan['file_path']) ?>" alt="<?= e($plan['plan_name']) ?>">
          <?php elseif ($plan['file_type']==='pdf'): ?><span>PDF</span>
          <?php else: ?><span>[MAP]</span><?php endif; ?>
          <?php if ($plan['file_type']): ?><span class="thumb-tag"><?= strtoupper($plan['file_type']) ?></span><?php endif; ?>
        </div>
        <div class="plan-card-body">
          <div class="plan-name" title="<?= e($plan['plan_name']) ?>"><?= e($plan['plan_name']) ?></div>
          <div class="plan-aaraji"># <?= e($plan['aaraji_number']) ?></div>
          <?php if ($plan['village_name']): ?><div class="plan-village"><?= e($plan['village_name']) ?><?= $plan['tehsil']?' - '.e($plan['tehsil']):'' ?></div><?php endif; ?>
          <div class="plan-loc"><?= $plan['google_location']?'Location: '.e(substr($plan['google_location'],0,42)).(strlen($plan['google_location'])>42?'...':''):'<span style="color:var(--t4)">No location</span>' ?></div>
        </div>
        <div class="plan-card-footer">
          <a href="index.php?page=view&id=<?= $plan['id'] ?>" class="btn btn-primary btn-sm">View</a>
          <?php if ($plan['google_location']): ?><a href="<?= e($plan['google_location']) ?>" target="_blank" rel="noopener" class="btn btn-ghost btn-sm">Map</a><?php endif; ?>
          <?php if (is_admin()): ?>
          <a href="index.php?page=edit&id=<?= $plan['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
          <form method="POST" style="display:inline" onsubmit="return confirm('Delete this plan?')">
            <input type="hidden" name="action" value="delete_plan"><input type="hidden" name="plan_id" value="<?= $plan['id'] ?>"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
            <button type="submit" class="btn btn-danger btn-sm">Del</button>
          </form>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php $tp=(int)ceil($total/$pp); if ($tp>1): $base='index.php?page=home'.($q?'&q='.urlencode($q):'').($vf?'&village='.$vf:'').($tf?'&type='.$tf:''); ?>
    <div class="pagination">
      <a href="<?= $base ?>&p=<?= max(1,$cp-1) ?>" class="pag-btn <?= $cp<=1?'disabled':'' ?>">Prev</a>
      <?php for ($i=1;$i<=$tp;$i++): ?><a href="<?= $base ?>&p=<?= $i ?>" class="pag-btn <?= $i==$cp?'active':'' ?>"><?= $i ?></a><?php endfor; ?>
      <a href="<?= $base ?>&p=<?= min($tp,$cp+1) ?>" class="pag-btn <?= $cp>=$tp?'disabled':'' ?>">Next</a>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <!-- ══════════════════════════════════════
         ADD / EDIT
    ══════════════════════════════════════ -->
    <?php elseif ($page==='add'||$page==='edit'): ?>

    <?php if ($err==='missing'): ?><div class="alert alert-danger">Plan name and Aaraji number are required.</div>
    <?php elseif ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>

    <div class="card" style="max-width:760px">
      <div class="card-header">
        <h3><?= $page==='edit'?'Edit Plan: '.e($editPlan['plan_name']??''):'Register New Plan' ?></h3>
        <a href="index.php" class="btn btn-ghost btn-sm">Back</a>
      </div>
      <div class="card-body">
        <form method="POST" action="index.php" enctype="multipart/form-data">
          <input type="hidden" name="action" value="save_plan">
          <input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
          <?php if ($page==='edit'): ?><input type="hidden" name="plan_id" value="<?= (int)$editPlan['id'] ?>"><?php endif; ?>
          <div class="form-grid">
            <div class="form-field"><label>Plan Name <span class="req">*</span></label><input class="input" type="text" name="plan_name" required placeholder="e.g. Green Valley Plot A" value="<?= e($editPlan['plan_name']??'') ?>"></div>
            <div class="form-field"><label>Aaraji Number <span class="req">*</span></label><input class="input" type="text" name="aaraji_number" required placeholder="e.g. ARJ/2024/0012" value="<?= e($editPlan['aaraji_number']??'') ?>"></div>
            <div class="form-field fg-full">
              <label>Revenue Village</label>
              <select class="input" name="village_id">
                <option value="">-- Select Village --</option>
                <?php foreach ($villagesAll as $v): ?><option value="<?= $v['id'] ?>" <?= (($editPlan['village_id']??0)==$v['id'])?'selected':'' ?>><?= e($v['name']) ?><?= $v['tehsil']?' - '.e($v['tehsil']):'' ?></option><?php endforeach; ?>
              </select>
              <small style="color:var(--t3);font-size:.7rem">Don't see your village? <a href="index.php?page=villages">Add it here</a></small>
            </div>
            <div class="form-field fg-full">
              <label>Upload Plan Image or PDF</label>
              <div class="upload-zone" id="uploadZone">
                <input type="file" name="plan_file" accept="image/*,.pdf" id="fileInput">
                <div style="font-size:1.6rem;margin-bottom:.4rem">[FILE]</div>
                <p>Click or drag &amp; drop to upload</p>
                <small>JPG, PNG, WEBP, PDF - Max <?= MAX_FILE_MB ?>MB</small>
              </div>
              <div class="upload-preview" id="uploadPreview">
                <span id="previewIcon">[F]</span>
                <span id="previewName"></span>
                <button type="button" onclick="clearUpload()" style="margin-left:auto;background:none;border:none;color:var(--red);cursor:pointer">X</button>
              </div>
              <div id="filePreviewBox" style="display:none;margin-top:10px;border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden;background:var(--surface2)">
                <img id="filePreviewImg" src="" alt="" style="width:100%;max-height:250px;object-fit:contain;display:none">
                <div id="filePreviewPdf" style="display:none;padding:1.5rem;text-align:center">
                  <div style="font-size:2.5rem;margin-bottom:.4rem">[PDF]</div>
                  <div id="filePreviewPdfName" style="font-size:.82rem;color:var(--t2)"></div>
                </div>
              </div>
              <?php if (!empty($editPlan['file_name'])): ?>
              <p style="margin-top:7px;font-size:.74rem;color:var(--t3)">Current: <strong><?= e($editPlan['file_name']) ?></strong> - upload to replace.</p>
              <?php if ($editPlan['file_type']==='image'&&$editPlan['file_path']): ?>
              <div style="margin-top:8px;border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden"><img src="<?= e($editPlan['file_path']) ?>" alt="Current plan" style="width:100%;max-height:210px;object-fit:contain;background:var(--surface2)"></div>
              <?php endif; ?>
              <?php endif; ?>
            </div>
            <div class="form-field fg-full">
              <label>Google Maps Location URL</label>
              <div style="position:relative">
                <input class="input" type="url" name="google_location" id="locInput" placeholder="Paste Google Maps share link..." value="<?= e($editPlan['google_location']??'') ?>" style="padding-right:44px" oninput="previewLoc()">
                <button type="button" onclick="pasteLoc()" style="position:absolute;right:7px;top:50%;transform:translateY(-50%);background:var(--primary-bg);border:1px solid #a0bcd4;border-radius:5px;width:30px;height:30px;cursor:pointer;color:var(--primary-d);font-size:.82rem;display:flex;align-items:center;justify-content:center">[P]</button>
              </div>
              <div id="locPreview" style="<?= !empty($editPlan['google_location'])?'':'display:none' ?>;margin-top:7px;padding:7px 11px;background:var(--primary-bg);border:1px solid #a0bcd4;border-radius:var(--r);font-size:.75rem;color:var(--primary-d)">Location link saved</div>
            </div>
            <div class="form-field fg-full"><label>Notes (optional)</label><textarea class="input" name="notes" placeholder="Additional notes..."><?= e($editPlan['notes']??'') ?></textarea></div>
            <div class="form-field fg-full">
              <label>Chain Documents <span style="font-weight:400;font-size:.68rem;color:var(--t3);text-transform:none;letter-spacing:0;margin-left:5px">(PDF or multiple images)</span></label>
              <?php if (!empty($chainDocs)): ?>
              <div style="margin-bottom:9px">
                <div style="font-size:.7rem;font-weight:700;color:var(--t3);margin-bottom:7px">Existing (<?= count($chainDocs) ?>)</div>
                <?php foreach ($chainDocs as $doc): ?>
                <div style="display:flex;align-items:center;gap:9px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--r);padding:7px 11px;margin-bottom:5px">
                  <span><?= $doc['file_type']==='pdf'?'[PDF]':'[IMG]' ?></span>
                  <div style="flex:1;min-width:0"><div style="font-size:.79rem;color:var(--t1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= e($doc['file_name']) ?></div><div style="font-size:.67rem;color:var(--t3)"><?= strtoupper($doc['file_type']) ?><?= $doc['file_size']?' - '.round($doc['file_size']/1024).' KB':'' ?></div></div>
                  <a href="<?= e($doc['file_path']) ?>" target="_blank" class="btn btn-ghost btn-sm">Open</a>
                  <?php if (is_admin()): ?>
                  <form method="POST" onsubmit="return confirm('Delete?')"><input type="hidden" name="action" value="delete_chain_doc"><input type="hidden" name="doc_id" value="<?= $doc['id'] ?>"><input type="hidden" name="plan_id" value="<?= (int)$editPlan['id'] ?>"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><button type="submit" class="btn btn-danger btn-sm">Del</button></form>
                  <?php endif; ?>
                </div>
                <?php endforeach; ?>
                <p style="font-size:.7rem;color:var(--t3);margin-top:5px">Add more below - they will be appended.</p>
              </div>
              <?php endif; ?>
              <div class="upload-zone" id="chainZone">
                <input type="file" name="chain_docs[]" id="chainInput" accept="image/*,.pdf" multiple>
                <div style="font-size:1.6rem;margin-bottom:.4rem">[FILES]</div>
                <p>Click or drag files here</p>
                <small>PDF or Images - Multiple files allowed - Max <?= MAX_FILE_MB ?>MB each</small>
              </div>
              <div id="chainPreviewList" style="display:none;flex-direction:column;gap:5px;margin-top:7px"></div>
            </div>
          </div>
          <div class="divider"></div>
          <div style="display:flex;gap:9px;flex-wrap:wrap">
            <a href="index.php" class="btn btn-ghost btn-md">Cancel</a>
            <button type="submit" class="btn btn-primary btn-md"><?= $page==='edit'?'Update Plan':'Register Plan' ?></button>
          </div>
        </form>
      </div>
    </div>

    <!-- ══════════════════════════════════════
         VIEW PLAN DETAIL
    ══════════════════════════════════════ -->
    <?php elseif ($page==='view'):
      $isBasic=can_view_basic(); $isAdvance=can_view_advance(); ?>

    <?php if (!$isBasic): ?>
    <div style="max-width:500px;margin:3rem auto;text-align:center"><div class="card"><div class="card-body" style="padding:2.5rem 1.5rem">
      <div style="font-size:2.5rem;margin-bottom:.8rem">[LOCK]</div>
      <h2 style="margin-bottom:.5rem">Subscription Required</h2>
      <p style="font-size:.84rem;color:var(--t3);margin-bottom:1.2rem">You need an active subscription to view plan details.</p>
      <a href="index.php" class="btn btn-ghost btn-md" style="margin-right:8px">Back</a>
      <a href="index.php?page=profile" class="btn btn-primary btn-md">Request Access</a>
    </div></div></div>
    <?php else: ?>

    <?php if (!is_admin()&&$mySub??null): ?>
    <div class="alert alert-<?= $isAdvance?'warning':'success' ?>" style="margin-bottom:.9rem">
      <?= $isAdvance?'Advance Plan':'Basic Plan' ?> Active - Expires <?= date('d M Y',strtotime(($mySub??[])['end_date']??'today')) ?><?= $isAdvance?'':' - Download &amp; DLC require Advance plan' ?>
    </div>
    <?php endif; ?>

    <div style="display:flex;gap:8px;margin-bottom:1.1rem;align-items:center;flex-wrap:wrap">
      <a href="index.php" class="btn btn-ghost btn-sm">Back</a>
      <?php if (is_admin()): ?><a href="index.php?page=edit&id=<?= $plan['id'] ?>" class="btn btn-secondary btn-sm">Edit</a><?php endif; ?>
      <?php if ($plan['google_location']&&$isBasic): ?><a href="<?= e($plan['google_location']) ?>" target="_blank" rel="noopener" class="btn btn-ghost btn-sm">Open in Maps</a><?php endif; ?>
      <?php if ($plan['file_path']&&$isAdvance): ?><a href="<?= e($plan['file_path']) ?>" download="<?= e($plan['file_name']) ?>" class="btn btn-secondary btn-sm">Download</a><?php endif; ?>
      <?php if (is_admin()): ?>
      <form method="POST" style="margin-left:auto" onsubmit="return confirm('Delete this plan permanently?')">
        <input type="hidden" name="action" value="delete_plan"><input type="hidden" name="plan_id" value="<?= $plan['id'] ?>"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
      </form>
      <?php endif; ?>
    </div>

    <div class="two-col">
      <!-- Left -->
      <div>
        <?php if ($plan['file_type']==='image'&&$plan['file_path']&&$isBasic): ?>
        <div class="card" style="margin-bottom:1.1rem;overflow:hidden">
          <img src="<?= e($plan['file_path']) ?>" alt="Plan" class="zoomable" onclick="openZoom(this.src)" style="width:100%;display:block;max-height:320px;object-fit:contain;background:var(--surface2)">
          <div style="padding:7px 12px;background:var(--surface2);border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
            <span style="font-size:.72rem;color:var(--t3)">Click image to zoom</span>
            <?php if ($isAdvance): ?><a href="<?= e($plan['file_path']) ?>" download class="btn btn-secondary btn-sm">Download</a>
            <?php else: ?><span style="font-size:.72rem;color:var(--t3)">Download requires Advance plan</span><?php endif; ?>
          </div>
        </div>
        <?php elseif ($plan['file_type']==='pdf'&&$plan['file_path']): ?>
        <div class="card" style="margin-bottom:1.1rem"><div style="padding:2rem;text-align:center;background:var(--surface2)">
          <div style="font-size:2.5rem;margin-bottom:.7rem">[PDF]</div>
          <p style="font-size:.84rem;color:var(--t2);margin-bottom:1rem"><?= e($plan['file_name']) ?></p>
          <?php if ($isAdvance): ?>
          <a href="<?= e($plan['file_path']) ?>" target="_blank" class="btn btn-secondary btn-sm">Open PDF</a>
          <a href="<?= e($plan['file_path']) ?>" download class="btn btn-ghost btn-sm" style="margin-left:6px">Download</a>
          <?php else: ?><div class="locked-section" style="justify-content:center"><span class="lock-icon">[LOCK]</span><div><strong>Advance Plan Required</strong><br><small>Upgrade to view PDF</small></div></div><?php endif; ?>
        </div></div>
        <?php else: ?>
        <div class="card" style="margin-bottom:1.1rem"><div style="padding:2.5rem;text-align:center;background:var(--surface2)"><div style="font-size:2.5rem;margin-bottom:.5rem">[MAP]</div><p style="font-size:.8rem;color:var(--t3)">No file uploaded</p></div></div>
        <?php endif; ?>
        <?php if ($plan['google_location']&&$isBasic): $emb=embedUrl($plan['google_location']); ?>
        <div class="map-embed">
          <?php if ($emb): ?><iframe src="<?= e($emb) ?>" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          <?php else: ?><div style="height:160px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:5px;background:var(--surface2)"><div style="font-size:1.5rem">[MAP]</div><a href="<?= e($plan['google_location']) ?>" target="_blank" rel="noopener" style="font-size:.79rem">Open in Google Maps</a></div><?php endif; ?>
        </div>
        <?php endif; ?>
      </div>
      <!-- Right -->
      <div>
        <div class="card" style="margin-bottom:1.1rem">
          <div class="card-header"><h3>Plan Information</h3><span class="badge badge-blue"># <?= e($plan['aaraji_number']) ?></span></div>
          <div class="card-body">
            <div style="margin-bottom:1rem"><div style="font-size:1.05rem;font-weight:700;color:var(--t1);margin-bottom:2px"><?= e($plan['plan_name']) ?></div><div style="font-size:.74rem;color:var(--primary-d);font-weight:600">Aaraji # <?= e($plan['aaraji_number']) ?></div></div>
            <table style="width:100%;border-collapse:collapse">
              <?php $rows=[['Aaraji No.',$plan['aaraji_number']],['Village',$plan['village_name']??'--'],['Tehsil',$plan['tehsil']??'--'],['District',$plan['district']??'--']];
              if (is_admin()) $rows=array_merge($rows,[['File Type',$plan['file_type']?strtoupper($plan['file_type']):'--'],['Registered By',$plan['created_by_name']??'--'],['Registered On',date('d M Y',strtotime($plan['created_at']))]]);
              foreach ($rows as [$l,$v]): ?>
              <tr><td style="padding:6px 0;border-bottom:1px solid var(--surface2);font-size:.67rem;font-weight:700;color:var(--t3);text-transform:uppercase;letter-spacing:.06em;width:38%"><?= e($l) ?></td><td style="padding:6px 0;border-bottom:1px solid var(--surface2);font-size:.81rem;color:var(--t1);text-align:right"><?= e($v) ?></td></tr>
              <?php endforeach; ?>
            </table>
            <?php if ($plan['notes']&&is_admin()): ?><div style="margin-top:1rem"><div style="font-size:.67rem;font-weight:700;color:var(--t3);text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px">Notes</div><p style="font-size:.82rem;line-height:1.7"><?= nl2br(e($plan['notes'])) ?></p></div><?php endif; ?>
          </div>
        </div>

        <!-- DLC -->
        <?php if ($isAdvance&&$planDlc): ?>
        <div class="card" style="margin-bottom:1.1rem">
          <div class="card-header"><h3>DLC Rates</h3><div style="display:flex;gap:7px;align-items:center"><span class="badge badge-gold">FY <?= e($planDlc['financial_year']) ?></span><span style="font-size:.68rem;color:var(--t3)">Eff. <?= date('d M Y',strtotime($planDlc['effective_from'])) ?></span></div></div>
          <div class="card-body">
            <table style="width:100%;border-collapse:collapse">
              <tr><th style="padding:6px 0;font-size:.68rem;font-weight:700;color:var(--t3);text-transform:uppercase;border-bottom:1px solid var(--border);text-align:left">Road</th><th style="padding:6px 4px;font-size:.68rem;font-weight:700;color:var(--t3);text-transform:uppercase;border-bottom:1px solid var(--border);text-align:right">Rs/sq.m</th><th style="padding:6px 0 6px 4px;font-size:.68rem;font-weight:700;color:var(--t3);text-transform:uppercase;border-bottom:1px solid var(--border);text-align:right">Rs/sq.ft</th></tr>
              <?php foreach ([['30 ft','road_30ft'],['40 ft','road_40ft'],['60 ft','road_60ft'],['80 ft','road_80ft'],['100 ft','road_100ft'],['Highway','near_highway']] as [$lbl,$fld]):
              if ($planDlc[$fld]===null) continue; ?>
              <tr><td style="padding:5px 0;border-bottom:1px solid var(--surface2);font-size:.79rem;color:var(--t2)"><?= $lbl ?></td><td style="padding:5px 4px;border-bottom:1px solid var(--surface2);text-align:right;font-weight:600;color:var(--t1);font-size:.79rem"><?= fmtSqm((float)$planDlc[$fld]) ?></td><td style="padding:5px 0 5px 4px;border-bottom:1px solid var(--surface2);text-align:right;color:var(--t3);font-size:.72rem"><?= fmtSqft((float)$planDlc[$fld]) ?></td></tr>
              <?php endforeach; ?>
            </table>
          </div>
        </div>
        <?php elseif (!$isAdvance): ?>
        <div class="card" style="margin-bottom:1.1rem"><div class="card-header"><h3>DLC Rates</h3></div><div class="locked-section"><span class="lock-icon">[LOCK]</span><div><strong>Advance Plan Required</strong><br><small style="color:var(--t3)">Upgrade to view DLC rates</small></div></div></div>
        <?php endif; ?>

        <!-- Chain docs -->
        <?php if ($isAdvance): ?>
        <div class="card">
          <div class="card-header"><h3>Chain Documents</h3><?php if (!empty($chainDocs)): ?><span class="badge badge-blue"><?= count($chainDocs) ?></span><?php endif; ?></div>
          <?php if (empty($chainDocs)): ?><div style="padding:1.2rem;text-align:center;color:var(--t3);font-size:.82rem">No chain documents.<?php if (is_admin()): ?> <a href="index.php?page=edit&id=<?= $plan['id'] ?>">Add</a><?php endif; ?></div>
          <?php else: ?>
          <div class="card-body" style="display:flex;flex-direction:column;gap:8px">
            <?php foreach ($chainDocs as $idx=>$doc): ?>
            <div style="display:flex;align-items:center;gap:9px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--r);padding:9px 11px">
              <div style="width:38px;height:38px;flex-shrink:0;border-radius:var(--r);background:var(--bg);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:1rem;overflow:hidden">
                <?php if ($doc['file_type']==='image'): ?><img src="<?= e($doc['file_path']) ?>" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:4px"><?php else: ?>[PDF]<?php endif; ?>
              </div>
              <div style="flex:1;min-width:0"><div style="font-size:.79rem;font-weight:600;color:var(--t1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= e($doc['file_name']) ?></div><div style="font-size:.67rem;color:var(--t3)"><?= strtoupper($doc['file_type']) ?><?= $doc['file_size']?' - '.round($doc['file_size']/1024).' KB':'' ?> - #<?= $idx+1 ?></div></div>
              <a href="<?= e($doc['file_path']) ?>" target="_blank" class="btn btn-ghost btn-sm">Open</a>
              <a href="<?= e($doc['file_path']) ?>" download="<?= e($doc['file_name']) ?>" class="btn btn-secondary btn-sm">Save</a>
              <?php if (is_admin()): ?><form method="POST" onsubmit="return confirm('Delete?')"><input type="hidden" name="action" value="delete_chain_doc"><input type="hidden" name="doc_id" value="<?= $doc['id'] ?>"><input type="hidden" name="plan_id" value="<?= $plan['id'] ?>"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><button type="submit" class="btn btn-danger btn-sm">Del</button></form><?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="card"><div class="card-header"><h3>Chain Documents</h3></div><div class="locked-section"><span class="lock-icon">[LOCK]</span><div><strong>Advance Plan Required</strong><br><small style="color:var(--t3)">Upgrade to view &amp; download chain documents</small></div></div></div>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; /* isBasic */ ?>

    <!-- ══════════════════════════════════════
         VILLAGES
    ══════════════════════════════════════ -->
    <?php elseif ($page==='villages'): ?>
    <div class="two-col">
      <?php if (is_admin()): ?>
      <div class="card">
        <div class="card-header"><h3>Add Revenue Village</h3></div>
        <div class="card-body">
          <form method="POST" action="index.php">
            <input type="hidden" name="action" value="save_village"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
            <div class="form-field"><label>Village Name <span class="req">*</span></label><input class="input" type="text" name="village_name" required placeholder="e.g. Malegaon"></div>
            <div class="form-field"><label>Tehsil</label><input class="input" type="text" name="tehsil" placeholder="e.g. Sinnar"></div>
            <div class="form-field"><label>District</label><input class="input" type="text" name="district" placeholder="e.g. Nashik"></div>
            <button type="submit" class="btn btn-primary btn-md">Save Village</button>
          </form>
        </div>
      </div>
      <?php endif; ?>
      <div class="card">
        <div class="card-header"><h3>All Revenue Villages</h3><span class="badge badge-blue"><?= count($villages) ?></span></div>
        <div class="table-wrap">
          <?php if (empty($villages)): ?><div style="padding:1.5rem;text-align:center;color:var(--t3)">No villages added yet.</div>
          <?php else: ?>
          <table>
            <thead><tr><th>Village</th><th>Tehsil</th><th>Plans</th><?php if (is_admin()): ?><th></th><?php endif; ?></tr></thead>
            <tbody>
              <?php foreach ($villages as $v): ?>
              <tr>
                <td><strong style="color:var(--t1)"><?= e($v['name']) ?></strong><?php if ($v['district']): ?><br><small style="color:var(--t3)"><?= e($v['district']) ?></small><?php endif; ?></td>
                <td><?= e($v['tehsil']??'--') ?></td>
                <td><span class="badge badge-gray"><?= (int)$v['plan_count'] ?></span></td>
                <?php if (is_admin()): ?><td><form method="POST" onsubmit="return confirm('Delete village?')"><input type="hidden" name="action" value="delete_village"><input type="hidden" name="village_id" value="<?= $v['id'] ?>"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><button type="submit" class="btn btn-danger btn-sm">Del</button></form></td><?php endif; ?>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- ══════════════════════════════════════
         DLC RATES
    ══════════════════════════════════════ -->
    <?php elseif ($page==='dlc'): ?>

    <?php if (is_admin()): ?>
    <div style="display:flex;gap:9px;margin-bottom:1.2rem;flex-wrap:wrap;align-items:center">
      <button onclick="openDlcModal()" class="btn btn-primary btn-sm">+ Add DLC Rate</button>
      <a href="index.php?action=export_dlc<?= $filterVid?'&village='.$filterVid:'' ?><?= $filterFy?'&fy='.urlencode($filterFy):'' ?>" class="btn btn-success btn-sm">Export Excel</a>
      <form method="POST" action="index.php" enctype="multipart/form-data" style="display:flex;align-items:center;gap:7px">
        <input type="hidden" name="action" value="import_dlc_csv"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
        <input type="file" name="dlc_csv" accept=".csv,.txt" style="font-size:.75rem;background:var(--surface);border:1px solid var(--border);border-radius:var(--r);padding:4px 8px;max-width:190px" required>
        <button type="submit" class="btn btn-secondary btn-sm">Import CSV</button>
      </form>
    </div>
    <div class="alert alert-info" style="font-size:.75rem;margin-bottom:1rem">CSV columns: Village Name, Financial Year, Effective From, 30 ft Road, 40 ft Road, 60 ft Road, 80 ft Road, 100 ft Road, Near Highway, Notes</div>
    <?php endif; ?>

    <?php if ($err==='missing'): ?><div class="alert alert-danger">Village, Financial Year and Effective From are required.</div><?php endif; ?>
    <?php if ($msg==='imported'): ?><div class="alert alert-success">Imported <?= (int)($_GET['ok']??0) ?> rows. <?= (int)($_GET['skip']??0) ?> skipped.</div><?php endif; ?>

    <!-- Filter -->
    <form method="GET" action="index.php" style="display:flex;gap:7px;margin-bottom:1.1rem;flex-wrap:wrap">
      <input type="hidden" name="page" value="dlc">
      <select class="input" name="village" style="width:auto;min-width:145px"><option value="">All Villages</option><?php foreach ($villagesAll as $v): ?><option value="<?= $v['id'] ?>" <?= $filterVid==$v['id']?'selected':'' ?>><?= e($v['name']) ?></option><?php endforeach; ?></select>
      <select class="input" name="fy" style="width:auto;min-width:110px">
        <?php if (empty($fyList)): ?><option value="<?= e($fyDefault) ?>"><?= e($fyDefault) ?></option>
        <?php else: foreach ($fyList as $fy): ?><option value="<?= e($fy) ?>" <?= $filterFy===$fy?'selected':'' ?>><?= e($fy) ?></option><?php endforeach; endif; ?>
      </select>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      <?php if ($filterVid||($filterFy&&$filterFy!==$fyDefault)): ?><a href="index.php?page=dlc" class="btn btn-ghost btn-sm">Clear</a><?php endif; ?>
    </form>

    <div class="card">
      <div class="card-header"><h3>DLC Rate Records</h3><span class="badge badge-blue"><?= count($dlcList) ?> records</span></div>
      <?php if (empty($dlcList)): ?>
      <div style="padding:2rem;text-align:center;color:var(--t3);font-size:.82rem">No DLC records<?= is_admin()?'. Click "Add DLC Rate" to add.':' yet.' ?></div>
      <?php else: ?>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Village</th><th>FY</th><th>Eff. Date</th><th>30ft</th><th>40ft</th><th>60ft</th><th>80ft</th><th>100ft</th><th>Highway</th><?php if (is_admin()): ?><th>Actions</th><?php endif; ?></tr></thead>
          <tbody>
            <?php foreach ($dlcList as $dr): ?>
            <tr>
              <td><strong style="color:var(--t1)"><?= e($dr['village_name']) ?></strong><?= $dr['tehsil']?'<br><small style="color:var(--t3)">'.e($dr['tehsil']).'</small>':'' ?></td>
              <td><span class="badge badge-gold"><?= e($dr['financial_year']) ?></span></td>
              <td style="font-size:.75rem;color:var(--t3);white-space:nowrap"><?= date('d M Y',strtotime($dr['effective_from'])) ?></td>
              <?php foreach (['road_30ft','road_40ft','road_60ft','road_80ft','road_100ft','near_highway'] as $f): ?>
              <td style="text-align:right">
                <?php if ($dr[$f]!==null): ?>
                <div class="dlc-sqm"><?= fmtSqm((float)$dr[$f]) ?></div>
                <div class="dlc-sqft">(<?= fmtSqft((float)$dr[$f]) ?>)</div>
                <?php else: ?><span style="color:var(--t4)">--</span><?php endif; ?>
              </td>
              <?php endforeach; ?>
              <?php if (is_admin()): ?>
              <td>
                <div style="display:flex;gap:4px">
                  <button onclick="openDlcModalEdit(<?= htmlspecialchars(json_encode($dr),ENT_QUOTES) ?>)" class="btn btn-icon">Edit</button>
                  <form method="POST" onsubmit="return confirm('Delete?')"><input type="hidden" name="action" value="delete_dlc"><input type="hidden" name="dlc_id" value="<?= $dr['id'] ?>"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><button type="submit" class="btn btn-danger btn-sm">Del</button></form>
                </div>
              </td>
              <?php endif; ?>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>

    <!-- ══════════════════════════════════════
         SUBSCRIPTIONS
    ══════════════════════════════════════ -->
    <?php elseif ($page==='subscriptions'): ?>

    <?php $errMap=['missing'=>'User, dates required.','invalid'=>'Invalid plan/cycle.','userdata'=>'Username &amp; password (6+ chars) required.','username'=>'Username: letters, numbers, underscore only.','exists'=>'Username already taken.'];
    if (isset($errMap[$err])): ?><div class="alert alert-danger"><?= $errMap[$err] ?></div><?php endif; ?>

    <div class="stats-grid" style="grid-template-columns:repeat(4,1fr)">
      <?php $ss=$subStats??[]; foreach ([['[S]','si-blue',$ss['total']??0,'Total'],['[A]','si-green',$ss['active']??0,'Active'],['[B]','si-gray',$ss['basic_count']??0,'Basic'],['[+]','si-gold',$ss['advance_count']??0,'Advance']] as [$ic,$sc,$sv,$sl]): ?>
      <div class="stat-card"><div class="stat-icon <?= $sc ?>"><?= $ic ?></div><div><div class="stat-val"><?= (int)$sv ?></div><div class="stat-lbl"><?= $sl ?></div></div></div>
      <?php endforeach; ?>
    </div>

    <?php if (!empty($upgradeReqs)): ?>
    <div class="card" style="margin-bottom:1.2rem">
      <div class="card-header"><h3>Pending Upgrade Requests</h3><span class="badge badge-red"><?= count($upgradeReqs) ?></span></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:9px">
        <?php foreach ($upgradeReqs as $req): ?>
        <div style="background:var(--gold-bg);border:1px solid #d4b090;border-radius:var(--r);padding:11px 13px">
          <div style="display:flex;align-items:flex-start;gap:9px;flex-wrap:wrap">
            <div style="flex:1;min-width:180px">
              <strong style="color:var(--t1)"><?= e($req['username']) ?><?= $req['full_name']?' - '.e($req['full_name']):'' ?></strong>
              <div style="font-size:.75rem;color:var(--t2);margin-top:1px">Requests: <strong><?= ucfirst($req['request_plan']) ?></strong> (<?= $req['billing_cycle'] ?>) - Current: <?= ucfirst($req['current_plan']) ?></div>
              <?php if ($req['message']): ?><div style="font-size:.73rem;color:var(--t3);margin-top:2px"><?= e($req['message']) ?></div><?php endif; ?>
              <div style="font-size:.67rem;color:var(--t4);margin-top:2px"><?= date('d M Y H:i',strtotime($req['created_at'])) ?></div>
            </div>
            <form method="POST" action="index.php" style="display:flex;gap:6px;align-items:flex-start;flex-wrap:wrap">
              <input type="hidden" name="action" value="review_upgrade"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><input type="hidden" name="request_id" value="<?= $req['id'] ?>">
              <input class="input" type="text" name="admin_note" placeholder="Note (optional)" style="width:150px;font-size:.77rem">
              <button type="submit" name="status" value="approved" class="btn btn-success btn-sm">Approve</button>
              <button type="submit" name="status" value="rejected" class="btn btn-danger btn-sm">Reject</button>
            </form>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:1fr 1.6fr;gap:1.2rem">
      <div style="display:flex;flex-direction:column;gap:1.1rem">
        <div class="card">
          <div class="card-header"><h3>Create Viewer Account</h3></div>
          <div class="card-body">
            <form method="POST" action="index.php">
              <input type="hidden" name="action" value="save_user"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
              <div class="form-field"><label>Username <span class="req">*</span></label><input class="input" type="text" name="new_username" required placeholder="letters_numbers" autocomplete="off"></div>
              <div class="form-field"><label>Full Name</label><input class="input" type="text" name="new_fullname" placeholder="e.g. Ramesh Patil"></div>
              <div class="form-field"><label>Password <span class="req">*</span></label><input class="input" type="password" name="new_password" placeholder="Min 6 characters" autocomplete="new-password"></div>
              <div class="form-field"><label>Role</label><select class="input" name="new_role"><option value="viewer">Viewer (subscription-gated)</option><option value="admin">Admin (full access)</option></select></div>
              <button type="submit" class="btn btn-primary btn-md">Create Account</button>
            </form>
          </div>
        </div>
        <div class="card">
          <div class="card-header"><h3>Assign Subscription</h3></div>
          <div class="card-body">
            <form method="POST" action="index.php">
              <input type="hidden" name="action" value="save_subscription"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><input type="hidden" name="sub_id" value="0">
              <div class="form-field"><label>User <span class="req">*</span></label><select class="input" name="sub_user_id" required><option value="">-- Select --</option><?php foreach ($viewerUsers as $vu): ?><option value="<?= $vu['id'] ?>"><?= e($vu['username']) ?><?= $vu['full_name']?' - '.e($vu['full_name']):'' ?></option><?php endforeach; ?></select></div>
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
              <button type="submit" class="btn btn-primary btn-md">Assign Subscription</button>
            </form>
          </div>
        </div>
        <div class="card">
          <div class="card-header"><h3>Pricing Reference</h3></div>
          <div class="card-body">
            <table style="width:100%;border-collapse:collapse">
              <?php foreach ([['Basic','Monthly',PLAN_BASIC_MONTHLY],['Basic','Yearly',PLAN_BASIC_YEARLY],['Advance','Monthly',PLAN_ADVANCE_MONTHLY],['Advance','Yearly',PLAN_ADVANCE_YEARLY]] as [$pl,$cy,$am]): ?>
              <tr><td style="padding:5px 0;border-bottom:1px solid var(--surface2);font-size:.8rem"><?= $pl ?></td><td style="padding:5px 0;border-bottom:1px solid var(--surface2);font-size:.77rem;color:var(--t3)"><?= $cy ?></td><td style="padding:5px 0;border-bottom:1px solid var(--surface2);text-align:right;font-weight:700;color:var(--gold-s);font-size:.82rem">Rs<?= number_format($am) ?></td></tr>
              <?php endforeach; ?>
            </table>
          </div>
        </div>
      </div>
      <div style="display:flex;flex-direction:column;gap:1.1rem">
        <div class="card">
          <div class="card-header"><h3>All Users</h3><span class="badge badge-blue"><?= count($allUsers) ?></span></div>
          <div class="table-wrap">
            <table>
              <thead><tr><th>User</th><th>Role</th><th>Active Plan</th><th>Expires</th></tr></thead>
              <tbody>
                <?php foreach ($allUsers as $au): ?>
                <tr>
                  <td><strong style="color:var(--t1)"><?= e($au['username']) ?></strong><?= $au['full_name']?'<br><small style="color:var(--t3)">'.e($au['full_name']).'</small>':'' ?></td>
                  <td><span class="badge <?= $au['role']==='admin'?'badge-blue':'badge-gray' ?>"><?= ucfirst($au['role']) ?></span></td>
                  <td><?php if ($au['role']==='admin'): ?><span class="badge badge-blue">Admin</span><?php elseif ($au['plan_type']&&$au['sub_active']): ?><span class="badge <?= $au['plan_type']==='advance'?'badge-gold':'badge-green' ?>"><?= ucfirst($au['plan_type']) ?></span><?php else: ?><span class="badge badge-gray">None</span><?php endif; ?></td>
                  <td style="font-size:.73rem;color:var(--t3)"><?= $au['end_date']?date('d M Y',strtotime($au['end_date'])):'--' ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="card">
          <div class="card-header"><h3>Subscription History</h3><span class="badge badge-blue"><?= count($allSubs) ?></span></div>
          <?php if (empty($allSubs)): ?><div style="padding:1.2rem;text-align:center;color:var(--t3);font-size:.82rem">No subscriptions yet.</div>
          <?php else: ?>
          <div class="table-wrap">
            <table>
              <thead><tr><th>User</th><th>Plan</th><th>Cycle</th><th>End</th><th>Rs</th><th>Status</th><th></th></tr></thead>
              <tbody>
                <?php foreach ($allSubs as $sub): $isExp=strtotime($sub['end_date'])<time(); $act=$sub['is_active']&&!$isExp; ?>
                <tr>
                  <td><strong style="color:var(--t1)"><?= e($sub['username']) ?></strong></td>
                  <td><span class="badge <?= $sub['plan_type']==='advance'?'badge-gold':'badge-green' ?>"><?= ucfirst($sub['plan_type']) ?></span></td>
                  <td style="font-size:.74rem;color:var(--t3)"><?= ucfirst($sub['billing_cycle']) ?></td>
                  <td style="font-size:.73rem;color:<?= $isExp?'var(--red)':'var(--t2)' ?>"><?= date('d M Y',strtotime($sub['end_date'])) ?></td>
                  <td style="font-weight:600;color:var(--gold-s);font-size:.75rem"><?= $sub['amount']?'Rs'.number_format((float)$sub['amount'],0):'--' ?></td>
                  <td><?php if ($act): ?><span class="badge badge-green">Active</span><?php elseif ($isExp): ?><span class="badge badge-gray">Expired</span><?php else: ?><span class="badge badge-red">Off</span><?php endif; ?></td>
                  <td>
                    <form method="POST" style="display:inline"><input type="hidden" name="action" value="toggle_subscription"><input type="hidden" name="sub_id" value="<?= $sub['id'] ?>"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><button type="submit" class="btn btn-icon" title="Toggle"><?= $sub['is_active']?'Off':'On' ?></button></form>
                    <form method="POST" style="display:inline" onsubmit="return confirm('Delete?')"><input type="hidden" name="action" value="delete_subscription"><input type="hidden" name="sub_id" value="<?= $sub['id'] ?>"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><button type="submit" class="btn btn-danger btn-sm">Del</button></form>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <script>
    var prices={basic:{monthly:<?= PLAN_BASIC_MONTHLY ?>,yearly:<?= PLAN_BASIC_YEARLY ?>},advance:{monthly:<?= PLAN_ADVANCE_MONTHLY ?>,yearly:<?= PLAN_ADVANCE_YEARLY ?>}};
    function updateAmt(){var p=document.getElementById('planType').value,c=document.getElementById('billCycle').value,a=document.getElementById('amtField');if(a)a.value=prices[p][c];calcEnd();}
    function calcEnd(){var s=document.getElementById('startDate').value,c=document.getElementById('billCycle').value,e=document.getElementById('endDate');if(!s||!e)return;var d=new Date(s);c==='yearly'?d.setFullYear(d.getFullYear()+1):d.setMonth(d.getMonth()+1);d.setDate(d.getDate()-1);e.value=d.toISOString().split('T')[0];}
    updateAmt();
    </script>

    <!-- ══════════════════════════════════════
         PROFILE
    ══════════════════════════════════════ -->
    <?php elseif ($page==='profile'): ?>

    <?php $pwErrMap=['wrongpw'=>'Current password incorrect.','short'=>'New password must be 6+ characters.','mismatch'=>'Passwords do not match.'];
    if ($msg==='pwchanged'): ?><div class="alert alert-success">Password changed successfully.</div>
    <?php elseif (isset($pwErrMap[$err])): ?><div class="alert alert-danger"><?= $pwErrMap[$err] ?></div><?php endif; ?>
    <?php if ($msg==='upgrade_requested'): ?><div class="alert alert-success">Upgrade request submitted. Admin will review shortly.</div>
    <?php elseif ($err==='already_requested'): ?><div class="alert alert-warning">You already have a pending upgrade request.</div><?php endif; ?>

    <div class="two-col">
      <div style="display:flex;flex-direction:column;gap:1.1rem">
        <div class="card">
          <div class="card-header"><h3>Account Info</h3></div>
          <div class="card-body">
            <div style="display:flex;align-items:center;gap:11px;margin-bottom:1.1rem">
              <div style="width:44px;height:44px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#fff;font-weight:700;flex-shrink:0"><?= strtoupper(substr($user['username'],0,1)) ?></div>
              <div><div style="font-size:.95rem;font-weight:700;color:var(--t1)"><?= e($user['full_name']?:$user['username']) ?></div><div style="font-size:.73rem;color:var(--t3)"><?= e($user['username']) ?> - <?= ucfirst($user['role']) ?></div></div>
            </div>
            <table style="width:100%;border-collapse:collapse">
              <?php foreach ([['Username',$user['username']],['Full Name',$user['full_name']?:'--'],['Role',ucfirst($user['role'])]] as [$l,$v]): ?>
              <tr><td style="padding:5px 0;border-bottom:1px solid var(--surface2);font-size:.67rem;font-weight:700;color:var(--t3);text-transform:uppercase"><?= $l ?></td><td style="padding:5px 0;border-bottom:1px solid var(--surface2);font-size:.81rem;color:var(--t1);text-align:right"><?= e($v) ?></td></tr>
              <?php endforeach; ?>
            </table>
          </div>
        </div>
        <div class="card">
          <div class="card-header"><h3>Change Password</h3></div>
          <div class="card-body">
            <form method="POST" action="index.php">
              <input type="hidden" name="action" value="change_password"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
              <div class="form-field"><label>Current Password</label><input class="input" type="password" name="current_password" required placeholder="Current password"></div>
              <div class="form-field"><label>New Password</label><input class="input" type="password" name="new_password" required placeholder="Min 6 characters"></div>
              <div class="form-field"><label>Confirm Password</label><input class="input" type="password" name="confirm_password" required placeholder="Repeat new password"></div>
              <button type="submit" class="btn btn-primary btn-md">Update Password</button>
            </form>
          </div>
        </div>
      </div>
      <div style="display:flex;flex-direction:column;gap:1.1rem">
        <div class="card">
          <div class="card-header"><h3>My Subscription</h3></div>
          <div class="card-body">
            <?php if ($mySub): ?>
            <div style="text-align:center;padding:.9rem 0;margin-bottom:1rem">
              <div style="font-size:2.2rem;margin-bottom:.4rem">[STAR]</div>
              <div style="font-size:1.1rem;font-weight:700;color:var(--t1)"><?= ucfirst($mySub['plan_type']) ?> Plan</div>
              <div style="font-size:.77rem;color:var(--t3);margin-top:2px"><?= ucfirst($mySub['billing_cycle']) ?> - Expires <?= date('d M Y',strtotime($mySub['end_date'])) ?></div>
            </div>
            <?php else: ?><div style="text-align:center;padding:1.2rem;color:var(--t3)"><div style="font-size:2rem;margin-bottom:.4rem">[--]</div><div style="font-size:.84rem">No active subscription</div></div><?php endif; ?>
            <?php if (!is_admin()&&(!$mySub||$mySub['plan_type']==='basic')): ?>
            <div class="divider"></div>
            <h4 style="font-size:.84rem;margin-bottom:.8rem;color:var(--t1)">Request Upgrade</h4>
            <form method="POST" action="index.php">
              <input type="hidden" name="action" value="request_upgrade"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                <div class="form-field"><label>Plan</label><select class="input" name="request_plan"><?php if (!$mySub): ?><option value="basic">Basic</option><?php endif; ?><option value="advance">Advance</option></select></div>
                <div class="form-field"><label>Billing</label><select class="input" name="billing_cycle"><option value="monthly">Monthly</option><option value="yearly">Yearly</option></select></div>
              </div>
              <div class="form-field"><label>Message (optional)</label><textarea class="input" name="message" style="min-height:50px" placeholder="Any notes for the admin..."></textarea></div>
              <button type="submit" class="btn btn-primary btn-md">Submit Request</button>
            </form>
            <?php endif; ?>
          </div>
        </div>
        <?php if (!empty($myRequests)): ?>
        <div class="card">
          <div class="card-header"><h3>My Requests</h3></div>
          <div class="card-body" style="display:flex;flex-direction:column;gap:7px">
            <?php foreach ($myRequests as $req): ?>
            <div style="background:var(--surface2);border:1px solid var(--border);border-radius:var(--r);padding:9px 11px;display:flex;align-items:center;justify-content:space-between;gap:9px;flex-wrap:wrap">
              <div><div style="font-size:.81rem;font-weight:600;color:var(--t1)"><?= ucfirst($req['request_plan']) ?> - <?= ucfirst($req['billing_cycle']) ?></div><div style="font-size:.69rem;color:var(--t3)"><?= date('d M Y',strtotime($req['created_at'])) ?></div><?php if ($req['admin_note']): ?><div style="font-size:.71rem;color:var(--t2);margin-top:1px">Note: <?= e($req['admin_note']) ?></div><?php endif; ?></div>
              <span class="badge <?= $req['status']==='approved'?'badge-green':($req['status']==='rejected'?'badge-red':'badge-gold') ?>"><?= ucfirst($req['status']) ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- ══════════════════════════════════════
         SETTINGS
    ══════════════════════════════════════ -->
    <?php elseif ($page==='settings'): ?>

    <?php if ($msg==='saved'): ?><div class="alert alert-success">Settings saved successfully.</div><?php endif; ?>

    <form method="POST" action="index.php">
      <input type="hidden" name="action" value="save_settings">
      <input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">

      <div class="settings-section">
        <h4>Scrolling Announcement (Marquee)</h4>
        <div class="form-field">
          <label>Marquee Text</label>
          <textarea class="input" name="marquee_text" placeholder="Enter scrolling announcement text..."><?= e($sMarqueeText) ?></textarea>
          <small style="color:var(--t3);font-size:.7rem">This text scrolls across the top of every page. Leave blank to hide.</small>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.9rem">
          <div class="form-field">
            <label>Enable Marquee</label>
            <div style="display:flex;align-items:center;gap:10px;padding:9px 0">
              <input type="checkbox" name="marquee_enabled" id="mqEnabled" value="1" <?= $sMarqueeEnabled==='1'?'checked':'' ?> style="width:16px;height:16px;cursor:pointer">
              <label for="mqEnabled" style="font-size:.83rem;color:var(--t2);text-transform:none;letter-spacing:0;cursor:pointer">Show marquee bar on all pages</label>
            </div>
          </div>
          <div class="form-field">
            <label>Scroll Speed (seconds for one pass)</label>
            <input class="input" type="number" name="marquee_speed" min="10" max="300" step="5" value="<?= e($sMarqueeSpeed) ?>" placeholder="60">
            <small style="color:var(--t3);font-size:.7rem">Lower = faster. Default: 60</small>
          </div>
        </div>
        <div style="background:var(--primary-bg);border:1px solid #a0bcd4;border-radius:var(--r);padding:9px 13px;margin-top:.5rem">
          <div style="font-size:.72rem;font-weight:700;color:var(--t3);margin-bottom:4px">PREVIEW</div>
          <div style="background:var(--primary);color:#fff;padding:4px 0;overflow:hidden;border-radius:4px;white-space:nowrap">
            <span id="mqPreviewText" style="display:inline-block;padding:0 16px;font-size:.8rem"><?= e($sMarqueeText) ?></span>
          </div>
        </div>
        <script>
        document.getElementById('marquee_text')?.addEventListener('input',function(){
          var t=document.getElementById('mqPreviewText');if(t)t.textContent=this.value||'(empty)';
        });
        </script>
      </div>

      <div class="settings-section">
        <h4>Footer Text</h4>
        <div class="form-field">
          <label>Footer Text (shown below Sign Out button in sidebar)</label>
          <textarea class="input" name="footer_text" placeholder="e.g. PMS By Mingosoft Technologies &amp;copy; 2025"><?= e($sFooterText) ?></textarea>
          <small style="color:var(--t3);font-size:.7rem">HTML allowed (e.g. &amp;copy; for copyright symbol). Leave blank to hide footer.</small>
        </div>
        <?php if (trim($sFooterText)!==''): ?>
        <div style="background:var(--surface2);border:1px solid var(--border);border-radius:var(--r);padding:8px 12px;margin-top:.4rem">
          <div style="font-size:.72rem;font-weight:700;color:var(--t3);margin-bottom:3px">PREVIEW</div>
          <div style="font-size:.65rem;color:var(--t4);text-align:center;line-height:1.5"><?= $sFooterText ?></div>
        </div>
        <?php endif; ?>
      </div>

      <div style="display:flex;gap:9px">
        <button type="submit" class="btn btn-primary btn-md">Save Settings</button>
        <a href="index.php" class="btn btn-ghost btn-md">Cancel</a>
      </div>
    </form>

    <!-- DEVELOPER PLAN FORM -->
    <?php elseif ($page==='dev_plan'): ?>
    <?php if ($err==='missing'): ?><div class="alert alert-danger">Plan name and Aaraji number are required.</div>
    <?php elseif ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
    <?php if (!$editDp): ?><div class="alert alert-info" style="font-size:.8rem">After submission, your plan will be reviewed by admin before it appears publicly.</div><?php endif; ?>

    <div class="card" style="max-width:820px">
      <div class="card-header">
        <h3><?= $editDp?'Edit Developer Plan: '.e($editDp['plan_name']):'Submit New Developer Plan' ?></h3>
        <a href="index.php?page=dev_plans" class="btn btn-ghost btn-sm">Back</a>
      </div>
      <div class="card-body">
        <form method="POST" action="index.php" enctype="multipart/form-data">
          <input type="hidden" name="action" value="save_dev_plan">
          <input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>">
          <?php if ($editDp): ?><input type="hidden" name="dp_id" value="<?= (int)$editDp['id'] ?>"><?php endif; ?>

          <div style="font-size:.75rem;font-weight:700;color:var(--primary-d);text-transform:uppercase;letter-spacing:.06em;margin-bottom:.7rem;padding-bottom:.4rem;border-bottom:1px solid var(--border)">Plan Details</div>
          <div class="form-grid">
            <div class="form-field"><label>Plan Name <span class="req">*</span></label><input class="input" type="text" name="plan_name" required placeholder="e.g. Green Valley Residency" value="<?= e($editDp['plan_name']??'') ?>"></div>
            <div class="form-field"><label>Aaraji Number <span class="req">*</span></label><input class="input" type="text" name="aaraji_number" required placeholder="e.g. ARJ/2024/0012" value="<?= e($editDp['aaraji_number']??'') ?>"></div>
            <div class="form-field"><label>Contact Number</label><input class="input" type="text" name="contact_number" placeholder="e.g. +91 98765 43210" value="<?= e($editDp['contact_number']??'') ?>"></div>
            <div class="form-field">
              <label>Revenue Village</label>
              <select class="input" name="village_id"><option value="">-- Select --</option>
                <?php foreach ($villagesAll as $v): ?><option value="<?= $v['id'] ?>" <?= (($editDp['village_id']??0)==$v['id'])?'selected':'' ?>><?= e($v['name']) ?><?= $v['tehsil']?' - '.e($v['tehsil']):'' ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="form-field fg-full">
              <label>Google Maps Location URL</label>
              <input class="input" type="url" name="google_location" placeholder="Paste Google Maps link..." value="<?= e($editDp['google_location']??'') ?>">
            </div>
          </div>

          <div style="font-size:.75rem;font-weight:700;color:var(--primary-d);text-transform:uppercase;letter-spacing:.06em;margin:.9rem 0 .7rem;padding-bottom:.4rem;border-bottom:1px solid var(--border)">Plan Files</div>
          <div class="form-grid">
            <div class="form-field">
              <label>Plan Image or PDF</label>
              <div class="upload-zone" style="padding:1rem">
                <input type="file" name="plan_file" accept="image/*,.pdf">
                <p style="font-size:.78rem">Click to upload plan image or PDF</p>
                <small>Max <?= MAX_FILE_MB ?>MB</small>
              </div>
              <?php if ($editDp&&$editDp['file_name']): ?><p style="margin-top:5px;font-size:.72rem;color:var(--t3)">Current: <strong><?= e($editDp['file_name']) ?></strong></p><?php endif; ?>
            </div>
            <div class="form-field">
              <label>Approved Plan Map (Image or PDF)</label>
              <div class="upload-zone" style="padding:1rem">
                <input type="file" name="approved_map" accept="image/*,.pdf">
                <p style="font-size:.78rem">Upload approved plan map from authority</p>
                <small>Max <?= MAX_FILE_MB ?>MB</small>
              </div>
              <?php if ($editDp&&$editDp['approved_map_name']): ?><p style="margin-top:5px;font-size:.72rem;color:var(--t3)">Current: <strong><?= e($editDp['approved_map_name']) ?></strong></p><?php endif; ?>
            </div>
          </div>

          <div style="font-size:.75rem;font-weight:700;color:var(--primary-d);text-transform:uppercase;letter-spacing:.06em;margin:.9rem 0 .7rem;padding-bottom:.4rem;border-bottom:1px solid var(--border)">Pricing by Road Width</div>
          <div class="form-field" style="margin-bottom:.5rem">
            <label>Price Unit</label>
            <select class="input" name="price_unit" style="width:auto;min-width:130px">
              <option value="sq.ft" <?= ($editDp['price_unit']??'sq.ft')==='sq.ft'?'selected':'' ?>>Per sq.ft</option>
              <option value="sq.m"  <?= ($editDp['price_unit']??'')==='sq.m'?'selected':'' ?>>Per sq.m</option>
            </select>
          </div>
          <div style="background:var(--surface2);border:1px solid var(--border);border-radius:var(--r);padding:12px;margin-bottom:.9rem">
            <?php foreach([['price_30ft','30 ft Road'],['price_40ft','40 ft Road'],['price_60ft','60 ft Road'],['price_80ft','80 ft Road'],['price_100ft','100 ft Road'],['price_highway','Near Highway']] as [$fn,$fl]): ?>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:7px">
              <label style="width:100px;flex-shrink:0;font-size:.73rem;font-weight:600;color:var(--t2);margin:0"><?= $fl ?></label>
              <div style="position:relative;flex:1"><span style="position:absolute;left:9px;top:50%;transform:translateY(-50%);color:var(--t3);font-size:.82rem;pointer-events:none">Rs</span><input class="input" type="number" name="<?= $fn ?>" step="0.01" min="0" placeholder="0.00" style="padding-left:26px" value="<?= $editDp?e($editDp[$fn]??''):'' ?>"></div>
            </div>
            <?php endforeach; ?>
          </div>

          <div style="font-size:.75rem;font-weight:700;color:var(--primary-d);text-transform:uppercase;letter-spacing:.06em;margin:.9rem 0 .7rem;padding-bottom:.4rem;border-bottom:1px solid var(--border)">Brokerage</div>
          <div class="form-grid">
            <div class="form-field"><label>Brokerage Rate (%)</label><input class="input" type="number" name="brokerage_rate" step="0.01" min="0" max="100" placeholder="e.g. 2.5" value="<?= e($editDp['brokerage_rate']??'') ?>"></div>
            <div class="form-field"><label>Brokerage Notes</label><input class="input" type="text" name="brokerage_notes" placeholder="e.g. Negotiable for bulk deals" value="<?= e($editDp['brokerage_notes']??'') ?>"></div>
          </div>

          <div class="form-field"><label>Additional Notes</label><textarea class="input" name="notes" placeholder="Any additional details about this property..."><?= e($editDp['notes']??'') ?></textarea></div>

          <div class="divider"></div>
          <div style="display:flex;gap:9px;flex-wrap:wrap">
            <a href="index.php?page=dev_plans" class="btn btn-ghost btn-md">Cancel</a>
            <button type="submit" class="btn btn-primary btn-md"><?= $editDp?'Update Plan':'Submit for Approval' ?></button>
          </div>
        </form>
      </div>
    </div>

    <!-- DEVELOPER PLANS LIST -->
    <?php elseif ($page==='dev_plans'): ?>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.1rem;flex-wrap:wrap;gap:8px">
      <div style="font-size:.84rem;color:var(--t3)">Your submitted developer plans. Approved plans are visible to all users.</div>
      <a href="index.php?page=dev_plan" class="btn btn-primary btn-sm">+ Add New Plan</a>
    </div>

    <?php if (empty($dplans)): ?>
    <div style="text-align:center;padding:3rem;color:var(--t3)"><div style="font-size:2rem;margin-bottom:.7rem">[DEV]</div><h3 style="margin-bottom:.4rem">No developer plans yet</h3><p style="font-size:.82rem">Submit your first property plan for admin approval.</p></div>
    <?php else: ?>
    <div class="card">
      <div class="table-wrap">
        <table>
          <thead><tr><th>Plan Name</th><th>Aaraji #</th><th>Village</th><?php if (is_admin()): ?><th>Developer</th><?php endif; ?><th>Status</th><th>Sponsored</th><th>Submitted</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($dplans as $dp): ?>
            <tr>
              <td><strong style="color:var(--t1)"><?= e($dp['plan_name']) ?></strong><?= $dp['contact_number']?'<br><small style="color:var(--t3)">'.e($dp['contact_number']).'</small>':'' ?></td>
              <td style="font-size:.78rem;color:var(--primary-d);font-weight:600"># <?= e($dp['aaraji_number']) ?></td>
              <td style="font-size:.79rem"><?= e($dp['village_name']??'--') ?></td>
              <?php if (is_admin()): ?><td style="font-size:.78rem;color:var(--t3)"><?= e($dp['dev_name']??'--') ?></td><?php endif; ?>
              <td>
                <?php if ($dp['status']==='approved'): ?><span class="badge badge-green">Approved</span>
                <?php elseif ($dp['status']==='rejected'): ?><span class="badge badge-red">Rejected<?= $dp['admin_note']?'<br><small style="font-size:.6rem">'.e($dp['admin_note']).'</small>':'' ?></span>
                <?php else: ?><span class="badge badge-gold">Pending</span><?php endif; ?>
              </td>
              <td><?= $dp['is_sponsored']?'<span class="badge badge-gold">'.e($dp['sponsored_label']).'</span>':'<span style="color:var(--t4);font-size:.75rem">--</span>' ?></td>
              <td style="font-size:.73rem;color:var(--t3)"><?= date('d M Y',strtotime($dp['created_at'])) ?></td>
              <td>
                <div style="display:flex;gap:4px;flex-wrap:wrap">
                  <?php if ($dp['status']==='approved'): ?><a href="index.php?page=dev_view&id=<?= $dp['id'] ?>" class="btn btn-primary btn-sm">View</a><?php endif; ?>
                  <?php if (is_admin()||$dp['created_by']==current_user()['id']): ?>
                  <a href="index.php?page=dev_plan&id=<?= $dp['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                  <?php endif; ?>
                  <?php if (is_admin()): ?>
                  <form method="POST" onsubmit="return confirm('Delete?')"><input type="hidden" name="action" value="delete_dev_plan"><input type="hidden" name="dp_id" value="<?= $dp['id'] ?>"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><button type="submit" class="btn btn-danger btn-sm">Del</button></form>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>

    <!-- DEVELOPER PLAN DETAIL VIEW -->
    <?php elseif ($page==='dev_view'): ?>
    <div style="display:flex;gap:8px;margin-bottom:1.1rem;align-items:center;flex-wrap:wrap">
      <a href="index.php" class="btn btn-ghost btn-sm">Back to Dashboard</a>
      <?php if (is_admin()||$devplan['created_by']==current_user()['id']): ?>
      <a href="index.php?page=dev_plan&id=<?= $devplan['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
      <?php endif; ?>
      <?php if ($devplan['google_location']): ?><a href="<?= e($devplan['google_location']) ?>" target="_blank" rel="noopener" class="btn btn-ghost btn-sm">Open in Maps</a><?php endif; ?>
      <?php if ($devplan['is_sponsored']): ?><span class="badge badge-gold" style="padding:5px 12px"><?= e($devplan['sponsored_label']) ?></span><?php endif; ?>
    </div>

    <div class="two-col">
      <!-- Left: Images -->
      <div>
        <?php if ($devplan['file_type']==='image'&&$devplan['file_path']): ?>
        <div class="card" style="margin-bottom:1.1rem;overflow:hidden">
          <div style="background:var(--surface2);border-bottom:1px solid var(--border);padding:6px 12px;font-size:.72rem;font-weight:700;color:var(--t3);text-transform:uppercase">Plan Image</div>
          <img src="<?= e($devplan['file_path']) ?>" alt="Plan" class="zoomable" onclick="openZoom(this.src)" style="width:100%;display:block;max-height:300px;object-fit:contain;background:var(--surface2)">
          <div style="padding:7px 12px;background:var(--surface2);border-top:1px solid var(--border);text-align:right"><a href="<?= e($devplan['file_path']) ?>" download class="btn btn-secondary btn-sm">Download Plan</a></div>
        </div>
        <?php elseif ($devplan['file_path']): ?>
        <div class="card" style="margin-bottom:1.1rem"><div style="padding:1.5rem;text-align:center;background:var(--surface2)"><div style="font-size:2rem;margin-bottom:.5rem">[PDF]</div><p style="font-size:.83rem;margin-bottom:.8rem"><?= e($devplan['file_name']) ?></p><a href="<?= e($devplan['file_path']) ?>" target="_blank" class="btn btn-secondary btn-sm">Open PDF</a></div></div>
        <?php endif; ?>

        <?php if ($devplan['approved_map_path']): ?>
        <div class="card" style="margin-bottom:1.1rem;overflow:hidden">
          <div style="background:var(--green-bg);border-bottom:1px solid var(--border);padding:6px 12px;font-size:.72rem;font-weight:700;color:var(--green);text-transform:uppercase">Approved Plan Map</div>
          <?php if ($devplan['approved_map_type']==='image'): ?>
          <img src="<?= e($devplan['approved_map_path']) ?>" alt="Approved Map" class="zoomable" onclick="openZoom(this.src)" style="width:100%;display:block;max-height:260px;object-fit:contain;background:var(--surface2)">
          <?php else: ?>
          <div style="padding:1.5rem;text-align:center;background:var(--surface2)"><div style="font-size:2rem;margin-bottom:.5rem">[PDF]</div><p style="font-size:.83rem;margin-bottom:.8rem"><?= e($devplan['approved_map_name']) ?></p></div>
          <?php endif; ?>
          <div style="padding:7px 12px;background:var(--green-bg);border-top:1px solid var(--border);text-align:right"><a href="<?= e($devplan['approved_map_path']) ?>" download class="btn btn-success btn-sm">Download Approved Map</a></div>
        </div>
        <?php endif; ?>

        <?php if ($devplan['google_location']): $emb=embedUrl($devplan['google_location']); ?>
        <div class="map-embed">
          <?php if ($emb): ?><iframe src="<?= e($emb) ?>" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          <?php else: ?><div style="height:150px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:5px;background:var(--surface2)"><div>[MAP]</div><a href="<?= e($devplan['google_location']) ?>" target="_blank">Open in Google Maps</a></div><?php endif; ?>
        </div>
        <?php endif; ?>
      </div>

      <!-- Right: Details -->
      <div>
        <div class="card" style="margin-bottom:1.1rem">
          <div class="card-header"><h3><?= e($devplan['plan_name']) ?></h3><span class="badge badge-green">Developer Plan</span></div>
          <div class="card-body">
            <table style="width:100%;border-collapse:collapse">
              <?php $rows=[['Aaraji No.',$devplan['aaraji_number']],['Village',$devplan['village_name']??'--'],['Tehsil',$devplan['tehsil']??'--'],['District',$devplan['district']??'--'],['Developer',$devplan['dev_fullname']?:$devplan['dev_username']],['Contact',$devplan['contact_number']??'--']];
              foreach($rows as[$l,$v]): ?>
              <tr><td style="padding:6px 0;border-bottom:1px solid var(--surface2);font-size:.67rem;font-weight:700;color:var(--t3);text-transform:uppercase;width:38%"><?= e($l) ?></td><td style="padding:6px 0;border-bottom:1px solid var(--surface2);font-size:.81rem;color:var(--t1);text-align:right"><?= e($v) ?></td></tr>
              <?php endforeach; ?>
            </table>
            <?php if ($devplan['notes']): ?><div style="margin-top:.9rem;font-size:.82rem;color:var(--t2);line-height:1.7"><?= nl2br(e($devplan['notes'])) ?></div><?php endif; ?>
          </div>
        </div>

        <!-- Pricing -->
        <?php $hasPricing=array_filter([$devplan['price_30ft'],$devplan['price_40ft'],$devplan['price_60ft'],$devplan['price_80ft'],$devplan['price_100ft'],$devplan['price_highway']],fn($v)=>$v!==null); ?>
        <?php if ($hasPricing): ?>
        <div class="card" style="margin-bottom:1.1rem">
          <div class="card-header"><h3>Pricing (per <?= e($devplan['price_unit']) ?>)</h3></div>
          <div class="card-body">
            <table style="width:100%;border-collapse:collapse">
              <tr><th style="padding:5px 0;font-size:.67rem;font-weight:700;color:var(--t3);text-transform:uppercase;border-bottom:1px solid var(--border);text-align:left">Road</th><th style="padding:5px 4px;font-size:.67rem;font-weight:700;color:var(--t3);text-transform:uppercase;border-bottom:1px solid var(--border);text-align:right">Price</th></tr>
              <?php foreach([['30 ft','price_30ft'],['40 ft','price_40ft'],['60 ft','price_60ft'],['80 ft','price_80ft'],['100 ft','price_100ft'],['Highway','price_highway']] as[$lbl,$fld]):
              if($devplan[$fld]===null) continue; ?>
              <tr><td style="padding:5px 0;border-bottom:1px solid var(--surface2);font-size:.79rem"><?= $lbl ?></td><td style="padding:5px 0;border-bottom:1px solid var(--surface2);text-align:right;font-weight:700;color:var(--primary-d);font-size:.82rem">Rs <?= number_format((float)$devplan[$fld],2) ?>/<?= e($devplan['price_unit']) ?></td></tr>
              <?php endforeach; ?>
            </table>
          </div>
        </div>
        <?php endif; ?>

        <!-- Brokerage -->
        <?php if ($devplan['brokerage_rate']): ?>
        <div class="card" style="margin-bottom:1.1rem">
          <div class="card-header"><h3>Brokerage</h3></div>
          <div class="card-body">
            <div style="font-size:1.2rem;font-weight:700;color:var(--gold-s);margin-bottom:.4rem"><?= number_format((float)$devplan['brokerage_rate'],2) ?>%</div>
            <?php if ($devplan['brokerage_notes']): ?><p style="font-size:.81rem;color:var(--t2)"><?= e($devplan['brokerage_notes']) ?></p><?php endif; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- ADMIN APPROVALS -->
    <?php elseif ($page==='dev_approvals'): ?>
    <?php if ($pendingCount>0): ?>
    <div class="alert alert-warning"><?= $pendingCount ?> developer plan(s) awaiting your review.</div>
    <?php endif; ?>

    <?php if (!empty($pending)): ?>
    <div class="card" style="margin-bottom:1.3rem">
      <div class="card-header"><h3>Pending Approvals</h3><span class="badge badge-gold"><?= count($pending) ?></span></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:10px">
        <?php foreach ($pending as $dp): ?>
        <div style="background:var(--gold-bg);border:1px solid #d4b090;border-radius:var(--r);padding:12px 14px">
          <div style="display:flex;align-items:flex-start;gap:10px;flex-wrap:wrap">
            <div style="flex:1;min-width:200px">
              <strong style="color:var(--t1)"><?= e($dp['plan_name']) ?></strong> <span style="font-size:.75rem;color:var(--gold-s)"># <?= e($dp['aaraji_number']) ?></span>
              <div style="font-size:.75rem;color:var(--t2);margin-top:2px">Developer: <strong><?= e($dp['dev_fullname']?:$dp['dev_name']) ?></strong> | Village: <?= e($dp['village_name']??'--') ?></div>
              <?php if ($dp['contact_number']): ?><div style="font-size:.72rem;color:var(--t3)">Contact: <?= e($dp['contact_number']) ?></div><?php endif; ?>
              <div style="font-size:.68rem;color:var(--t4);margin-top:2px">Submitted: <?= date('d M Y H:i',strtotime($dp['created_at'])) ?></div>
              <div style="margin-top:6px;display:flex;gap:5px">
                <?php if ($dp['file_path']): ?><a href="<?= e($dp['file_path']) ?>" target="_blank" class="btn btn-ghost btn-sm">View Plan</a><?php endif; ?>
                <?php if ($dp['approved_map_path']): ?><a href="<?= e($dp['approved_map_path']) ?>" target="_blank" class="btn btn-ghost btn-sm">View Map</a><?php endif; ?>
              </div>
            </div>
            <form method="POST" action="index.php" style="display:flex;gap:6px;align-items:flex-start;flex-wrap:wrap;flex-shrink:0">
              <input type="hidden" name="action" value="review_dev_plan"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><input type="hidden" name="dp_id" value="<?= $dp['id'] ?>">
              <input class="input" type="text" name="admin_note" placeholder="Note (optional)" style="width:150px;font-size:.78rem">
              <button type="submit" name="status" value="approved" class="btn btn-success btn-sm">Approve</button>
              <button type="submit" name="status" value="rejected" class="btn btn-danger btn-sm">Reject</button>
            </form>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Approved plans with sponsorship toggle -->
    <div class="card">
      <div class="card-header"><h3>Approved Developer Plans</h3><span class="badge badge-green"><?= count($approved) ?></span></div>
      <?php if (empty($approved)): ?><div style="padding:1.5rem;text-align:center;color:var(--t3);font-size:.82rem">No approved plans yet.</div>
      <?php else: ?>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Plan Name</th><th>Developer</th><th>Village</th><th>Approved</th><th>Sponsored</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($approved as $dp): ?>
            <tr>
              <td><strong style="color:var(--t1)"><?= e($dp['plan_name']) ?></strong><br><small style="color:var(--primary-d)"># <?= e($dp['aaraji_number']) ?></small></td>
              <td style="font-size:.79rem"><?= e($dp['dev_name']) ?></td>
              <td style="font-size:.79rem"><?= e($dp['village_name']??'--') ?></td>
              <td style="font-size:.73rem;color:var(--t3)"><?= $dp['approved_at']?date('d M Y',strtotime($dp['approved_at'])):'--' ?></td>
              <td><?= $dp['is_sponsored']?'<span class="badge badge-gold">'.e($dp['sponsored_label']).'</span>':'<span style="color:var(--t4);font-size:.74rem">Not sponsored</span>' ?></td>
              <td>
                <div style="display:flex;gap:5px;align-items:center;flex-wrap:wrap">
                  <a href="index.php?page=dev_view&id=<?= $dp['id'] ?>" class="btn btn-primary btn-sm">View</a>
                  <!-- Sponsored toggle -->
                  <form method="POST" style="display:flex;align-items:center;gap:5px">
                    <input type="hidden" name="action" value="toggle_sponsored"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><input type="hidden" name="dp_id" value="<?= $dp['id'] ?>">
                    <input class="input" type="text" name="sponsored_label" value="<?= e($dp['sponsored_label']??'Sponsored') ?>" placeholder="Label" style="width:100px;font-size:.74rem;padding:4px 8px">
                    <button type="submit" class="btn btn-sm <?= $dp['is_sponsored']?'btn-secondary':'btn-success' ?>"><?= $dp['is_sponsored']?'Unfeature':'Feature' ?></button>
                  </form>
                  <form method="POST" onsubmit="return confirm('Delete?')"><input type="hidden" name="action" value="delete_dev_plan"><input type="hidden" name="dp_id" value="<?= $dp['id'] ?>"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><button type="submit" class="btn btn-danger btn-sm">Del</button></form>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>

    <!-- PERMISSION MATRIX -->
    <?php elseif ($page==='permissions'): ?>
    <div class="card" style="margin-bottom:1.3rem">
      <div class="card-header"><h3>Permission Matrix</h3><span class="badge badge-blue">v<?= APP_VER ?></span></div>
      <div class="card-body">
        <p style="font-size:.82rem;color:var(--t3);margin-bottom:1rem">This table shows what each user role can do in <?= e(APP_BRAND) ?>.</p>
        <div class="table-wrap">
          <table>
            <thead>
              <tr style="background:var(--primary);color:#fff">
                <th style="color:#fff;background:var(--primary)">Feature / Action</th>
                <th style="color:#fff;background:var(--primary);text-align:center">Admin</th>
                <th style="color:#fff;background:var(--primary-h);text-align:center">Developer</th>
                <th style="color:#fff;background:var(--gold);text-align:center">Viewer (Advance)</th>
                <th style="color:#fff;background:var(--green);text-align:center">Viewer (Basic)</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $yes='<span style="color:var(--green);font-weight:700;font-size:1rem">YES</span>';
              $no='<span style="color:var(--red);font-weight:700;font-size:.85rem">NO</span>';
              $matrix=[
                ['AARAJI PLANS','','','',''],
                ['View plan list',$yes,$yes,$yes,$yes],
                ['View plan image',$yes,$yes,$yes,$yes],
                ['View Google Maps location',$yes,$yes,$yes,$yes],
                ['View DLC rates',$yes,$yes,$yes,$no],
                ['Download plan image/PDF',$yes,$yes,$yes,$no],
                ['View/Download chain docs',$yes,$yes,$yes,$no],
                ['Add/Register plan',$yes,$no,$no,$no],
                ['Edit plan',$yes,$no,$no,$no],
                ['Delete plan',$yes,$no,$no,$no],
                ['DEVELOPER PLANS','','','',''],
                ['View approved developer plans',$yes,$yes,$yes,$yes],
                ['Add developer plan',$yes,$yes,$no,$no],
                ['Edit own developer plan',$yes,'<span style="color:var(--primary-d);font-weight:600;font-size:.8rem">Own only</span>',$no,$no],
                ['Delete developer plan',$yes,$no,$no,$no],
                ['Approve/Reject developer plan',$yes,$no,$no,$no],
                ['Mark plan as Sponsored/Featured',$yes,$no,$no,$no],
                ['DLC RATES','','','',''],
                ['View DLC rates',$yes,$yes,$yes,$no],
                ['Add/Edit DLC rates',$yes,$no,$no,$no],
                ['Delete DLC rates',$yes,$no,$no,$no],
                ['Export DLC to Excel',$yes,$yes,$yes,$no],
                ['Import DLC via CSV',$yes,$no,$no,$no],
                ['REVENUE VILLAGES','','','',''],
                ['View villages list',$yes,$yes,$yes,$yes],
                ['Add/Edit village',$yes,$no,$no,$no],
                ['Delete village',$yes,$no,$no,$no],
                ['SUBSCRIPTIONS','','','',''],
                ['Create user accounts',$yes,$no,$no,$no],
                ['Assign subscriptions',$yes,$no,$no,$no],
                ['View subscription history',$yes,$no,$no,$no],
                ['Request plan upgrade',$no,$no,$yes,$yes],
                ['SETTINGS','','','',''],
                ['Access settings page',$yes,$no,$no,$no],
                ['Edit marquee text',$yes,$no,$no,$no],
                ['Edit footer text',$yes,$no,$no,$no],
              ];
              foreach ($matrix as $row):
                if (!$row[1]): ?>
              <tr style="background:var(--surface2)"><td colspan="5" style="padding:6px 11px;font-size:.72rem;font-weight:700;color:var(--primary-d);text-transform:uppercase;letter-spacing:.07em"><?= $row[0] ?></td></tr>
                <?php else: ?>
              <tr><td style="font-size:.83rem;color:var(--t1);font-weight:500"><?= $row[0] ?></td><td style="text-align:center"><?= $row[1] ?></td><td style="text-align:center"><?= $row[2] ?></td><td style="text-align:center"><?= $row[3] ?></td><td style="text-align:center"><?= $row[4] ?></td></tr>
                <?php endif;
              endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3>Role Descriptions</h3></div>
      <div class="card-body">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px">
          <?php foreach([
            ['Admin','var(--primary)','Full system access. Can manage all plans, users, subscriptions, DLC rates, settings and approve developer plans.'],
            ['Developer','var(--primary-h)','Can submit property listings for admin approval. Can add pricing and brokerage. Cannot access admin tools.'],
            ['Viewer (Advance)','var(--gold-s)','Can view all plans, download files, view DLC rates and chain documents. Requires active Advance subscription.'],
            ['Viewer (Basic)','var(--green)','Can view plan details, images and map location only. Requires active Basic subscription.'],
          ] as[$role,$color,$desc]): ?>
          <div style="background:var(--surface2);border:1px solid var(--border);border-radius:var(--r);padding:12px">
            <div style="font-size:.82rem;font-weight:700;color:<?= $color ?>;margin-bottom:5px"><?= $role ?></div>
            <div style="font-size:.78rem;color:var(--t2);line-height:1.6"><?= $desc ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <?php endif; /* end page switch */ ?>

    </main>
  </div>
</div>
<?php endif; /* end app/login */ ?>

<!-- ══ GLOBAL JS ══ -->
<script>
// Sidebar
function toggleSidebar(){
  var s=document.getElementById('sidebar');
  var o=document.getElementById('sidebarOverlay');
  if(s){s.classList.toggle('open');}
  if(o){o.classList.toggle('show');}
}
function closeSidebar(){
  var s=document.getElementById('sidebar');var o=document.getElementById('sidebarOverlay');
  if(s)s.classList.remove('open');if(o)o.classList.remove('show');
}

// File preview
var fileInput=document.getElementById('fileInput');
var uploadPreview=document.getElementById('uploadPreview');
var previewName=document.getElementById('previewName');
var previewIcon=document.getElementById('previewIcon');
var filePreviewBox=document.getElementById('filePreviewBox');
var filePreviewImg=document.getElementById('filePreviewImg');
var filePreviewPdf=document.getElementById('filePreviewPdf');
var filePreviewPdfName=document.getElementById('filePreviewPdfName');

function showFilePreview(file){
  if(!file||!filePreviewBox) return;
  previewName.textContent=file.name;
  previewIcon.textContent=file.type==='application/pdf'?'[PDF]':'[IMG]';
  uploadPreview.classList.add('show');
  filePreviewBox.style.display='block';
  if(file.type.startsWith('image/')){
    if(filePreviewImg) filePreviewImg.style.display='block';
    if(filePreviewPdf) filePreviewPdf.style.display='none';
    var rd=new FileReader();
    rd.onload=function(e){if(filePreviewImg) filePreviewImg.src=e.target.result;};
    rd.readAsDataURL(file);
  } else {
    if(filePreviewImg) filePreviewImg.style.display='none';
    if(filePreviewPdf) filePreviewPdf.style.display='block';
    if(filePreviewPdfName) filePreviewPdfName.textContent=file.name;
  }
}
function clearUpload(){
  if(fileInput) fileInput.value='';
  if(uploadPreview) uploadPreview.classList.remove('show');
  if(filePreviewBox) filePreviewBox.style.display='none';
}
if(fileInput){
  fileInput.addEventListener('change',function(){if(this.files[0])showFilePreview(this.files[0]);});
}
var uploadZone=document.getElementById('uploadZone');
if(uploadZone){
  uploadZone.addEventListener('dragover',function(e){e.preventDefault();this.classList.add('drag-over');});
  uploadZone.addEventListener('dragleave',function(){this.classList.remove('drag-over');});
  uploadZone.addEventListener('drop',function(e){
    e.preventDefault();this.classList.remove('drag-over');
    var f=e.dataTransfer.files[0];
    if(f&&fileInput){
      try{var dt=new DataTransfer();dt.items.add(f);fileInput.files=dt.files;}catch(err){}
      showFilePreview(f);
    }
  });
}

// Chain docs preview
var chainInput=document.getElementById('chainInput');
var chainList=document.getElementById('chainPreviewList');
var chainZone=document.getElementById('chainZone');
function fmtB(b){return b<1024?b+' B':b<1048576?(b/1024).toFixed(1)+' KB':(b/1048576).toFixed(1)+' MB';}
function renderChainPreview(files){
  if(!chainList) return;
  chainList.innerHTML='';
  chainList.style.display=files.length?'flex':'none';
  for(var i=0;i<files.length;i++){
    (function(f,idx){
      var row=document.createElement('div');
      row.style.cssText='display:flex;align-items:center;gap:8px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--r);padding:7px 11px';
      var icon=document.createElement('div');
      icon.style.cssText='width:32px;height:32px;flex-shrink:0;border-radius:4px;background:var(--bg);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:.85rem;overflow:hidden';
      if(f.type.startsWith('image/')){
        var img=document.createElement('img');img.style.cssText='width:100%;height:100%;object-fit:cover';
        var rd=new FileReader();rd.onload=function(e){img.src=e.target.result;};rd.readAsDataURL(f);
        icon.appendChild(img);
      } else {icon.textContent='[PDF]';}
      var info=document.createElement('div');info.style.flex='1';
      info.innerHTML='<div style="font-size:.78rem;color:var(--t1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">'+f.name+'</div><div style="font-size:.67rem;color:var(--t3)">'+fmtB(f.size)+' #'+(idx+1)+'</div>';
      row.appendChild(icon);row.appendChild(info);chainList.appendChild(row);
    })(files[i],i);
  }
}
if(chainInput) chainInput.addEventListener('change',function(){renderChainPreview(this.files);});
if(chainZone){
  chainZone.addEventListener('dragover',function(e){e.preventDefault();this.classList.add('drag-over');});
  chainZone.addEventListener('dragleave',function(){this.classList.remove('drag-over');});
  chainZone.addEventListener('drop',function(e){
    e.preventDefault();this.classList.remove('drag-over');
    if(chainInput){
      try{var dt=new DataTransfer();for(var i=0;i<e.dataTransfer.files.length;i++)dt.items.add(e.dataTransfer.files[i]);chainInput.files=dt.files;}catch(err){}
      renderChainPreview(chainInput.files);
    }
  });
}

// Location preview
function previewLoc(){
  var v=document.getElementById('locInput');var d=document.getElementById('locPreview');
  if(d&&v) d.style.display=v.value.trim()?'':'none';
}
function pasteLoc(){
  if(navigator.clipboard&&navigator.clipboard.readText){
    navigator.clipboard.readText().then(function(t){
      var i=document.getElementById('locInput');if(i){i.value=t;previewLoc();}
    }).catch(function(){alert('Please paste manually (Ctrl+V)');});
  } else {alert('Please paste manually (Ctrl+V)');}
}

// Image zoom
var zoomScale=1;
function openZoom(src){
  var ov=document.getElementById('imgZoomOverlay');var img=document.getElementById('zoomImg');
  if(!ov||!img) return;
  img.src=src;zoomScale=1;img.style.transform='scale(1)';
  ov.style.display='flex';document.body.style.overflow='hidden';
}
function closeZoom(e){if(e.target===document.getElementById('imgZoomOverlay'))closeZoomBtn();}
function closeZoomBtn(){
  var ov=document.getElementById('imgZoomOverlay');
  if(ov)ov.style.display='none';
  document.body.style.overflow='';
}
function zoomIn(){zoomScale=Math.min(zoomScale+0.3,4);var img=document.getElementById('zoomImg');if(img)img.style.transform='scale('+zoomScale+')';}
function zoomOut(){zoomScale=Math.max(zoomScale-0.3,0.5);var img=document.getElementById('zoomImg');if(img)img.style.transform='scale('+zoomScale+')';}
function zoomReset(){zoomScale=1;var img=document.getElementById('zoomImg');if(img)img.style.transform='scale(1)';}
document.addEventListener('keydown',function(e){if(e.key==='Escape')closeZoomBtn();});

// DLC Modal
function openDlcModal(){
  document.getElementById('dlcModalTitle').textContent='Add DLC Rate';
  document.getElementById('dlcFormId').value='0';
  document.getElementById('dlcVillage').value='';
  document.getElementById('dlcFY').value='<?= e(currentFY()) ?>';
  document.getElementById('dlcEf').value='';
  <?php foreach (['road_30ft','road_40ft','road_60ft','road_80ft','road_100ft','near_highway'] as $fn): ?>
  var el<?= $fn ?>=document.getElementById('dlc_<?= $fn ?>');if(el<?= $fn ?>)el<?= $fn ?>.value='';
  <?php endforeach; ?>
  document.getElementById('dlcNotes').value='';
  document.getElementById('dlcSubmitBtn').textContent='Save DLC Rate';
  document.getElementById('dlcModal').classList.add('show');
}
function openDlcModalEdit(data){
  document.getElementById('dlcModalTitle').textContent='Edit DLC Rate';
  document.getElementById('dlcFormId').value=data.id;
  document.getElementById('dlcVillage').value=data.village_id;
  document.getElementById('dlcFY').value=data.financial_year;
  document.getElementById('dlcEf').value=data.effective_from;
  <?php foreach (['road_30ft','road_40ft','road_60ft','road_80ft','road_100ft','near_highway'] as $fn): ?>
  var f<?= $fn ?>=document.getElementById('dlc_<?= $fn ?>');if(f<?= $fn ?>)f<?= $fn ?>.value=data.<?= $fn ?>||'';
  <?php endforeach; ?>
  document.getElementById('dlcNotes').value=data.notes||'';
  document.getElementById('dlcSubmitBtn').textContent='Update DLC Rate';
  document.getElementById('dlcModal').classList.add('show');
}
function closeDlcModal(){document.getElementById('dlcModal').classList.remove('show');}
document.addEventListener('keydown',function(e){if(e.key==='Escape')closeDlcModal();});
document.getElementById('dlcModal').addEventListener('click',function(e){if(e.target===this)closeDlcModal();});
</script>
</body>
</html>