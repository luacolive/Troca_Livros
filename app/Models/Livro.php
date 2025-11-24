<?php
namespace App\Models;

class Livro
{
    private ?int $id;
    private string $titulo;
    private string $autor;
    private string $genero;
    private string $sinopse;
    private string $condicao;
    private bool $disponivel;
    private int $usuarioId;
    private ?string $criadoEm;

    public function __construct(
        string $titulo,
        string $autor,
        string $genero,
        string $sinopse,
        string $condicao,
        bool $disponivel,
        int $usuarioId,
        ?int $id = null,
        ?string $criadoEm = null
    ) {
        $this->titulo = $titulo;
        $this->autor = $autor;
        $this->genero = $genero;
        $this->sinopse = $sinopse;
        $this->condicao = $condicao;
        $this->disponivel = $disponivel;
        $this->usuarioId = $usuarioId;
        $this->id = $id;
        $this->criadoEm = $criadoEm;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getTitulo(): string { return $this->titulo; }
    public function getAutor(): string { return $this->autor; }
    public function getGenero(): string { return $this->genero; }
    public function getSinopse(): string { return $this->sinopse; }
    public function getCondicao(): string { return $this->condicao; }
    public function isDisponivel(): bool { return $this->disponivel; }
    public function getUsuarioId(): int { return $this->usuarioId; }
    public function getCriadoEm(): ?string { return $this->criadoEm; }

    // Setters
    public function setTitulo(string $titulo): void { $this->titulo = $titulo; }
    public function setAutor(string $autor): void { $this->autor = $autor; }
    public function setGenero(string $genero): void { $this->genero = $genero; }
    public function setSinopse(string $sinopse): void { $this->sinopse = $sinopse; }
    public function setCondicao(string $condicao): void { $this->condicao = $condicao; }
    public function setDisponivel(bool $disponivel): void { $this->disponivel = $disponivel; }
}