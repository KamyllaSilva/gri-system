// ws-server.js
const WebSocket = require('ws');
const http = require('http');
const mysql = require('mysql2/promise');

const server = http.createServer();
const wss = new WebSocket.Server({ server });

// Configuração do banco de dados (ajuste para suas credenciais)
const dbConfig = {
    host: process.env.DB_HOST || 'mysql.railway.internal',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASS || 'uiieAgKnVVmRzCiByaTGwZZPuPurwuQX',
    database: process.env.DB_NAME || 'railway'
};

// Monitorar mudanças no banco de dados
async function monitorDatabaseChanges() {
    const connection = await mysql.createConnection(dbConfig);
    
    // Usando polling por simplicidade (em produção, considere binlog ou triggers)
    setInterval(async () => {
        const [rows] = await connection.query(
            "SELECT COUNT(*) as changes FROM respostas_indicadores WHERE updated_at > DATE_SUB(NOW(), INTERVAL 5 SECOND)"
        );
        
        if (rows[0].changes > 0) {
            wss.clients.forEach(client => {
                if (client.readyState === WebSocket.OPEN) {
                    client.send(JSON.stringify({ type: 'update' }));
                }
            });
        }
    }, 5000);
}

wss.on('connection', (ws) => {
    console.log('Novo cliente conectado');
    
    ws.on('close', () => {
        console.log('Cliente desconectado');
    });
});

server.listen(8080, () => {
    console.log('Servidor WebSocket rodando na porta 8080');
    monitorDatabaseChanges();
});