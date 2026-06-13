import fitz
import json

doc = fitz.open("Demande ordre mission conbiné .pdf")
page = doc[0]
blocks = page.get_text("blocks")
blocks.sort(key=lambda b: b[1])

out = []
for b in blocks:
    out.append({"x": b[0], "y": b[1], "text": b[4].strip()})

with open("pdf_layout.json", "w", encoding="utf-8") as f:
    json.dump(out, f, indent=2, ensure_ascii=False)
