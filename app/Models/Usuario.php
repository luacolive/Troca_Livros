<?php
namespace App\Models;

class Usuario
{
    private ?int $id;
    private string $nome;
    private string $email;
    private string $senhaHash;
    private ?string $criadoEm;
    private ?string $endereco;
    private ?string $telefone;

    public function __construct(
        string $nome,
        string $email,
        string $senhaHash,
        ?string $endereco = null,
        ?string $telefone = null,
        ?int $id = null,
        ?string $criadoEm = null
    ) {
        $this->nome = $nome;
        $this->email = $email;
        $this->senhaHash = $senhaHash;
        $this->endereco = $endereco;
        $this->telefone = $telefone;
        $this->id = $id;
        $this->criadoEm = $criadoEm;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getNome(): string { return $this->nome; }
    public function getEmail(): string { return $this->email; }
    public function getSenhaHash(): string { return $this->senhaHash; }
    public function getCriadoEm(): ?string { return $this->criadoEm; }
    public function getEndereco(): ?string { return $this->endereco; }
    public function getTelefone(): ?string { return $this->telefone; }

    // Setters
    public function setNome(string $nome): void { $this->nome = $nome; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function setSenhaHash(string $senhaHash): void { $this->senhaHash = $senhaHash; }
    public function setEndereco(?string $endereco): void { $this->endereco = $endereco; }
    public function setTelefone(?string $telefone): void { $this->telefone = $telefone; }
}