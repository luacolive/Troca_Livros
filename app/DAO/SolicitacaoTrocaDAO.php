<?php
namespace app\DAO;

use app\Models\SolicitacaoTroca;
use PDO;

class SolicitacaoTrocaDAO
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function inserir(SolicitacaoTroca $solicitacao): bool
    {
        $sql = "INSERT INTO solicitacoes_troca 
                (livro_ofertado_id, livro_solicitado_id, usuario_solicitante_id, usuario_proprietario_id, status) 
                VALUES (:livro_ofertado_id, :livro_solicitado_id, :usuario_solicitante_id, :usuario_proprietario_id, :status)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':livro_ofertado_id' => $solicitacao->getLivroOfertadoId(),
            ':livro_solicitado_id' => $solicitacao->getLivroSolicitadoId(),
            ':usuario_solicitante_id' => $solicitacao->getUsuarioSolicitanteId(),
            ':usuario_proprietario_id' => $solicitacao->getUsuarioProprietarioId(),
            ':status' => $solicitacao->getStatus()
        ]);
    }

    public function buscarPorId(int $id): ?SolicitacaoTroca
    {
        $sql = "SELECT * FROM solicitacoes_troca WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if (!$row) return null;

        return new SolicitacaoTroca(
            $row['livro_ofertado_id'],
            $row['livro_solicitado_id'],
            $row['usuario_solicitante_id'],
            $row['usuario_proprietario_id'],
            $row['status'],
            $row['id'],
            $row['data_solicitacao'],
            $row['data_resposta']
        );
    }

    public function buscarPorUsuario(int $usuarioId): array
    {
        $sql = "SELECT * FROM solicitacoes_troca 
                WHERE usuario_solicitante_id = :usuario_solicitante_id 
                OR usuario_proprietario_id = :usuario_proprietario_id 
                ORDER BY data_solicitacao DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':usuario_solicitante_id' => $usuarioId,
            ':usuario_proprietario_id' => $usuarioId
        ]);
        
        $solicitacoes = [];
        while ($row = $stmt->fetch()) {
            $solicitacoes[] = new SolicitacaoTroca(
                $row['livro_ofertado_id'],
                $row['livro_solicitado_id'],
                $row['usuario_solicitante_id'],
                $row['usuario_proprietario_id'],
                $row['status'],
                $row['id'],
                $row['data_solicitacao'],
                $row['data_resposta']
            );
        }
        
        return $solicitacoes;
    }

    public function buscarPorProprietario(int $usuarioProprietarioId): array
    {
        $sql = "SELECT * FROM solicitacoes_troca 
                WHERE usuario_proprietario_id = :usuario_proprietario_id 
                ORDER BY data_solicitacao DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':usuario_proprietario_id' => $usuarioProprietarioId]);
        
        $solicitacoes = [];
        while ($row = $stmt->fetch()) {
            $solicitacoes[] = new SolicitacaoTroca(
                $row['livro_ofertado_id'],
                $row['livro_solicitado_id'],
                $row['usuario_solicitante_id'],
                $row['usuario_proprietario_id'],
                $row['status'],
                $row['id'],
                $row['data_solicitacao'],
                $row['data_resposta']
            );
        }
        
        return $solicitacoes;
    }

    public function atualizarStatus(int $id, string $status): bool
    {
        $sql = "UPDATE solicitacoes_troca SET status = :status, data_resposta = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':id' => $id
        ]);
    }

    public function verificarSolicitacaoExistente(int $livroSolicitadoId, int $usuarioSolicitanteId): bool
    {
        $sql = "SELECT COUNT(*) FROM solicitacoes_troca 
                WHERE livro_solicitado_id = :livro_solicitado_id 
                AND usuario_solicitante_id = :usuario_solicitante_id 
                AND status = 'pendente'";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':livro_solicitado_id' => $livroSolicitadoId,
            ':usuario_solicitante_id' => $usuarioSolicitanteId
        ]);
        
        return $stmt->fetchColumn() > 0;
    }
}