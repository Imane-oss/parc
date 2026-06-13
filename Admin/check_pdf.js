const fs = require('fs');
const { PDFDocument } = require('pdf-lib');

async function run() {
    const pdfBytes = fs.readFileSync('Demande ordre mission conbiné .pdf');
    const pdfDoc = await PDFDocument.load(pdfBytes);
    const form = pdfDoc.getForm();
    const fields = form.getFields();
    console.log("Fields found:", fields.length);
    fields.forEach(field => {
        const type = field.constructor.name;
        const name = field.getName();
        console.log(`${type}: ${name}`);
    });
}
run().catch(console.error);
