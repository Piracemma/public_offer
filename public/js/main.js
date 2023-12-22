  function alternarTipoSenha() {
    var senha = document.getElementById("senha");
    var senha1 = document.getElementById("senha1");
    var senha2 = document.getElementById("senha2");
    var tipoAtual = senha.type;

    // Alterna para o tipo de entrada oposto
    if (tipoAtual === "password") {
      senha.type = "text";
      senha1.style.display = 'none';
      senha2.style.display = 'block';
    } else {
      senha.type = "password";
      senha1.style.display = 'block';
      senha2.style.display = 'none';
    }
  }
  function alternarTipoSenha2() {
    var senha = document.getElementById("validasenha");
    var senha3 = document.getElementById("senha3");
    var senha4 = document.getElementById("senha4");
    var tipoAtual = senha.type;

    // Alterna para o tipo de entrada oposto
    if (tipoAtual === "password") {
      senha.type = "text";
      senha3.style.display = 'none';
      senha4.style.display = 'block';
    } else {
      senha.type = "password";
      senha3.style.display = 'block';
      senha4.style.display = 'none';
    }
  }

  //Validar senha

  var senha = document.getElementById('senha');
  var confsenha = senha.value;
  if(confsenha == '') {
    var validasenha = document.getElementById("validasenha");
    var confvalidasenha = validasenha.value;
    var smallsenha = document.getElementById("conferesenha");
    var textRegex = document.getElementById('conferesenharegex');

    textRegex.style.display = "none";
    smallsenha.style.display = "none";
    validasenha.style.borderBottom = "1px solid  #444444";
  }

  function validaSenhaRegex() {

    var validaRegex;

    var padrao = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#/])[A-Za-z\d@$!%*?&#/]{8,20}$/;

    var senha = document.getElementById('senha');
    var confsenha = senha.value;
  
    if (padrao.test(confsenha)) {

      validaRegex = true; // A senha é válida

    } else {

      validaRegex = false; // A senha é inválida

    }

    var textRegex = document.getElementById('conferesenharegex');

    if (validaRegex) {

      textRegex.style.display = "none";
      senha.style.borderBottom = "1px solid  #444444";

    } else {

      textRegex.style.display = "block";
      senha.style.borderBottom = "1px solid  #FF0D0D";

    }

  }

  function limpaSmal() {

    var senha = document.getElementById('senha');
    var confsenha = senha.value;

    if(confsenha == '') {

      var textRegex = document.getElementById('conferesenharegex');

      textRegex.style.display = "none";
      senha.style.borderBottom = "1px solid  #444444";

    }

  }

  function verifica() {

    validaSenha();

  }
  function validaSenha() {//verifica se são igauis
    var senha = document.getElementById('senha');
    var confsenha = senha.value;
    var validasenha = document.getElementById("validasenha");
    var confvalidasenha = validasenha.value;
    

    var smallsenha = document.getElementById("conferesenha");

    if(confsenha == confvalidasenha) {
        smallsenha.style.display = "none";
        validasenha.style.borderBottom = "1px solid  #444444";
    } else {
        smallsenha.style.display = "block";
        validasenha.style.borderBottom = "1px solid  #FF0D0D";
    }

  }
  // Seleciona a div pelo ID
  var div = document.getElementById("popup");

  // Define o tempo total de espera e o intervalo de tempo para diminuir a opacidade
  var tempoTotal = 5000;
  var intervalo = 50;

  // Define a opacidade inicial e a quantidade a ser diminuída a cada intervalo
  var opacidadeInicial = 1;
  var quantidadeOpacidade = opacidadeInicial / (tempoTotal / intervalo);

  // Define o intervalo para diminuir a opacidade da div
  var intervaloId = setInterval(function() {
    opacidadeInicial -= quantidadeOpacidade;
    div.style.opacity = opacidadeInicial;

    // Verifica se a opacidade chegou a zero e esconde a div
    if (opacidadeInicial <= 0) {
      clearInterval(intervaloId);
      div.style.display = "none";
    }
  }, intervalo);

  // Esconde a div após o tempo total de espera
  setTimeout(function() {
    clearInterval(intervaloId);
    div.style.display = "none";
  }, tempoTotal);




  //validador de documento
  var documento = document.getElementById('documento');
  var confdoc = documento.value;
  if(confdoc == '') {
    var textodocumento = document.getElementById("conferedocumento");
    textodocumento.style.display = "none";
    documento.style.borderBottom = "1px solid  #444444";
  }

  function limpaDoc() {

    var documento = document.getElementById('documento');
    var confdoc = documento.value;
    if(confdoc == '') {
      var textodocumento = document.getElementById("conferedocumento");
      textodocumento.style.display = "none";
      documento.style.borderBottom = "1px solid  #444444";
    }

  }

  function validarCPFCNPJ() {
    var documento = document.getElementById('documento');
    var confdocumento = documento.value;
    numero = confdocumento.replace(/\D/g, '');
    
    if (numero.length === 11) {
        if (/^(\d)\1*$/.test(numero)) {
            return false;
        }
        
        let soma = 0;
        for (let i = 0; i < 9; i++) {
            soma += parseInt(numero[i]) * (10 - i);
        }
        
        const digito1 = (soma % 11 < 2) ? 0 : 11 - (soma % 11);
        
        soma = 0;
        for (let i = 0; i < 10; i++) {
            soma += parseInt(numero[i]) * (11 - i);
        }
        
        const digito2 = (soma % 11 < 2) ? 0 : 11 - (soma % 11);
        
        if (numero[9] == digito1 && numero[10] == digito2) {
            return true;
        }
    } else if (numero.length === 14) {
        if (/^(\d)\1*$/.test(numero)) {
            return false;
        }
        
        let soma = 0;
        let multiplicador = 5;
        for (let i = 0; i < 12; i++) {
            soma += parseInt(numero[i]) * multiplicador;
            multiplicador = (multiplicador === 2) ? 9 : multiplicador - 1;
        }
        
        const digito1 = soma % 11 < 2 ? 0 : 11 - (soma % 11);
        
        soma = 0;
        multiplicador = 6;
        for (let i = 0; i < 13; i++) {
            soma += parseInt(numero[i]) * multiplicador;
            multiplicador = (multiplicador === 2) ? 9 : multiplicador - 1;
        }
        
        const digito2 = soma % 11 < 2 ? 0 : 11 - (soma % 11);
        
        if (numero[12] == digito1 && numero[13] == digito2) {
            return true;
        }
    }
    
    return false;
}

function validadedoc() {
  var styledocumento = document.getElementById('documento');
  var textodocumento = document.getElementById("conferedocumento");

  if(validarCPFCNPJ()) {
    textodocumento.style.display = "none";
    styledocumento.style.borderBottom = "1px solid  #444444";
  } else {
    textodocumento.style.display = "block";
    styledocumento.style.borderBottom = "1px solid  #FF0D0D";
  }

}