<?php
function dlc_export_xlsx(array $rows): string {
    $headers=['Revenue Village','Tehsil','District','Financial Year','Effective From',
              '30ft Road (Rs/sqm)','40ft Road (Rs/sqm)','60ft Road (Rs/sqm)',
              '80ft Road (Rs/sqm)','100ft Road (Rs/sqm)','Near Highway (Rs/sqm)','Notes',
              '30ft Road (Rs/sqft)','40ft Road (Rs/sqft)','60ft Road (Rs/sqft)',
              '80ft Road (Rs/sqft)','100ft Road (Rs/sqft)','Near Highway (Rs/sqft)'];
    $strings=$headers; $ssi=array_flip($strings);
    foreach ($rows as $r) {
        foreach (['village_name','tehsil','district','financial_year','notes'] as $f) {
            $v=(string)($r[$f]??'');
            if ($v!==''&&!isset($ssi[$v])){$ssi[$v]=count($strings);$strings[]=$v;}
        }
    }
    $xlRows=''; $ri=1;
    $hc=''; $cols=['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R'];
    foreach ($cols as $ci=>$col) $hc.='<c r="'.$col.$ri.'" t="s"><v>'.$ci.'</v></c>';
    $xlRows.='<row r="1">'.$hc.'</row>'; $ri++;
    foreach ($rows as $r) {
        $data=[$r['village_name']??'',$r['tehsil']??'',$r['district']??'',$r['financial_year']??'',$r['effective_from']??'',$r['road_30ft']??'',$r['road_40ft']??'',$r['road_60ft']??'',$r['road_80ft']??'',$r['road_100ft']??'',$r['near_highway']??'',$r['notes']??'',
            // sq.ft columns
            $r['road_30ft']!==''&&$r['road_30ft']!==null?round($r['road_30ft']/10.76,2):'',
            $r['road_40ft']!==''&&$r['road_40ft']!==null?round($r['road_40ft']/10.76,2):'',
            $r['road_60ft']!==''&&$r['road_60ft']!==null?round($r['road_60ft']/10.76,2):'',
            $r['road_80ft']!==''&&$r['road_80ft']!==null?round($r['road_80ft']/10.76,2):'',
            $r['road_100ft']!==''&&$r['road_100ft']!==null?round($r['road_100ft']/10.76,2):'',
            $r['near_highway']!==''&&$r['near_highway']!==null?round($r['near_highway']/10.76,2):'',
        ];
        $cells='';
        foreach ($cols as $i=>$col) {
            $v=$data[$i]; if ($v==='') continue;
            if (in_array($i,[5,6,7,8,9,10,12,13,14,15,16,17])&&is_numeric($v)) {
                $cells.='<c r="'.$col.$ri.'"><v>'.htmlspecialchars($v).'</v></c>';
            } else {
                if (!isset($ssi[$v])){$ssi[$v]=count($strings);$strings[]=$v;}
                $cells.='<c r="'.$col.$ri.'" t="s"><v>'.$ssi[$v].'</v></c>';
            }
        }
        $xlRows.='<row r="'.$ri.'">'.$cells.'</row>'; $ri++;
    }
    $ssXml='<?xml version="1.0" encoding="UTF-8"?><sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="'.count($strings).'" uniqueCount="'.count($strings).'">';
    foreach ($strings as $s) $ssXml.='<si><t>'.htmlspecialchars($s).'</t></si>';
    $ssXml.='</sst>';
    $wsXml='<?xml version="1.0" encoding="UTF-8"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>'.$xlRows.'</sheetData></worksheet>';
    $wbXml='<?xml version="1.0" encoding="UTF-8"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="DLC Rates" sheetId="1" r:id="rId1"/></sheets></workbook>';
    $relsMain='<?xml version="1.0"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>';
    $relsWb='<?xml version="1.0"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/></Relationships>';
    $ct='<?xml version="1.0"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/></Types>';
    $tmp=tempnam(sys_get_temp_dir(),'dlc_');
    $zip=new ZipArchive(); $zip->open($tmp,ZipArchive::OVERWRITE);
    $zip->addFromString('[Content_Types].xml',$ct);
    $zip->addFromString('_rels/.rels',$relsMain);
    $zip->addFromString('xl/workbook.xml',$wbXml);
    $zip->addFromString('xl/_rels/workbook.xml.rels',$relsWb);
    $zip->addFromString('xl/worksheets/sheet1.xml',$wsXml);
    $zip->addFromString('xl/sharedStrings.xml',$ssXml);
    $zip->close(); $data=file_get_contents($tmp); unlink($tmp);
    return $data;
}
function dlc_import_csv(string $path): array {
    $rows=[]; if(!($h=fopen($path,'r'))) return [];
    $hdr=null;
    while(($line=fgetcsv($h,2000,','))!==false){
        if(!$hdr){$hdr=array_map(fn($v)=>strtolower(trim($v)),$line);continue;}
        if(count($line)<2) continue;
        $rows[]=array_combine($hdr,array_pad($line,count($hdr),''));
    }
    fclose($h); return $rows;
}
function dlc_map_csv_row(array $row): array {
    $map=['village name'=>'village_name','financial year'=>'financial_year','effective from'=>'effective_from',
          '30 ft'=>'road_30ft','40 ft'=>'road_40ft','60 ft'=>'road_60ft','80 ft'=>'road_80ft',
          '100 ft'=>'road_100ft','near highway'=>'near_highway','highway'=>'near_highway','notes'=>'notes'];
    $out=[];
    foreach ($row as $k=>$v) {
        $kl=strtolower(trim(preg_replace('/\(.*\)/','',preg_replace('/[^a-z0-9 ]/i','',$k))));
        foreach ($map as $pat=>$col) { if(str_contains($kl,$pat)){$out[$col]=trim($v);break;} }
    }
    return $out;
}