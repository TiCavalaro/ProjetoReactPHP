import React, { useState, useEffect } from 'react';
import '../Css/Gestao.css';

export default function Gestao() {
  const [financa, setFinanca] = useState({
    tipo: 'entrada',
    descricao: '',
    valor: '',
    categoria: '',
    data: ''
  });

  const [financas, setFinancas] = useState([]);
  const [modoEdicao, setModoEdicao] = useState(false);
  const [idEdicao, setIdEdicao] = useState(null);

  const carregarFinancas = async () => {
    try {
      const response = await fetch('http://localhost/ProjetoReact/ProjetoReactPHP/back/inserir.php');
      const dados = await response.json();
      setFinancas(dados.financas || []);
    } catch (error) {
      console.error('Erro ao carregar finanças:', error);
    }
  };

  useEffect(() => {
    carregarFinancas();
  }, []);

  const handleFinancaSubmit = async (e) => {
    e.preventDefault();
    try {
      const url = 'http://localhost/ProjetoReact/ProjetoReactPHP/back/inserir.php';
      const method = modoEdicao ? 'PUT' : 'POST';
      const body = JSON.stringify(modoEdicao ? { id: idEdicao, ...financa } : { acao: 'financa', ...financa });

      const response = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json' },
        body
      });

      const data = await response.json();
      alert(data.mensagem);

      setFinanca({
        tipo: 'entrada',
        descricao: '',
        valor: '',
        categoria: '',
        data: ''
      });
      setModoEdicao(false);
      setIdEdicao(null);
      carregarFinancas();
    } catch (error) {
      console.error("Erro ao salvar:", error);
      alert("Erro ao conectar com o servidor.");
    }
  };

  const editarFinanca = (item) => {
    setFinanca({
      tipo: item.tipo,
      descricao: item.descricao,
      valor: item.valor,
      categoria: item.categoria,
      data: item.data
    });
    setModoEdicao(true);
    setIdEdicao(item.id);
  };

  const excluirFinanca = async (id) => {
    if (window.confirm("Tem certeza que deseja excluir?")) {
      try {
        const response = await fetch('http://localhost/ProjetoReact/ProjetoReactPHP/back/inserir.php', {
          method: 'DELETE',
          body: new URLSearchParams({ id })
        });
        const data = await response.json();
        alert(data.mensagem);
        carregarFinancas();
      } catch (error) {
        console.error("Erro ao excluir:", error);
      }
    }
  };

// ... seu código completo anterior permanece igual até aqui:

  return (
    <div className="gestao-container">
      <h1>Gestão Financeira</h1>

      <section className="form-section">
        <h2>{modoEdicao ? 'Editar Finança' : 'Registrar Finança'}</h2>
        <form onSubmit={handleFinancaSubmit}>
          <select
            value={financa.tipo}
            onChange={(e) => setFinanca({ ...financa, tipo: e.target.value })}
          >
            <option value="entrada">Entrada</option>
            <option value="saida">Saída</option>
          </select>
          <input
            type="text"
            placeholder="Descrição"
            value={financa.descricao}
            onChange={(e) => setFinanca({ ...financa, descricao: e.target.value })}
            required
          />
          <input
            type="number"
            placeholder="Valor"
            value={financa.valor}
            onChange={(e) => setFinanca({ ...financa, valor: e.target.value })}
            required
          />
          <input
            type="text"
            placeholder="Categoria"
            value={financa.categoria}
            onChange={(e) => setFinanca({ ...financa, categoria: e.target.value })}
            required
          />
          <input
            type="date"
            value={financa.data}
            onChange={(e) => setFinanca({ ...financa, data: e.target.value })}
            required
          />
          <div className="botoes-form">
            <button type="submit" className={modoEdicao ? 'btn-atualizar' : 'btn-registrar'}>
              {modoEdicao ? 'Atualizar' : 'Registrar'}
            </button>
            {modoEdicao && (
              <button
                type="button"
                className="btn-cancelar"
                onClick={() => {
                  setModoEdicao(false);
                  setIdEdicao(null);
                  setFinanca({
                    tipo: 'entrada',
                    descricao: '',
                    valor: '',
                    categoria: '',
                    data: ''
                  });
                }}
              >
                Cancelar
              </button>
            )}
          </div>
        </form>
      </section>

      <section className="form-section">
        <h2>Transações Registradas</h2>
        {financas.length === 0 ? (
          <p>Nenhuma transação registrada.</p>
        ) : (
          <table>
            <thead>
              <tr>
                <th>Tipo</th>
                <th>Descrição</th>
                <th>Valor</th>
                <th>Categoria</th>
                <th>Data</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              {financas.map((item, index) => (
                <tr key={item.id} className={index % 2 === 0 ? 'linha-par' : 'linha-impar'}>
                  <td className={`tipo ${item.tipo}`}>{item.tipo === 'entrada' ? '⬆ Entrada' : '⬇ Saída'}</td>
                  <td>{item.descricao}</td>
                  <td className={`valor ${item.tipo}`}>
                    R$ {parseFloat(item.valor).toLocaleString('pt-BR', { minimumFractionDigits: 2 })}
                  </td>
                  <td>{item.categoria}</td>
                  <td>{new Date(item.data).toLocaleDateString('pt-BR')}</td>
                  <td className="acoes">
                    <button className="btn-editar" onClick={() => editarFinanca(item)}>Editar</button>
                    <button className="btn-excluir" onClick={() => excluirFinanca(item.id)}>Excluir</button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </section>
    </div>
  );
}
