<?php
namespace App\Models;

class SolicitacaoTroca
{
    private ?int $id; // ← Adicione "?" para permitir null
    private int $livroOfertadoId;
    private int $livroSolicitadoId;
    private int $usuarioSolicitanteId;
    private int $usuarioProprietarioId;
    private string $status;
    private string $dataSolicitacao;
    private ?string $dataResposta;
    private ?Livro $livroOfertado;
    private ?Livro $livroSolicitado;
    private ?Usuario $usuarioSolicitante;
    private ?Usuario $usuarioProprietario;

    public function __construct(
        int $livroOfertadoId,
        int $livroSolicitadoId,
        int $usuarioSolicitanteId,
        int $usuarioProprietarioId,
        string $status = 'pendente',
        ?int $id = null, // ← Permita null aqui também
        ?string $dataSolicitacao = null,
        ?string $dataResposta = null
    ) {
        $this->id = $id; // ← Agora pode receber null
        $this->livroOfertadoId = $livroOfertadoId;
        $this->livroSolicitadoId = $livroSolicitadoId;
        $this->usuarioSolicitanteId = $usuarioSolicitanteId;
        $this->usuarioProprietarioId = $usuarioProprietarioId;
        $this->status = $status;
        $this->dataSolicitacao = $dataSolicitacao ?? date('Y-m-d H:i:s');
        $this->dataResposta = $dataResposta;
    }

    // Atualize os getters para permitir null no ID
    public function getId(): ?int { return $this->id; }
    public function getLivroOfertadoId(): int { return $this->livroOfertadoId; }
    public function getLivroSolicitadoId(): int { return $this->livroSolicitadoId; }
    public function getUsuarioSolicitanteId(): int { return $this->usuarioSolicitanteId; }
    public function getUsuarioProprietarioId(): int { return $this->usuarioProprietarioId; }
    public function getStatus(): string { return $this->status; }
    public function getDataSolicitacao(): string { return $this->dataSolicitacao; }
    public function getDataResposta(): ?string { return $this->dataResposta; }
    public function getLivroOfertado(): ?Livro { return $this->livroOfertado; }
    public function getLivroSolicitado(): ?Livro { return $this->livroSolicitado; }
    public function getUsuarioSolicitante(): ?Usuario { return $this->usuarioSolicitante; }
    public function getUsuarioProprietario(): ?Usuario { return $this->usuarioProprietario; }

    // Setters (mantenha os mesmos)
    public function setLivroOfertado(Livro $livro): void { $this->livroOfertado = $livro; }
    public function setLivroSolicitado(Livro $livro): void { $this->livroSolicitado = $livro; }
    public function setUsuarioSolicitante(Usuario $usuario): void { $this->usuarioSolicitante = $usuario; }
    public function setUsuarioProprietario(Usuario $usuario): void { $this->usuarioProprietario = $usuario; }
    public function setStatus(string $status): void { $this->status = $status; }
    public function setDataResposta(string $dataResposta): void { $this->dataResposta = $dataResposta; }
}