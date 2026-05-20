<?php // includes/layout.php — PMS v4.0 ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
<title><?= e(APP_BRAND) ?><?= !in_array($page,['home','login'])?' — '.ucfirst(str_replace('_',' ',$page)):'' ?></title>
<style>
<?= get_theme_css() ?>

*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
html{scroll-behavior:smooth;}
body{background:var(--bg);color:var(--t2);font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.6;min-height:100vh;}
a{color:var(--primary-d);text-decoration:none;}a:hover{text-decoration:underline;}

/* Marquee */
.mqbar{background:var(--primary);color:var(--btn-text);padding:5px 0;overflow:hidden;white-space:nowrap;font-size:.8rem;font-weight:600;}
.mqinner{display:inline-block;animation:mqScroll linear infinite;}
@keyframes mqScroll{0%{transform:translateX(100vw);}100%{transform:translateX(-100%);}}

/* Buttons */
.btn{display:inline-flex;align-items:center;justify-content:center;gap:5px;border:1px solid transparent;border-radius:var(--r);font-family:Arial,Helvetica,sans-serif;font-weight:600;cursor:pointer;transition:all .15s;white-space:nowrap;text-decoration:none;line-height:1.3;}
.btn-sm{font-size:.76rem;padding:5px 11px;}.btn-md{font-size:.84rem;padding:8px 16px;}.btn-lg{font-size:.9rem;padding:11px 22px;}.btn-full{width:100%;justify-content:center;}
.btn-primary{background:var(--primary);color:var(--btn-text);border-color:var(--primary);}
.btn-primary:hover{background:var(--primary-h);border-color:var(--primary-h);color:var(--btn-text);text-decoration:none;}
.btn-secondary{background:var(--surface2);color:var(--t2);border-color:var(--border);}
.btn-secondary:hover{background:var(--border);color:var(--t1);text-decoration:none;}
.btn-danger{background:var(--red-bg);color:var(--red);border-color:#e0a0a0;}
.btn-danger:hover{background:var(--red);color:#fff;text-decoration:none;}
.btn-ghost{background:transparent;color:var(--t3);border-color:var(--border);}
.btn-ghost:hover{background:var(--surface2);color:var(--t1);text-decoration:none;}
.btn-success{background:var(--green-bg);color:var(--green);border-color:#9acc9a;}
.btn-success:hover{background:var(--green);color:#fff;text-decoration:none;}
.btn-gold{background:var(--gold-bg);color:var(--gold-s);border-color:#d4b090;}
.btn-gold:hover{background:var(--gold);color:#fff;text-decoration:none;}

/* Inputs */
.input,select.input,textarea.input{background:var(--surface);border:1px solid var(--border);border-radius:var(--r);padding:9px 12px;color:var(--t1);font-family:Arial,Helvetica,sans-serif;font-size:.86rem;outline:none;transition:border-color .15s,box-shadow .15s;width:100%;}
.input::placeholder{color:var(--t4);}.input:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(129,166,198,.18);}
textarea.input{resize:vertical;min-height:70px;}select.input{cursor:pointer;}

/* Alerts */
.alert{border-radius:var(--r);padding:10px 14px;font-size:.81rem;display:flex;align-items:flex-start;gap:8px;border:1px solid;margin-bottom:1rem;}
.alert-danger{background:var(--red-bg);border-color:#e0a0a0;color:var(--red);}
.alert-success{background:var(--green-bg);border-color:#9acc9a;color:var(--green);}
.alert-info{background:var(--primary-bg);border-color:#a0bcd4;color:var(--primary-d);}
.alert-warning{background:var(--gold-bg);border-color:#d4b090;color:var(--gold-s);}

/* Badges */
.badge{display:inline-block;border-radius:4px;padding:2px 8px;font-size:.65rem;font-weight:700;letter-spacing:.04em;text-transform:uppercase;}
.badge-blue{background:var(--primary-bg);color:var(--primary-d);border:1px solid #a0bcd4;}
.badge-gold{background:var(--gold-bg);color:var(--gold-s);border:1px solid #d4b090;}
.badge-green{background:var(--green-bg);color:var(--green);border:1px solid #9acc9a;}
.badge-red{background:var(--red-bg);color:var(--red);border:1px solid #e0a0a0;}
.badge-gray{background:var(--surface2);color:var(--t3);border:1px solid var(--border);}
.badge-dev{background:#e8f0fe;color:#4a5fca;border:1px solid #c0c8f0;}

/* Cards */
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden;box-shadow:var(--sh);}
.card-header{padding:.9rem 1.2rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:1rem;background:var(--surface2);}
.card-header h3{font-size:.9rem;font-weight:700;color:var(--t1);}
.card-body{padding:1.2rem;}

/* Forms */
.form-field{display:flex;flex-direction:column;gap:5px;margin-bottom:.85rem;}
.form-field label{font-size:.7rem;font-weight:700;color:var(--t3);letter-spacing:.06em;text-transform:uppercase;}
.form-field label .req{color:var(--primary);margin-left:2px;}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:.9rem;}
.fg-full{grid-column:1/-1;}
.divider{height:1px;background:var(--border);margin:1.1rem 0;}

/* Table */
.table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch;}
table{width:100%;border-collapse:collapse;}
thead th{padding:9px 11px;text-align:left;font-size:.68rem;font-weight:700;color:var(--t3);text-transform:uppercase;letter-spacing:.06em;border-bottom:2px solid var(--border);background:var(--surface2);white-space:nowrap;}
tbody td{padding:9px 11px;font-size:.82rem;color:var(--t2);border-bottom:1px solid var(--surface2);vertical-align:middle;}
tbody tr:hover td{background:var(--bg2);}
tbody tr:last-child td{border-bottom:none;}

/* Stats */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:12px;margin-bottom:1.5rem;}
.stat-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--r-lg);padding:1rem 1.1rem;display:flex;align-items:center;gap:11px;box-shadow:var(--sh);}
.stat-icon{width:38px;height:38px;flex-shrink:0;border-radius:var(--r);display:flex;align-items:center;justify-content:center;font-size:1.1rem;}
.si-blue{background:var(--primary-bg);}.si-gold{background:var(--gold-bg);}.si-green{background:var(--green-bg);}.si-gray{background:var(--surface2);}
.stat-val{font-size:1.35rem;font-weight:700;color:var(--t1);line-height:1;}.stat-lbl{font-size:.65rem;color:var(--t3);text-transform:uppercase;letter-spacing:.05em;margin-top:2px;}

/* Plan cards */
.plans-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(270px,1fr));gap:14px;}
.plan-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden;transition:all .15s;display:flex;flex-direction:column;box-shadow:var(--sh);}
.plan-card:hover{border-color:var(--primary);transform:translateY(-2px);box-shadow:var(--sh-md);}
.plan-card.dev-card{border-color:#c0c8f0;}
.plan-card.dev-card:hover{border-color:#4a5fca;}
.plan-card.sponsored{border:2px solid var(--gold);box-shadow:0 4px 16px rgba(200,149,108,.25);}
.plan-thumb{height:125px;background:var(--surface2);display:flex;align-items:center;justify-content:center;font-size:1.8rem;border-bottom:1px solid var(--border);position:relative;overflow:hidden;}
.plan-thumb img{width:100%;height:100%;object-fit:cover;}
.thumb-tag{position:absolute;bottom:6px;left:6px;background:rgba(44,58,74,.78);border-radius:3px;padding:2px 6px;font-size:.6rem;font-weight:700;text-transform:uppercase;color:#fff;}
.thumb-sponsored{position:absolute;top:6px;right:6px;background:var(--gold);color:#fff;border-radius:100px;padding:2px 8px;font-size:.62rem;font-weight:700;}
.plan-card-body{padding:11px 13px;flex:1;display:flex;flex-direction:column;}
.plan-name{font-size:.88rem;font-weight:700;color:var(--t1);margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.plan-aaraji{font-size:.72rem;color:var(--primary-d);margin-bottom:6px;font-weight:600;}
.plan-village{display:inline-flex;align-items:center;gap:4px;background:var(--gold-bg);border:1px solid #d4b090;border-radius:4px;padding:2px 7px;font-size:.67rem;font-weight:600;color:var(--gold-s);margin-bottom:6px;}
.plan-dev-badge{display:inline-flex;align-items:center;gap:4px;background:#e8f0fe;border:1px solid #c0c8f0;border-radius:4px;padding:2px 7px;font-size:.67rem;font-weight:600;color:#4a5fca;margin-bottom:6px;}
.plan-loc{font-size:.73rem;color:var(--t3);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:auto;}
.plan-card-footer{padding:8px 13px;border-top:1px solid var(--border);display:flex;gap:5px;flex-wrap:wrap;}

/* Filter chips */
.filter-row{display:flex;gap:7px;flex-wrap:wrap;align-items:center;margin-bottom:1.1rem;}
.chip{background:var(--surface);border:1px solid var(--border);border-radius:100px;padding:4px 12px;font-size:.74rem;font-weight:500;color:var(--t3);cursor:pointer;transition:all .15s;text-decoration:none;display:inline-block;}
.chip:hover{color:var(--t1);text-decoration:none;}.chip.active{background:var(--primary-bg);border-color:#a0bcd4;color:var(--primary-d);}

/* Pagination */
.pagination{display:flex;align-items:center;gap:5px;margin-top:1.5rem;flex-wrap:wrap;}
.pag-btn{background:var(--surface);border:1px solid var(--border);color:var(--t3);border-radius:var(--r-sm);padding:5px 11px;font-size:.77rem;font-weight:500;text-decoration:none;transition:all .15s;display:inline-block;}
.pag-btn:hover{background:var(--primary-bg);border-color:var(--primary);color:var(--primary);text-decoration:none;}
.pag-btn.active{background:var(--primary);border-color:var(--primary);color:var(--btn-text);}
.pag-btn.disabled{opacity:.4;pointer-events:none;}

/* Upload */
.upload-zone{border:2px dashed var(--border2);border-radius:var(--r-lg);padding:1.4rem;text-align:center;cursor:pointer;background:var(--surface2);position:relative;transition:all .15s;}
.upload-zone:hover,.upload-zone.drag-over{border-color:var(--primary);background:var(--primary-bg);}
.upload-zone input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;}
.upload-zone p{font-size:.82rem;color:var(--t3);}
.upload-zone small{font-size:.7rem;color:var(--t4);}
.upload-preview{margin-top:8px;display:none;align-items:center;gap:8px;background:var(--primary-bg);border:1px solid #a0bcd4;border-radius:var(--r);padding:7px 11px;font-size:.79rem;color:var(--primary-d);}
.upload-preview.show{display:flex;}

/* Sidebar */
.sidebar{width:var(--sidebar);background:var(--sidebar-bg);border-right:1px solid var(--border);display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;z-index:200;transition:transform .25s ease;overflow-y:auto;box-shadow:var(--sh);}
.sidebar-brand{padding:1rem;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px;min-height:var(--hdr);}
.brand-icon{width:34px;height:34px;flex-shrink:0;background:var(--primary);border-radius:var(--r);display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:var(--btn-text);font-weight:700;}
.brand-text h2{font-size:.88rem;font-weight:700;color:var(--t1);line-height:1.25;}
.brand-text span{font-size:.6rem;color:var(--t3);text-transform:uppercase;letter-spacing:.06em;}
.sidebar-nav{flex:1;padding:.7rem .5rem;}
.nav-section{font-size:.6rem;font-weight:700;color:var(--t4);letter-spacing:.1em;text-transform:uppercase;padding:0 .6rem;margin:.7rem 0 .3rem;}
.nav-item{display:flex;align-items:center;gap:8px;padding:7px 10px;border-radius:var(--r);color:var(--t3);font-size:.82rem;font-weight:500;text-decoration:none;cursor:pointer;border:none;background:none;width:100%;transition:all .15s;margin-bottom:1px;}
.nav-item .ni{font-size:1.1rem;width:20px;flex-shrink:0;text-align:center;display:flex;align-items:center;justify-content:center;}
.nav-item:hover{background:var(--primary-bg);color:var(--primary-d);text-decoration:none;}
.nav-item.active{background:var(--primary-bg);color:var(--primary-d);border:1px solid #a0bcd4;}
.sidebar-footer{padding:.7rem;border-top:1px solid var(--border);}
.user-info{display:flex;align-items:center;gap:8px;padding:7px 9px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--r);margin-bottom:7px;}
.user-avatar{width:26px;height:26px;flex-shrink:0;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.7rem;color:var(--btn-text);font-weight:700;}
.uname{font-size:.78rem;font-weight:700;color:var(--t1);}.urole{font-size:.62rem;color:var(--t3);text-transform:uppercase;letter-spacing:.04em;}
.sidebar-plan-pill{padding:6px 9px;border-radius:var(--r);border:1px solid var(--border);background:var(--surface2);display:flex;align-items:center;gap:7px;margin-bottom:7px;}
.spp-label{font-size:.72rem;font-weight:700;color:var(--primary-d);}.spp-exp{font-size:.62rem;color:var(--t4);}
.sidebar-footer-text{text-align:center;font-size:.65rem;color:var(--t4);padding:5px 0;line-height:1.5;}

/* App shell */
.app-shell{display:flex;min-height:100vh;}
.main-wrap{flex:1;margin-left:var(--sidebar);min-height:100vh;display:flex;flex-direction:column;}
.topbar{height:var(--hdr);background:var(--topbar-bg);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;padding:0 1.4rem;position:sticky;top:0;z-index:100;box-shadow:var(--sh);}
.topbar-left{display:flex;align-items:center;gap:9px;}
.topbar-title{font-size:.9rem;font-weight:700;color:var(--t1);}
.topbar-right{display:flex;align-items:center;gap:7px;}
.topbar-date{font-size:.7rem;color:var(--t3);background:var(--surface2);border:1px solid var(--border);border-radius:100px;padding:3px 10px;}
.page-content{padding:1.4rem;flex:1;}
.sidebar-toggle{display:none;background:var(--surface2);border:1px solid var(--border);color:var(--t2);border-radius:var(--r);padding:5px 9px;cursor:pointer;font-size:1rem;line-height:1;font-weight:700;}
.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(44,58,74,.4);z-index:199;}
.sidebar-overlay.show{display:block;}

/* Toast */
.toast-wrap{position:fixed;bottom:1.2rem;right:1.2rem;z-index:9999;}
.toast{background:var(--surface);border:1px solid var(--border);border-radius:var(--r-lg);padding:10px 15px;display:flex;align-items:center;gap:9px;font-size:.82rem;font-weight:600;color:var(--t1);box-shadow:var(--sh-lg);min-width:230px;}
.toast.success{border-left:3px solid var(--green);}.toast.error{border-left:3px solid var(--red);}.toast.info{border-left:3px solid var(--primary);}

/* Map */
.map-embed{border-radius:var(--r-lg);overflow:hidden;border:1px solid var(--border);}
.map-embed iframe{width:100%;height:230px;border:none;display:block;}

/* Locked */
.locked-section{padding:1.1rem;display:flex;align-items:center;gap:11px;background:var(--surface2);}

/* DLC table */
.dlc-sqm{font-weight:600;color:var(--t1);font-size:.8rem;}.dlc-sqft{font-size:.67rem;color:var(--t3);}

/* Search */
.search-bar{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:.9rem;}
.search-input-wrap{position:relative;flex:1;min-width:190px;}
.search-icon-pos{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--t4);pointer-events:none;font-size:.9rem;}
.search-input-wrap .input{padding-left:32px;}

/* Tabs */
.tab-bar{display:flex;gap:0;border-bottom:2px solid var(--border);margin-bottom:1.1rem;}
.tab-btn{padding:7px 18px;font-size:.82rem;font-weight:600;color:var(--t3);background:none;border:none;cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px;transition:all .15s;text-decoration:none;display:inline-block;}
.tab-btn:hover{color:var(--primary-d);text-decoration:none;}
.tab-btn.active{color:var(--primary-d);border-bottom-color:var(--primary);}

/* Image zoom overlay */
#imgZoom{position:fixed;inset:0;background:rgba(0,0,0,.9);z-index:9999;display:none;align-items:center;justify-content:center;}
#imgZoom.show{display:flex;}
#imgZoom img{max-width:90vw;max-height:88vh;border-radius:var(--r-lg);cursor:default;}
.zoom-close-btn{position:absolute;top:1rem;right:1rem;background:rgba(255,255,255,.2);border:none;color:#fff;font-size:1.3rem;width:36px;height:36px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;}
.zoom-ctrls{position:absolute;bottom:1.5rem;left:50%;transform:translateX(-50%);display:flex;gap:9px;}
.zoom-ctrls button{background:rgba(255,255,255,.2);border:none;color:#fff;font-size:.9rem;width:36px;height:36px;border-radius:50%;cursor:pointer;font-weight:700;}
.zoom-ctrls button:hover{background:rgba(255,255,255,.35);}
.zoomable{cursor:zoom-in;}

/* Modal */
.modal-overlay{position:fixed;inset:0;background:rgba(44,58,74,.45);z-index:500;display:none;align-items:center;justify-content:center;padding:1rem;}
.modal-overlay.show{display:flex;}
.modal-box{background:var(--surface);border:1px solid var(--border);border-radius:var(--r-xl);box-shadow:var(--sh-lg);width:100%;max-width:580px;max-height:90vh;overflow-y:auto;position:relative;animation:mIn .22s ease;}
@keyframes mIn{from{opacity:0;transform:scale(.94) translateY(12px);}to{opacity:1;transform:scale(1) translateY(0);}}
.modal-hdr{padding:.9rem 1.2rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:var(--surface2);}
.modal-hdr h3{font-size:.9rem;font-weight:700;color:var(--t1);}
.modal-close{background:none;border:none;font-size:1.1rem;cursor:pointer;color:var(--t3);padding:2px 7px;border-radius:4px;font-weight:700;}
.modal-close:hover{background:var(--red-bg);color:var(--red);}
.modal-body{padding:1.2rem;}

/* Two col */
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:1.2rem;}

/* Section heading */
.section-heading{font-size:.75rem;font-weight:700;color:var(--primary-d);text-transform:uppercase;letter-spacing:.06em;padding-bottom:.4rem;border-bottom:1px solid var(--border);margin:.9rem 0 .7rem;}

/* Color swatch input */
.color-row{display:flex;align-items:center;gap:10px;margin-bottom:.7rem;}
.color-row label{min-width:170px;font-size:.78rem;color:var(--t2);}
.color-row input[type=color]{width:42px;height:32px;border:1px solid var(--border);border-radius:4px;cursor:pointer;padding:2px;}
.color-row input[type=text]{flex:1;font-family:'JetBrains Mono',monospace,sans-serif;font-size:.82rem;}

/* Permission table */
.perm-table{width:100%;border-collapse:collapse;}
.perm-table th{padding:8px 10px;background:var(--surface2);font-size:.68rem;font-weight:700;color:var(--t3);text-transform:uppercase;border-bottom:2px solid var(--border);text-align:center;}
.perm-table th:first-child{text-align:left;}
.perm-table td{padding:8px 10px;border-bottom:1px solid var(--surface2);font-size:.82rem;color:var(--t2);text-align:center;vertical-align:middle;}
.perm-table td:first-child{text-align:left;font-weight:500;color:var(--t1);}
.perm-table tr.perm-group td{background:var(--primary-bg);font-weight:700;color:var(--primary-d);font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;padding:5px 10px;}
.perm-table input[type=checkbox]{width:16px;height:16px;cursor:pointer;accent-color:var(--primary);}
.perm-locked{color:var(--t4);font-size:.72rem;}

/* Login */
<?php if (in_array($page,['login','reset_password'])): ?>
body{display:flex;align-items:center;justify-content:center;min-height:100vh;padding:1.5rem;background:linear-gradient(135deg,var(--bg) 0%,var(--sec) 100%);}
<?php endif; ?>
.login-wrap{width:100%;max-width:400px;animation:fadeUp .4s ease both;}
@keyframes fadeUp{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
.login-brand{text-align:center;margin-bottom:1.8rem;}
.login-brand-icon{width:60px;height:60px;background:var(--primary);border-radius:16px;display:inline-flex;align-items:center;justify-content:center;font-size:26px;margin-bottom:.7rem;box-shadow:0 6px 20px rgba(129,166,198,.45);color:var(--btn-text);font-weight:700;}
.login-brand h1{font-size:1.5rem;font-weight:700;color:var(--t1);}
.login-brand .sub{font-size:.72rem;color:var(--t3);text-transform:uppercase;letter-spacing:.08em;margin-top:3px;}
.login-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--r-xl);padding:1.8rem;box-shadow:var(--sh-lg);}
.login-card h2{font-size:1rem;font-weight:700;color:var(--t1);margin-bottom:3px;}
.login-card .lsub{font-size:.78rem;color:var(--t3);margin-bottom:1.4rem;}

/* Sponsored strip */
.sponsored-strip{background:linear-gradient(135deg,var(--gold-bg),#fff);border:1px solid #d4b090;border-radius:var(--r-lg);padding:1rem 1.2rem;margin-bottom:1.4rem;}
.sponsored-strip-title{display:flex;align-items:center;gap:10px;margin-bottom:.8rem;}
.sponsored-strip-title .label{background:var(--gold);color:#fff;padding:2px 10px;border-radius:100px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;}
.sponsored-strip-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:10px;}
.sp-card{background:#fff;border:1px solid #d4b090;border-radius:var(--r);overflow:hidden;transition:all .15s;}
.sp-card:hover{border-color:var(--gold);transform:translateY(-1px);box-shadow:0 4px 14px rgba(200,149,108,.2);}
.sp-thumb{height:90px;overflow:hidden;background:var(--surface2);display:flex;align-items:center;justify-content:center;font-size:1.5rem;}
.sp-thumb img{width:100%;height:100%;object-fit:cover;}
.sp-body{padding:8px 10px;}
.sp-name{font-size:.82rem;font-weight:700;color:var(--t1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.sp-price{font-size:.72rem;color:var(--gold-s);font-weight:600;margin-top:2px;}

/* Responsive */
@media(max-width:768px){
  :root{--sidebar:265px;}
  .sidebar{transform:translateX(-265px);}.sidebar.open{transform:translateX(0);}
  .main-wrap{margin-left:0;}.sidebar-toggle{display:flex;align-items:center;}
  .page-content{padding:1rem;}.plans-grid{grid-template-columns:1fr;}.form-grid{grid-template-columns:1fr;}.fg-full{grid-column:1;}.stats-grid{grid-template-columns:1fr 1fr;}.two-col{grid-template-columns:1fr;}.topbar{padding:0 .9rem;}.topbar-date{display:none;}
  table{font-size:.76rem;}thead th{padding:7px 8px;}tbody td{padding:7px 8px;}
  .plan-card-footer .btn{font-size:.7rem;padding:5px 7px;}
  .color-row label{min-width:130px;font-size:.74rem;}
}
@media(max-width:480px){.stats-grid{grid-template-columns:1fr 1fr;}.chip{font-size:.68rem;padding:3px 9px;}.login-card{padding:1.3rem;}}

/* Boxicons sizing in stat cards and buttons */
.stat-icon i{font-size:1.3rem;color:var(--primary-d);}
.stat-icon.si-gold i{color:var(--gold-s);}
.stat-icon.si-green i{color:var(--green);}
.stat-icon.si-gray i{color:var(--t3);}
.btn i{font-size:1rem;vertical-align:middle;line-height:1;}
.nav-item i{font-size:1rem;}
.card-header h3 i{font-size:1rem;vertical-align:middle;margin-right:2px;}
.upload-zone i{display:block;}
</style>
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
    <p style="text-align:center;margin-top:.9rem;font-size:.72rem;color:var(--t4)">Default: <code>admin</code> / <code>admin@123</code></p>
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
      <?php foreach([['[P]','si-blue',$stats['admin_plans']??0,'Admin Plans'],['[D]','si-gold',$stats['dev_plans']??0,'Dev Plans'],['[L]','si-green',$stats['located']??0,'Located'],['[V]','si-gray',$stats['villages']??0,'Villages']] as[$ic,$sc,$sv,$sl]): ?>
      <div class="stat-card"><div class="stat-icon <?= $sc ?>"><?= $ic ?></div><div><div class="stat-val"><?= (int)$sv ?></div><div class="stat-lbl"><?= $sl ?></div></div></div>
      <?php endforeach; ?>
    </div>

    <!-- Tabs -->
    <div class="tab-bar">
      <a class="tab-btn <?= $tab==='admin'?'active':'' ?>" href="index.php?page=home&tab=admin<?= $q?'&q='.urlencode($q):'' ?><?= $vf?'&village='.$vf:'' ?>">Admin Plans</a>
      <a class="tab-btn <?= $tab==='developer'?'active':'' ?>" href="index.php?page=home&tab=developer<?= $q?'&q='.urlencode($q):'' ?><?= $vf?'&village='.$vf:'' ?>">Developer Plans</a>
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
          <?php if ($plan['contact_number']): ?><div style="font-size:.71rem;color:var(--t3)">Tel: <?= e($plan['contact_number']) ?></div><?php endif; ?>
          <?php else: ?>
          <div class="plan-loc"><?= $plan['google_location']?'Loc: '.e(substr($plan['google_location'],0,40)).(strlen($plan['google_location'])>40?'...':''):'<span style="color:var(--t4)">No location</span>' ?></div>
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
              <div class="upload-zone" style="padding:1rem"><input type="file" name="approved_map" accept="image/*,.pdf"><div style="font-size:2.2rem;margin-bottom:.4rem;color:var(--green)"><i class="bx bx-map-alt"></i></div><p style="font-size:.78rem">Upload government approved map</p><small>Max <?= MAX_FILE_MB ?>MB</small></div>
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

    <?php /* ============================= VIEW ============================= */ ?>
    <?php elseif ($page==='view'):
    $isBasic=can_view_basic(); $isAdvance=can_view_advance();
    $isDev=$plan['is_developer_plan']; $ownDev=($isDev&&$plan['created_by']==current_user()['id']); ?>

    <?php if (!$isBasic): ?>
    <div style="max-width:500px;margin:3rem auto;text-align:center"><div class="card"><div class="card-body" style="padding:2.5rem 1.5rem"><div style="font-size:2.5rem;margin-bottom:.8rem"><i class="bx bx-lock-alt"></i></div><h2 style="margin-bottom:.5rem">Subscription Required</h2><p style="font-size:.84rem;color:var(--t3);margin-bottom:1.2rem">You need an active subscription to view plan details.</p><a href="index.php" class="btn btn-ghost btn-md" style="margin-right:8px">Back</a><a href="index.php?page=profile" class="btn btn-primary btn-md">Request Access</a></div></div></div>
    <?php else: ?>

    <div style="display:flex;gap:8px;margin-bottom:1.1rem;align-items:center;flex-wrap:wrap">
      <a href="index.php" class="btn btn-ghost btn-sm">Back</a>
      <?php if ($isDev): ?><span class="badge-dev badge" style="padding:4px 10px">[DEV] Developer Plan</span><?php endif; ?>
      <?php if ($plan['is_sponsored']): ?><span style="background:var(--gold);color:#fff;border-radius:100px;padding:3px 10px;font-size:.68rem;font-weight:700"><?= e($plan['sponsored_label']) ?></span><?php endif; ?>
      <?php if (is_admin()||$ownDev): ?><a href="index.php?page=edit&id=<?= $plan['id'] ?>" class="btn btn-secondary btn-sm"><i class="bx bx-edit-alt"></i> Edit</a><?php endif; ?>
      <?php if ($plan['google_location']): ?><a href="<?= e($plan['google_location']) ?>" target="_blank" rel="noopener" class="btn btn-ghost btn-sm">Open in Maps</a><?php endif; ?>
      <?php if ($plan['file_path']&&$isAdvance): ?><a href="<?= e($plan['file_path']) ?>" download class="btn btn-secondary btn-sm">Download Plan</a><?php endif; ?>
      <?php if (is_admin()): ?>
      <form method="POST" style="margin-left:auto" onsubmit="return confirm('Delete this plan permanently?')"><input type="hidden" name="action" value="delete_plan"><input type="hidden" name="plan_id" value="<?= $plan['id'] ?>"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><button type="submit" class="btn btn-danger btn-sm">Delete</button></form>
      <?php endif; ?>
    </div>

    <div class="two-col">
      <div>
        <!-- Plan image -->
        <?php if ($plan['file_type']==='image'&&$plan['file_path']&&$isBasic): ?>
        <div class="card" style="margin-bottom:1.1rem;overflow:hidden">
          <img src="<?= e($plan['file_path']) ?>" alt="Plan" class="zoomable" onclick="openZoom(this.src)" style="width:100%;display:block;max-height:320px;object-fit:contain;background:var(--surface2)">
          <div style="padding:7px 12px;background:var(--surface2);border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
            <span style="font-size:.72rem;color:var(--t3)">Click to zoom</span>
            <?php if ($isAdvance): ?><a href="<?= e($plan['file_path']) ?>" download class="btn btn-secondary btn-sm">Download</a>
            <?php else: ?><span style="font-size:.72rem;color:var(--t3)">Download requires Advance plan</span><?php endif; ?>
          </div>
        </div>
        <?php elseif ($plan['file_path']): ?>
        <div class="card" style="margin-bottom:1.1rem"><div style="padding:2rem;text-align:center;background:var(--surface2)"><div style="font-size:2.5rem;margin-bottom:.7rem">[PDF]</div><p style="font-size:.83rem;color:var(--t2);margin-bottom:1rem"><?= e($plan['file_name']) ?></p><?php if ($isAdvance): ?><a href="<?= e($plan['file_path']) ?>" target="_blank" class="btn btn-secondary btn-sm">Open PDF</a><a href="<?= e($plan['file_path']) ?>" download class="btn btn-ghost btn-sm" style="margin-left:6px">Download</a><?php else: ?><div class="locked-section" style="justify-content:center"><strong>Advance Plan Required</strong></div><?php endif; ?></div></div>
        <?php endif; ?>
        <!-- Approved map (dev plans) -->
        <?php if ($isDev&&$plan['approved_map_path']): ?>
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
        <!-- Map -->
        <?php if ($plan['google_location']): $emb=embedUrl($plan['google_location']); ?>
        <div class="map-embed"><?php if ($emb): ?><iframe src="<?= e($emb) ?>" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe><?php else: ?><div style="height:150px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:5px;background:var(--surface2)"><div>[MAP]</div><a href="<?= e($plan['google_location']) ?>" target="_blank">Open in Google Maps</a></div><?php endif; ?></div>
        <?php endif; ?>
      </div>
      <div>
        <div class="card" style="margin-bottom:1.1rem">
          <div class="card-header"><h3><?= e($plan['plan_name']) ?></h3><span class="badge badge-blue"># <?= e(substr($plan['aaraji_number'],0,40)) ?></span></div>
          <div class="card-body">
            <table style="width:100%;border-collapse:collapse">
              <?php $rows=[['Aaraji No.',$plan['aaraji_number']],['Village',$plan['village_name']??'--'],['Tehsil',$plan['tehsil']??'--'],['District',$plan['district']??'--']];
              if ($isDev) $rows=array_merge($rows,[['Developer',$plan['dev_fullname']?:$plan['created_by_name']],['Contact',$plan['contact_number']??'--']]);
              if (is_admin()) $rows=array_merge($rows,[['File',$plan['file_name']??'--'],['Added',date('d M Y',strtotime($plan['created_at']))]]);
              foreach($rows as[$l,$v]): ?>
              <tr><td style="padding:5px 0;border-bottom:1px solid var(--surface2);font-size:.67rem;font-weight:700;color:var(--t3);text-transform:uppercase;width:38%"><?= e($l) ?></td><td style="padding:5px 0;border-bottom:1px solid var(--surface2);font-size:.81rem;color:var(--t1);text-align:right;word-break:break-word"><?= e($v) ?></td></tr>
              <?php endforeach; ?>
            </table>
            <?php if ($plan['notes']): ?><div style="margin-top:.9rem;font-size:.82rem;color:var(--t2);line-height:1.7"><?= nl2br(e($plan['notes'])) ?></div><?php endif; ?>
          </div>
        </div>

        <!-- Pricing (dev plans) -->
        <?php if ($isDev): $hasPricing=array_filter(['price_30ft','price_40ft','price_60ft','price_80ft','price_100ft','price_highway'],fn($f)=>$plan[$f]!==null); ?>
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
        <?php else: ?>

        <!-- DLC (admin plans, advance only) -->
        <?php if ($isAdvance&&$planDlc): ?>
        <div class="card" style="margin-bottom:1.1rem">
          <div class="card-header"><h3><i class="bx bx-bar-chart-alt-2"></i> DLC Rates</h3><div style="display:flex;gap:7px;align-items:center"><span class="badge badge-gold">FY <?= e($planDlc['financial_year']) ?></span><span style="font-size:.68rem;color:var(--t3)">Eff. <?= date('d M Y',strtotime($planDlc['effective_from'])) ?></span></div></div>
          <div class="card-body">
            <table style="width:100%;border-collapse:collapse">
              <tr><th style="padding:5px 0;font-size:.67rem;font-weight:700;color:var(--t3);text-transform:uppercase;border-bottom:1px solid var(--border);text-align:left">Road</th><th style="padding:5px 4px;font-size:.67rem;color:var(--t3);border-bottom:1px solid var(--border);text-align:right">Rs/sq.m</th><th style="padding:5px 0 5px 4px;font-size:.67rem;color:var(--t3);border-bottom:1px solid var(--border);text-align:right">Rs/sq.ft</th></tr>
              <?php foreach([['30 ft','road_30ft'],['40 ft','road_40ft'],['60 ft','road_60ft'],['80 ft','road_80ft'],['100 ft','road_100ft'],['Highway','near_highway']] as[$lbl,$fld]):
              if ($planDlc[$fld]===null) continue; ?>
              <tr><td style="padding:5px 0;border-bottom:1px solid var(--surface2);font-size:.79rem"><?= $lbl ?></td><td style="padding:5px 4px;border-bottom:1px solid var(--surface2);text-align:right;font-weight:600;font-size:.79rem"><?= fmtSqm((float)$planDlc[$fld]) ?></td><td style="padding:5px 0 5px 4px;border-bottom:1px solid var(--surface2);text-align:right;font-size:.72rem;color:var(--t3)"><?= fmtSqft((float)$planDlc[$fld]) ?></td></tr>
              <?php endforeach; ?>
            </table>
          </div>
        </div>
        <?php elseif (!$isAdvance): ?>
        <div class="card" style="margin-bottom:1.1rem"><div class="card-header"><h3><i class="bx bx-bar-chart-alt-2"></i> DLC Rates</h3></div><div class="locked-section"><i class="bx bx-lock-alt"></i> <strong>Advance Plan Required</strong></div></div>
        <?php endif; ?>

        <!-- Chain docs -->
        <?php if ($isAdvance): ?>
        <div class="card">
          <div class="card-header"><h3><i class="bx bx-link"></i> Chain Documents</h3><?php if (!empty($chainDocs)): ?><span class="badge badge-blue"><?= count($chainDocs) ?></span><?php endif; ?></div>
          <?php if (empty($chainDocs)): ?><div style="padding:1.2rem;text-align:center;color:var(--t3);font-size:.82rem">No chain documents.<?php if (is_admin()): ?> <a href="index.php?page=edit&id=<?= $plan['id'] ?>">Add</a><?php endif; ?></div>
          <?php else: ?><div class="card-body" style="display:flex;flex-direction:column;gap:8px">
            <?php foreach ($chainDocs as $idx=>$doc): ?>
            <div style="display:flex;align-items:center;gap:9px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--r);padding:9px 11px">
              <div style="width:38px;height:38px;flex-shrink:0;border-radius:var(--r);background:var(--bg);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:1rem;overflow:hidden"><?php if ($doc['file_type']==='image'): ?><img src="<?= e($doc['file_path']) ?>" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:4px"><?php else: ?>[PDF]<?php endif; ?></div>
              <div style="flex:1;min-width:0"><div style="font-size:.79rem;font-weight:600;color:var(--t1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= e($doc['file_name']) ?></div><div style="font-size:.67rem;color:var(--t3)"><?= strtoupper($doc['file_type']) ?><?= $doc['file_size']?' - '.round($doc['file_size']/1024).' KB':'' ?></div></div>
              <a href="<?= e($doc['file_path']) ?>" target="_blank" class="btn btn-ghost btn-sm">Open</a>
              <a href="<?= e($doc['file_path']) ?>" download class="btn btn-secondary btn-sm">Save</a>
              <?php if (is_admin()): ?><form method="POST" onsubmit="return confirm('Delete?')"><input type="hidden" name="action" value="delete_chain_doc"><input type="hidden" name="doc_id" value="<?= $doc['id'] ?>"><input type="hidden" name="plan_id" value="<?= $plan['id'] ?>"><input type="hidden" name="csrf_token" value="<?= e($csrfTok) ?>"><button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button></form><?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div><?php endif; ?>
        </div>
        <?php else: ?><div class="card"><div class="card-header"><h3><i class="bx bx-link"></i> Chain Documents</h3></div><div class="locked-section"><i class="bx bx-lock-alt"></i> <strong>Advance Plan Required</strong></div></div><?php endif; ?>
        <?php endif; /* end is_dev check */ ?>
      </div>
    </div>
    <?php endif; /* isBasic */ ?>

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
                  <td><?= e($p['label']) ?></td>
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

    <script>
    function syncColor(input){var v=input.value;if(/^#[0-9a-fA-F]{6}$/.test(v)){var pair=input.previousElementSibling;if(pair&&pair.type==='color')pair.value=v;}}
    function resetTheme(){var defaults={'theme_primary':'#81A6C6','theme_bg':'#F3E3D0','theme_surface':'#FFFFFF','theme_border':'#D2C4B4','theme_btn_text':'#FFFFFF','theme_heading':'#2C3A4A','theme_text':'#4A5E70','theme_sidebar_bg':'#FFFFFF','theme_topbar_bg':'#FFFFFF'};
    for(var k in defaults){var t=document.getElementById(k+'_text');if(t){t.value=defaults[k];var c=t.previousElementSibling;if(c&&c.type==='color')c.value=defaults[k];}}}
    </script>

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

<script>
// Sidebar
function toggleSidebar(){var s=document.getElementById('sidebar');var o=document.getElementById('sidebarOverlay');if(s)s.classList.toggle('open');if(o)o.classList.toggle('show');}
function closeSidebar(){document.getElementById('sidebar')?.classList.remove('open');document.getElementById('sidebarOverlay')?.classList.remove('show');}

// Forgot password toggle
function showForgot(){var f=document.getElementById('forgotForm');if(f)f.style.display=f.style.display==='none'?'block':'none';}

// File preview
var fileInput=document.getElementById('fileInput');
var uploadPreview=document.getElementById('uploadPreview');
var prevName=document.getElementById('prevName');
var prevIcon=document.getElementById('prevIcon');
var filePreviewBox=document.getElementById('filePreviewBox');
var filePreviewImg=document.getElementById('filePreviewImg');
var filePreviewPdf=document.getElementById('filePreviewPdf');
var filePreviewPdfName=document.getElementById('filePreviewPdfName');

function showFilePreview(file){
  if(!file||!filePreviewBox) return;
  if(prevName) prevName.textContent=file.name;
  if(prevIcon) prevIcon.textContent=file.type==='application/pdf'?'[PDF]':'[IMG]';
  if(uploadPreview) uploadPreview.classList.add('show');
  filePreviewBox.style.display='block';
  if(file.type.startsWith('image/')){
    if(filePreviewImg) filePreviewImg.style.display='block';
    if(filePreviewPdf) filePreviewPdf.style.display='none';
    var rd=new FileReader();rd.onload=function(e){if(filePreviewImg)filePreviewImg.src=e.target.result;};rd.readAsDataURL(file);
  } else {
    if(filePreviewImg) filePreviewImg.style.display='none';
    if(filePreviewPdf) filePreviewPdf.style.display='block';
    if(filePreviewPdfName) filePreviewPdfName.textContent=file.name;
  }
}
function clearUpload(){if(fileInput)fileInput.value='';if(uploadPreview)uploadPreview.classList.remove('show');if(filePreviewBox)filePreviewBox.style.display='none';}
if(fileInput) fileInput.addEventListener('change',function(){if(this.files[0])showFilePreview(this.files[0]);});
var uploadZone=document.getElementById('uploadZone');
if(uploadZone){
  uploadZone.addEventListener('dragover',function(e){e.preventDefault();this.classList.add('drag-over');});
  uploadZone.addEventListener('dragleave',function(){this.classList.remove('drag-over');});
  uploadZone.addEventListener('drop',function(e){e.preventDefault();this.classList.remove('drag-over');var f=e.dataTransfer.files[0];if(f&&fileInput){try{var dt=new DataTransfer();dt.items.add(f);fileInput.files=dt.files;}catch(err){}showFilePreview(f);}});
}

// Chain docs
var chainInput=document.getElementById('chainInput');var chainList=document.getElementById('chainPreviewList');var chainZone=document.getElementById('chainZone');
function fmtB(b){return b<1024?b+' B':b<1048576?(b/1024).toFixed(1)+' KB':(b/1048576).toFixed(1)+' MB';}
function renderChain(files){if(!chainList)return;chainList.innerHTML='';chainList.style.display=files.length?'flex':'none';for(var i=0;i<files.length;i++){(function(f,idx){var row=document.createElement('div');row.style.cssText='display:flex;align-items:center;gap:8px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--r);padding:7px 11px';var icon=document.createElement('div');icon.style.cssText='width:32px;height:32px;flex-shrink:0;border-radius:4px;background:var(--bg);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:.85rem;overflow:hidden';if(f.type.startsWith('image/')){var img=document.createElement('img');img.style.cssText='width:100%;height:100%;object-fit:cover';var rd=new FileReader();rd.onload=function(e){img.src=e.target.result;};rd.readAsDataURL(f);icon.appendChild(img);}else{icon.textContent='[PDF]';}var info=document.createElement('div');info.style.flex='1';info.innerHTML='<div style="font-size:.78rem;color:var(--t1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">'+f.name+'</div><div style="font-size:.67rem;color:var(--t3)">'+fmtB(f.size)+' #'+(idx+1)+'</div>';row.appendChild(icon);row.appendChild(info);chainList.appendChild(row);})(files[i],i);}}
if(chainInput) chainInput.addEventListener('change',function(){renderChain(this.files);});
if(chainZone){chainZone.addEventListener('dragover',function(e){e.preventDefault();this.classList.add('drag-over');});chainZone.addEventListener('dragleave',function(){this.classList.remove('drag-over');});chainZone.addEventListener('drop',function(e){e.preventDefault();this.classList.remove('drag-over');if(chainInput){try{var dt=new DataTransfer();for(var i=0;i<e.dataTransfer.files.length;i++)dt.items.add(e.dataTransfer.files[i]);chainInput.files=dt.files;}catch(err){}renderChain(chainInput.files);}});}

// Location preview
function previewLoc(){var v=document.getElementById('locInput');var d=document.getElementById('locPreview');if(d&&v)d.style.display=v.value.trim()?'':'none';}
var locPrev=document.getElementById('locPreview');if(locPrev){var li=document.getElementById('locInput');if(li&&li.value.trim())locPrev.style.display='';}

// Developer fields toggle (admin only)
function toggleDevFields(val){var show=val==='1';['contactField','approvedMapField','pricingSection'].forEach(function(id){var el=document.getElementById(id);if(el)el.style.display=show?'':'none';});}

// Image zoom
var zScale=1;
function openZoom(src){var ov=document.getElementById('imgZoom');var img=document.getElementById('zoomImg');if(!ov||!img)return;img.src=src;zScale=1;img.style.transform='scale(1)';ov.style.display='flex';document.body.style.overflow='hidden';}
function closeZoom(e){if(e.target===document.getElementById('imgZoom'))closeZoomBtn();}
function closeZoomBtn(){var ov=document.getElementById('imgZoom');if(ov)ov.style.display='none';document.body.style.overflow='';}
function zoomIn(){zScale=Math.min(zScale+0.3,4);var img=document.getElementById('zoomImg');if(img)img.style.transform='scale('+zScale+')';}
function zoomOut(){zScale=Math.max(zScale-0.3,0.5);var img=document.getElementById('zoomImg');if(img)img.style.transform='scale('+zScale+')';}
function zoomReset(){zScale=1;var img=document.getElementById('zoomImg');if(img)img.style.transform='scale(1)';}
document.addEventListener('keydown',function(e){if(e.key==='Escape')closeZoomBtn();});

// DLC Modal
function openDlcModal(){
  document.getElementById('dlcMTitle').textContent='Add DLC Rate';
  document.getElementById('dlcId').value='0';
  document.getElementById('dlcV').value='';
  document.getElementById('dlcFY').value='<?= e(currentFY()) ?>';
  document.getElementById('dlcEf').value='';
  ['road_30ft','road_40ft','road_60ft','road_80ft','road_100ft','near_highway'].forEach(function(f){var el=document.getElementById('dlc_'+f);if(el)el.value='';});
  document.getElementById('dlcNotes').value='';
  document.getElementById('dlcSubmitBtn').textContent='Save DLC Rate';
  document.getElementById('dlcModal').classList.add('show');
}
function openDlcEdit(d){
  document.getElementById('dlcMTitle').textContent='Edit DLC Rate';
  document.getElementById('dlcId').value=d.id;
  document.getElementById('dlcV').value=d.village_id;
  document.getElementById('dlcFY').value=d.financial_year;
  document.getElementById('dlcEf').value=d.effective_from;
  ['road_30ft','road_40ft','road_60ft','road_80ft','road_100ft','near_highway'].forEach(function(f){var el=document.getElementById('dlc_'+f);if(el)el.value=d[f]||'';});
  document.getElementById('dlcNotes').value=d.notes||'';
  document.getElementById('dlcSubmitBtn').textContent='Update DLC Rate';
  document.getElementById('dlcModal').classList.add('show');
}
function closeDlcModal(){document.getElementById('dlcModal').classList.remove('show');}
document.addEventListener('keydown',function(e){if(e.key==='Escape'){closeDlcModal();closeZoomBtn();}});
document.getElementById('dlcModal')?.addEventListener('click',function(e){if(e.target===this)closeDlcModal();});
</script>
</body>
</html>
