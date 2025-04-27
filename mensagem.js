// mensagem.js
const params = new URLSearchParams(window.location.search);
const nome = params.get("nome");

if (nome) {
  document.getElementById("nome-usuario").textContent = nome;
} else {
  document.getElementById("nome-usuario").textContent = "convidado(a)";
}
