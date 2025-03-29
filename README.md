# Gestão de Anuidades - Devs do RN

Este é um sistema de gestão de anuidades desenvolvido em PHP, projetado para facilitar o cadastro de associados, a geração de cobranças e o acompanhamento de pagamentos.

## 📌 Funcionalidades

- **Cadastro de Associados**: Permite adicionar, editar e listar associados.
- **Gestão de Anuidades**: Cadastro de valores anuais e geração de cobranças automáticas ou manuais.
- **Cobranças**: Emissão de cobranças para associados, com controle de status (pendente, paga, vencida).
- **Dashboard**: Visão geral com métricas como total de associados, cobranças abertas, vencidas e valores arrecadados.
- **Relatórios**: Acompanhamento da situação de pagamentos e arrecadação por ano.

## 📂 Estrutura do Projeto

```
├── index.php
├── meu_database.sql
├── README.md
├── assets/
│   ├── css/
│   │   ├── style.css
│   ├── js/
│   │   ├── scripts.js
├── classes/
│   ├── Anuidade.php
│   ├── Associado.php
│   ├── Cobranca.php
├── config/
│   ├── Database.php
├── views/
│   ├── footer.php
│   ├── header.php
├── anuidade/
│   ├── form.php
├── associado/
│   ├── editar.php
│   ├── form.php
│   ├── lista.php
├── cobranca/
│   ├── carregar_anuidades.php
│   ├── form.php
├── situacao/
│   ├── pagamento.php
```

## 🔧 Requisitos

- **Servidor Web**: Apache ou similar.
- **PHP**: Versão 7.4 ou superior.
- **Banco de Dados**: MySQL.
- **Dependências**:
  - [jQuery](https://jquery.com/)
  - [jQuery Mask Plugin](https://igorescobar.github.io/jQuery-Mask-Plugin/)
  - [jQuery MaskMoney](https://github.com/plentz/jquery-maskmoney)
  - [Bootstrap 5](https://getbootstrap.com/)
  - [Chart.js](https://www.chartjs.org/)

## 🚀 Configuração

1. Clone este repositório para o diretório do seu servidor web:
   ```sh
   git clone https://github.com/Marcel-Rodrigues/devsrn
   ```
2. Importe o arquivo `meu_database.sql` no seu banco de dados MySQL.
3. Configure as credenciais do banco de dados no arquivo `config/Database.php`.
4. Acesse o sistema pelo navegador através do endereço configurado no servidor.

## 📘 Uso

- Acesse a página inicial para visualizar o **dashboard**.
- Utilize o menu para cadastrar **associados**, **anuidades** e **gerar cobranças**.
- Acompanhe a situação de pagamentos na seção **"Situação de Pagamento"**.

## 📜 Licença

Este projeto é de uso interno e não possui uma licença específica.

