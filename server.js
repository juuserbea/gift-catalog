const express = require('express');
const fs = require('fs');
const cors = require('cors');

const app = express();
const PORT = 3000;

app.use(cors());
app.use(express.json());  // Permite receber JSON no body

app.post('/escolher', (req, res) => {
    const { id } = req.body;

    if (!id) return res.status(400).send({ message: 'ID do presente é obrigatório!' });

    fs.readFile('presente.json', 'utf8', (err, data) => {
        if (err) return res.status(500).send({ message: 'Erro ao ler o arquivo.' });

        let presentes = JSON.parse(data);
        const index = presentes.findIndex(p => p.id === id);

        if (index === -1) return res.status(404).send({ message: 'Presente não encontrado!' });
        if (presentes[index].escolhido) return res.status(400).send({ message: 'Esse presente já foi escolhido!' });

        presentes[index].escolhido = true;

        fs.writeFile('presente.json', JSON.stringify(presentes, null, 2), (err) => {
            if (err) return res.status(500).send({ message: 'Erro ao salvar o arquivo.' });
            res.send({ message: 'Presente escolhido com sucesso!' });
        });
    });
});

app.listen(PORT, () => {
    console.log(`Servidor rodando em http://localhost:${PORT}`);
});
