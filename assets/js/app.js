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
  document.getElementById('dlcFY').value=CURRENT_FY;
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
function syncColor(input){var v=input.value;if(/^#[0-9a-fA-F]{6}$/.test(v)){var pair=input.previousElementSibling;if(pair&&pair.type==='color')pair.value=v;}}
    function resetTheme(){var defaults={'theme_primary':'#81A6C6','theme_bg':'#F3E3D0','theme_surface':'#FFFFFF','theme_border':'#D2C4B4','theme_btn_text':'#FFFFFF','theme_heading':'#2C3A4A','theme_text':'#4A5E70','theme_sidebar_bg':'#FFFFFF','theme_topbar_bg':'#FFFFFF'};
    for(var k in defaults){var t=document.getElementById(k+'_text');if(t){t.value=defaults[k];var c=t.previousElementSibling;if(c&&c.type==='color')c.value=defaults[k];}}}
