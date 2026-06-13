const fs = require('fs');
const pdf = require('pdf-parse');

let dataBuffer = fs.readFileSync('Demande ordre mission conbiné .pdf');

pdf(dataBuffer).then(function(data) {
    console.log("--- PDF TEXT ---");
    console.log(data.text);
}).catch(console.error);
