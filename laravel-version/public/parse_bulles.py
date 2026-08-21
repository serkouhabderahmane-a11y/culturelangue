import zipfile
from xml.etree import ElementTree

base = 'C:/Users/laptop 368/Desktop/my computer/code/websites/education/cultulangues/cultulangues'
docx_path = base + '/img/Bulles pour la formation en solo (1).docx'

with zipfile.ZipFile(docx_path, 'r') as z:
    rels_xml = z.read('word/_rels/document.xml.rels')
    rels_tree = ElementTree.fromstring(rels_xml)
    rid_map = {}
    for rel in rels_tree:
        rid = rel.get('Id')
        target = rel.get('Target')
        if target and 'media' in target:
            rid_map[rid] = target
    
    print("Relationships:", rid_map)
    print()
    
    xml_content = z.read('word/document.xml')

tree = ElementTree.fromstring(xml_content)
ns = {
    'w': 'http://schemas.openxmlformats.org/wordprocessingml/2006/main',
    'a': 'http://schemas.openxmlformats.org/drawingml/2006/main',
    'r': 'http://schemas.openxmlformats.org/officeDocument/2006/relationships',
}

body = tree.find('.//w:body', ns)
print('=== BULLES DOCUMENT STRUCTURE ===')
idx = 0
for elem in body:
    tag = elem.tag.split('}')[-1] if '}' in elem.tag else elem.tag
    if tag == 'p':
        texts = elem.findall('.//w:t', ns)
        text = ''.join(t.text or '' for t in texts).strip()
        drawings = elem.findall('.//w:drawing', ns)
        
        if text or drawings:
            idx += 1
            parts = []
            if text:
                parts.append('TEXT: "' + text + '"')
            if drawings:
                blips = elem.findall('.//a:blip', ns)
                for blip in blips:
                    embed = blip.get('{http://schemas.openxmlformats.org/officeDocument/2006/relationships}embed')
                    if embed:
                        img_file = rid_map.get(embed, embed)
                        parts.append('IMAGE: ' + img_file)
            print(str(idx) + ': ' + ' | '.join(parts))
    elif tag == 'tbl':
        rows = elem.findall('.//w:tr', ns)
        print(str(idx) + ': TABLE with ' + str(len(rows)) + ' rows')
        for row in rows:
            cells = row.findall('.//w:tc', ns)
            cell_texts = []
            for cell in cells:
                t = cell.findall('.//w:t', ns)
                cell_texts.append(''.join(x.text or '' for x in t))
            print('    ROW: ' + ' | '.join(cell_texts))
