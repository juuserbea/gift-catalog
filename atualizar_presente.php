<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Escolha seu Presente - Bia 15 Anos</title>
  <link rel="stylesheet" href="seu-estilo.css" />
  <script src="https://unpkg.com/react@18/umd/react.development.js" crossorigin></script>
  <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js" crossorigin></script>
  <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-gradient-to-b from-yellow-50 to-yellow-100 min-h-screen flex flex-col items-center justify-center">

  <div id="root"></div>

  <script type="text/babel">
    const { useState, Fragment } = React;

    
      const [cart, setCart] = useState([]);
      const [showCart, setShowCart] = useState(false);
      const [nomePessoa, setNomePessoa] = useState('');

      const addToCart = (product) => {
        setCart([...cart, { ...product }]);
        setShowCart(true);
      };

      const removeFromCart = (index) => {
        const newCart = [...cart];
        newCart.splice(index, 1);
        setCart(newCart);
      };

      const total = cart.reduce((sum, item) => sum + item.price, 0);

      const finalizarEscolha = () => {
  // Enviar dados para o WhatsApp
  const mensagem = `Olá! A pessoa ${nomePessoa} escolheu os seguintes presentes:\n\n` +
                   cart.map(item => `${item.title} - R$ ${item.price}`).join('\n') + 
                   `\n\nTotal: R$ ${total.toFixed(2)}`;

  const url = `https://api.whatsapp.com/send?phone=+559184287263&text=${encodeURIComponent(mensagem)}`;
  window.open(url, '_blank');

  // Atualizar o arquivo JSON de presentes
  const urlPHP = 'escolher_presente.php';
  fetch(urlPHP, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ presente: cart[0].title }) // Passar o nome do presente escolhido
  })
  .then(response => response.json())
  .then(data => {
    if (data.sucesso) {
      alert('Escolha registrada com sucesso!');
      setCart([]);
      setShowCart(false);
    } else {
      alert('Erro ao registrar sua escolha. Tente novamente.');
    }
  });
};


      return (
        <div className="w-full max-w-5xl p-4">
          <h1 className="text-3xl text-center text-yellow-700 font-bold mb-6">Escolha seu Presente 🎁</h1>

          <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            {products.map(product => (
              <div key={product.id} className="bg-white shadow-lg rounded-2xl p-4 flex flex-col items-center justify-between">
                <div className="w-full h-40 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-lg flex items-center justify-center text-4xl text-white font-bold">
                  {product.title.charAt(0)}
                </div>
                <h3 className="mt-4 text-lg font-semibold text-center">{product.title}</h3>
                <p className="text-sm text-gray-600 text-center mt-2">{product.description}</p>
                <div className="mt-2 text-yellow-700 font-bold">R$ {product.price.toFixed(2)}</div>
                <button 
                  onClick={() => addToCart(product)}
                  className="mt-4 w-full bg-yellow-500 text-white py-2 rounded-lg hover:bg-yellow-600 transition"
                >
                  Escolher Presente
                </button>
              </div>
            ))}
          </div>

          {showCart && (
            <div className="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
              <div className="bg-white p-6 rounded-2xl shadow-xl w-full max-w-md">
                <h2 className="text-2xl font-semibold text-center mb-4">🎁 Seus Presentes:</h2>

                {cart.length === 0 ? (
                  <p className="text-center">Seu carrinho está vazio!</p>
                ) : (
                  <ul className="divide-y">
                    {cart.map((item, index) => (
                      <li key={index} className="flex justify-between py-2">
                        <span>{item.title}</span>
                        <button onClick={() => removeFromCart(index)} className="text-red-500 hover:underline">Remover</button>
                      </li>
                    ))}
                  </ul>
                )}

                <div className="mt-4 text-lg font-bold text-center text-yellow-700">
                  Total: R$ {total.toFixed(2)}
                </div>

                <label htmlFor="nomePessoa" className="block text-center mt-4">Seu nome:</label>
                <input 
                  type="text" 
                  id="nomePessoa" 
                  value={nomePessoa}
                  onChange={(e) => setNomePessoa(e.target.value)}
                  className="w-full border p-2 rounded mt-2" 
                  placeholder="Digite seu nome" 
                />

                <button 
                  className="mt-4 w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition"
                  onClick={finalizarEscolha}
                >
                  Finalizar Escolha
                </button>

                <button 
                  className="mt-3 w-full text-gray-600 underline"
                  onClick={() => setShowCart(false)}
                >
                  Continuar Escolhendo
                </button>
              </div>
            </div>
          )}
        </div>
      );
    }

    ReactDOM.createRoot(document.getElementById('root')).render(<App />);
  </script>

</body>
</html>
