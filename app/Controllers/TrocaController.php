<?php
namespace app\Controllers;


use app\DAO\SolicitacaoTrocaDAO;
use app\DAO\LivroDAO;
use app\DAO\UsuarioDAO;
use app\Models\SolicitacaoTroca;
// ... resto do código

class TrocaController
{
    private $solicitacaoTrocaDAO;
    private $livroDAO;
    private $usuarioDAO;

    public function __construct($pdo)
    {
        $this->solicitacaoTrocaDAO = new SolicitacaoTrocaDAO($pdo);
        $this->livroDAO = new LivroDAO($pdo);
        $this->usuarioDAO = new UsuarioDAO($pdo);
    }

    public function solicitarTroca(int $livroOfertadoId, int $livroSolicitadoId, int $usuarioSolicitanteId)
    {
        // Buscar informações do livro solicitado
        $livroSolicitado = $this->livroDAO->buscarPorId($livroSolicitadoId);
        if (!$livroSolicitado) {
            return ['sucesso' => false, 'mensagem' => 'Livro solicitado não encontrado.'];
        }

        // Verificar se o livro ofertado pertence ao usuário solicitante
        $livroOfertado = $this->livroDAO->buscarPorId($livroOfertadoId);
        if (!$livroOfertado || $livroOfertado->getUsuarioId() !== $usuarioSolicitanteId) {
            return ['sucesso' => false, 'mensagem' => 'Livro ofertado não pertence ao usuário.'];
        }

        // Verificar se já existe uma solicitação pendente para este livro
        if ($this->solicitacaoTrocaDAO->verificarSolicitacaoExistente($livroSolicitadoId, $usuarioSolicitanteId)) {
            return ['sucesso' => false, 'mensagem' => 'Já existe uma solicitação pendente para este livro.'];
        }
        

        // Criar a solicitação de troca
       $solicitacao = new SolicitacaoTroca(
        $livroOfertadoId,
        $livroSolicitadoId,
        $usuarioSolicitanteId,
        $livroSolicitado->getUsuarioId(),
        'pendente' // ← ADICIONE ESTE 5º PARÂMETRO (status)
    );


        if ($this->solicitacaoTrocaDAO->inserir($solicitacao)) {
            return ['sucesso' => true, 'mensagem' => 'Solicitação de troca enviada com sucesso!'];
        }

        return ['sucesso' => false, 'mensagem' => 'Erro ao enviar solicitação de troca.'];

        

    }

    public function listarSolicitacoesRecebidas(int $usuarioId)
    {
        $solicitacoes = $this->solicitacaoTrocaDAO->buscarPorProprietario($usuarioId);
        
        // Popular os dados dos livros e usuários
        foreach ($solicitacoes as $solicitacao) {
            $solicitacao->setLivroOfertado($this->livroDAO->buscarPorId($solicitacao->getLivroOfertadoId()));
            $solicitacao->setLivroSolicitado($this->livroDAO->buscarPorId($solicitacao->getLivroSolicitadoId()));
            $solicitacao->setUsuarioSolicitante($this->usuarioDAO->buscarPorId($solicitacao->getUsuarioSolicitanteId()));
        }
        
        return $solicitacoes;
    }

    public function listarSolicitacoesEnviadas(int $usuarioId)
    {
        $solicitacoes = $this->solicitacaoTrocaDAO->buscarPorUsuario($usuarioId);
        
        // Popular os dados dos livros e usuários
        foreach ($solicitacoes as $solicitacao) {
            $solicitacao->setLivroOfertado($this->livroDAO->buscarPorId($solicitacao->getLivroOfertadoId()));
            $solicitacao->setLivroSolicitado($this->livroDAO->buscarPorId($solicitacao->getLivroSolicitadoId()));
            $solicitacao->setUsuarioProprietario($this->usuarioDAO->buscarPorId($solicitacao->getUsuarioProprietarioId()));
        }
        
        return $solicitacoes;
    }

    public function responderSolicitacao(int $solicitacaoId, string $status, int $usuarioId)
    {
        $solicitacao = $this->solicitacaoTrocaDAO->buscarPorId($solicitacaoId);
        
        if (!$solicitacao) {
            return ['sucesso' => false, 'mensagem' => 'Solicitação não encontrada.'];
        }
        
        if ($solicitacao->getUsuarioProprietarioId() !== $usuarioId) {
            return ['sucesso' => false, 'mensagem' => 'Você não tem permissão para responder esta solicitação.'];
        }
        
        if ($solicitacao->getStatus() !== 'pendente') {
            return ['sucesso' => false, 'mensagem' => 'Esta solicitação já foi respondida.'];
        }
        
        $statusesValidos = ['aceita', 'rejeitada'];
        if (!in_array($status, $statusesValidos)) {
            return ['sucesso' => false, 'mensagem' => 'Status inválido.'];
        }
        
        if ($this->solicitacaoTrocaDAO->atualizarStatus($solicitacaoId, $status)) {
            // Se a troca foi aceita, marcar os livros como indisponíveis
            if ($status === 'aceita') {
                $this->livroDAO->atualizarDisponibilidade($solicitacao->getLivroOfertadoId(), false);
                $this->livroDAO->atualizarDisponibilidade($solicitacao->getLivroSolicitadoId(), false);
            }
            
            return ['sucesso' => true, 'mensagem' => 'Solicitação ' . $status . ' com sucesso!'];
        }
        
        return ['sucesso' => false, 'mensagem' => 'Erro ao atualizar solicitação.'];
    }

    public function cancelarSolicitacao(int $solicitacaoId, int $usuarioId)
    {
        $solicitacao = $this->solicitacaoTrocaDAO->buscarPorId($solicitacaoId);
        
        if (!$solicitacao) {
            return ['sucesso' => false, 'mensagem' => 'Solicitação não encontrada.'];
        }
        
        if ($solicitacao->getUsuarioSolicitanteId() !== $usuarioId) {
            return ['sucesso' => false, 'mensagem' => 'Você não tem permissão para cancelar esta solicitação.'];
        }
        
        if ($solicitacao->getStatus() !== 'pendente') {
            return ['sucesso' => false, 'mensagem' => 'Não é possível cancelar uma solicitação já respondida.'];
        }
        
        if ($this->solicitacaoTrocaDAO->atualizarStatus($solicitacaoId, 'cancelada')) {
            return ['sucesso' => true, 'mensagem' => 'Solicitação cancelada com sucesso!'];
        }
        
        return ['sucesso' => false, 'mensagem' => 'Erro ao cancelar solicitação.'];
    }
}