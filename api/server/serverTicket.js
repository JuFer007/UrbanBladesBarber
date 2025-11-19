const express = require("express");
const puppeteer = require("puppeteer");
const fs = require("fs");
const path = require("path");
const cors = require("cors");

const app = express();
app.use(cors());
app.use(express.json());

app.post("/generar-ticket", async (req, res) => {
  const { cliente, fecha, servicios } = req.body;
  const total = servicios.reduce((sum, s) => sum + s.precio, 0);

  let template = fs.readFileSync(path.join("ticket.html"), "utf8");

  const serviciosHTML = servicios.map(s => `<div class="servicio"><span>${s.nombre}</span><span>S/${s.precio.toFixed(2)}</span></div>`).join("");

  template = template.replace("{{cliente}}", cliente)
                     .replace("{{fecha}}", fecha)
                     .replace("{{total}}", total.toFixed(2))
                     .replace("{{servicios}}", serviciosHTML);

  try {
    const browser = await puppeteer.launch({ args: ['--no-sandbox'] });
    const page = await browser.newPage();
    await page.setContent(template, { waitUntil: "networkidle0" });

    const pdfBuffer = await page.pdf({ width: "220px", printBackground: true });
    await browser.close();

    res.writeHead(200, {
      "Content-Type": "application/pdf",
      "Content-Disposition": "inline; filename=ticket.pdf",
      "Content-Length": pdfBuffer.length
    }).end(pdfBuffer);

  } catch (err) {
    console.error(err);
    res.status(500).send("Error generando el ticket");
  }
});

app.listen(3000, () => console.log("API corriendo en http://localhost:3000"));
