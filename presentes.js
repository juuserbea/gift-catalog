document.addEventListener("DOMContentLoaded", async () => {

  // Contador regressivo
  const destino = new Date("2025-04-20T15:00:00-01:00");
  const contador = document.getElementById("contador");

  function atualizarContador() {
    const agora = new Date();
    const diff = destino - agora;
    if (diff <= 0) {
      contador.innerHTML = "🎉 Chegou o grande dia! 🎉";
      return;
    }
    const dias = Math.floor(diff / (1000 * 60 * 60 * 24));
    const horas = Math.floor((diff / (1000 * 60 * 60)) % 24);
    const minutos = Math.floor((diff / (1000 * 60)) % 60);
    const segundos = Math.floor((diff / 1000) % 60);
    contador.innerHTML = `Faltam ${dias} dia${dias !== 1 ? 's' : ''}, ${horas}h, ${minutos}m e ${segundos}s para a festa! 🎈`;
  }
  setInterval(atualizarContador, 1000);
  atualizarContador();

  // Função para carregar os presentes disponíveis
  function carregarPresentes() {
    const presentesRef = firebase.database().ref('presentes');
    
    // Adicionando listener em tempo real no Firebase
    presentesRef.on('value', (snapshot) => {
      const presentes = snapshot.val();
      const listaPresentes = document.getElementById('lista-presentes');
      listaPresentes.innerHTML = '';  // Limpa a lista antes de atualizar

      for (const id in presentes) {
        const presente = presentes[id];
        if (!presente.reservado) {  // Verifica se o presente está disponível
          const div = document.createElement('div');
          div.classList.add('presente');
          div.innerHTML = `
            <img src="${presente.image}" alt="${presente.title}">
            <h3>${presente.title}</h3>
            <button onclick="reservarPresente('${id}', 'Maria')">Reservar</button>
          `;
          listaPresentes.appendChild(div);
        }
      }
    });
  }

  // Função para reservar o presente no Firebase
  function reservarPresente(presenteId, nome) {
    const presenteRef = firebase.database().ref('presentes/' + presenteId);
    
    // Atualiza a chave "reservado" no banco de dados
    presenteRef.update({
      reservado: true,
      nomePessoa: nome
    }).then(() => {
      alert('Presente reservado com sucesso!');
    }).catch((error) => {
      console.error('Erro ao reservar o presente:', error);
    });
  }

  // Função para confirmar o presente no carrinho
  window.confirmarPresente = function(botao) {
    const presente = botao.closest(".presente");
    const nomePresente = presente.querySelector("p").innerText;

    // Verifica se o presente já foi adicionado ao carrinho
    if (!carrinho.includes(nomePresente)) {
      carrinho.push(nomePresente);
      localStorage.setItem("carrinhoBia", JSON.stringify(carrinho));
      atualizarCarrinho();
      botao.innerText = "Adicionado! 💖";
      botao.disabled = true;

      // Atualizar lista de reservados localmente e remover o presente da lista de visualização
      const idPresente = presente.getAttribute("data-id");
      reservados.push(idPresente);
      localStorage.setItem("presentesReservadosBia", JSON.stringify(reservados));

      // Remover o presente escolhido da lista de exibição
      presente.remove();

      // Reservar o presente no Firebase
      reservarPresente(idPresente, 'Maria');
    }
  };

  // Função para carregar a quantidade de itens no carrinho
  let carrinho = JSON.parse(localStorage.getItem("carrinhoBia")) || [];
  const carrinhoQuantidade = document.getElementById("carrinho-quantidade");
  const listaCarrinho = document.getElementById("lista-carrinho");
  const nomePessoaInput = document.getElementById("nomePessoa");

  function atualizarCarrinho() {
    carrinhoQuantidade.textContent = carrinho.length;
    listaCarrinho.innerHTML = "";
    carrinho.forEach((item, index) => {
      const li = document.createElement("li");
      li.className = "flex justify-between items-center bg-yellow-100 p-2 rounded";
      li.innerHTML = `<span>${item}</span> <button class="text-red-500 hover:text-red-700" onclick="removerPresente(${index})">Remover</button>`;
      listaCarrinho.appendChild(li);
    });
  }

  window.removerPresente = function(index) {
    carrinho = carrinho.filter((_, i) => i !== index);
    localStorage.setItem("carrinhoBia", JSON.stringify(carrinho));
    atualizarCarrinho();
  };

  // Evento para finalizar a compra
  document.getElementById("finalizarCarrinho").onclick = async () => {
    if (carrinho.length === 0) return alert("Seu carrinho está vazio!");
    const nomePessoa = nomePessoaInput.value.trim();
    if (!nomePessoa) return alert("Por favor, informe seu nome antes de finalizar.");

    // Salvar no localStorage como reservado
    reservados = reservados.concat(
      presentes.filter(p => carrinho.includes(p.title)).map(p => p.id)
    );
    localStorage.setItem("presentesReservadosBia", JSON.stringify(reservados));

    // WhatsApp
    const mensagem = `Olá, sou ${nomePessoa} e escolhi os seguintes presentes:\n\n${carrinho.map(p => `- ${p}`).join("\n")}\n\nCom carinho! 💖`;
    const mensagemCodificada = encodeURIComponent(mensagem);
    const numeroWhatsapp = "5591999921329";
    const urlWhatsapp = `https://wa.me/${numeroWhatsapp}?text=${mensagemCodificada}`;
    window.open(urlWhatsapp, "_blank");

    // Limpar carrinho
    carrinho = [];
    localStorage.removeItem("carrinhoBia");
    atualizarCarrinho();
    document.getElementById("painel-carrinho").style.display = "none";
    window.location.href = "obrigado.html";
  };

  // Chama a função para carregar os presentes
  carregarPresentes();

  atualizarCarrinho();
});
