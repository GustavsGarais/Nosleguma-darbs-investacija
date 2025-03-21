const express = require('express');
const cors = require('cors');
const app = express();

app.use(cors({
  origin: 'http://localhost:5173'
}));

app.post('/api/login', (req, res) => {
  // Your login logic here
  res.send('Login successful');
});

app.listen(3000, () => {
  console.log('Server is running on http://localhost:3000');
});