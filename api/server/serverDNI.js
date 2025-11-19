const express = require("express");
const axios = require("axios");
const cors = require("cors");

const app = express();
app.use(cors());
app.use(express.json());

const TOKEN = "sk_10968.q9l2alPcYhxW4dFGvTZ7fDdwEhHzZEso";

app.get("/dni/:numero", async (req, res) => {
  const { numero } = req.params;

  try {
    const response = await axios.get("https://api.decolecta.com/v1/reniec/dni", {
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${TOKEN}`,
      },
      params: {
        numero: numero,
      },
    });

    res.json(response.data);
  } catch (error) {
    console.error("Error al consultar DNI:", error.message);

    if (error.response) {
      console.error(error.response.data);
      res.status(error.response.status).json(error.response.data);
    } else {
      res.status(500).json({ error: "Error al consultar DNI" });
    }
  }
});

app.listen(3001, () => {
  console.log("Servidor corriendo en http://localhost:3001");
});
