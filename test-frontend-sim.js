// Simular exactamente lo que hace el frontend en el navegador
const http = require('http');

const data = JSON.stringify({
    email: 'admin@carpinteria.com',
    password: 'admin123'
});

const options = {
    hostname: 'localhost',
    port: 3000,
    path: '/api/auth/login',
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Content-Length': Buffer.byteLength(data),
        'Origin': 'http://localhost:3000'
    }
};

console.log('Enviando solicitud como lo haría el navegador...\n');

const req = http.request(options, (res) => {
    let body = '';
    res.on('data', (chunk) => {
        body += chunk;
    });
    res.on('end', () => {
        console.log('Status:', res.statusCode);
        console.log('Headers:', JSON.stringify(res.headers, null, 2));
        console.log('\nResponse:', body);
        
        try {
            const parsed = JSON.parse(body);
            if (parsed.success) {
                console.log('\n✅ LOGIN EXITOSO!');
                console.log('Token:', parsed.token.substring(0, 50) + '...');
                console.log('Usuario:', parsed.usuario);
            }
        } catch(e) {
            console.log('Error al parsear:', e.message);
        }
    });
});

req.on('error', (e) => {
    console.error('Error:', e.message);
});

req.write(data);
req.end();

