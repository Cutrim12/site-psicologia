<?php

class PacienteValidationException extends InvalidArgumentException
{
    public $campo;
    public $erros;

    public function __construct($erros)
    {
        $this->erros = $erros;
        parent::__construct('Revise os campos destacados.');
    }
}

function erroCampoPaciente($erros, $campo)
{
    if (empty($erros[$campo])) {
        return '';
    }

    return '<span class="help-block field-error">' . htmlspecialchars($erros[$campo], ENT_QUOTES, 'UTF-8') . '</span>';
}

function valorCampoPaciente($campo)
{
    return htmlspecialchars((string) ($_POST[$campo] ?? ''), ENT_QUOTES, 'UTF-8');
}

function opcaoSelecionadaPaciente($campo, $valor)
{
    return isset($_POST[$campo]) && (string) $_POST[$campo] === (string) $valor ? ' selected' : '';
}

function validarCadastroPaciente(array &$dados): void
{
    $erros = [];
    $obrigatorios = [
        'nome', 'sexo', 'nascimento', 'escolaridade', 'profissao',
        'renda_familiar', 'rg', 'cpf', 'estado_civil', 'bairro', 'cep',
        'telefone_residencial', 'telefone_recado', 'celular', 'email'
    ];

    foreach ($obrigatorios as $campo) {
        if (!isset($dados[$campo]) || trim((string) $dados[$campo]) === '') {
            $erros[$campo] = 'Preencha este campo.';
        }
    }

    $dados['nome'] = trim((string) ($dados['nome'] ?? ''));
    $dados['rg'] = trim((string) ($dados['rg'] ?? ''));
    $dados['cpf'] = preg_replace('/\D/', '', (string) ($dados['cpf'] ?? ''));

    if (isset($dados['nome']) && strlen($dados['nome']) > 150) {
        $erros['nome'] = 'O nome deve ter no máximo 150 caracteres.';
    }
    if (isset($dados['rg']) && strlen($dados['rg']) > 20) {
        $erros['rg'] = 'O RG deve ter no máximo 20 caracteres.';
    }
    if (!empty($dados['cpf']) && !preg_match('/^\d{11}$/', $dados['cpf'])) {
        $erros['cpf'] = 'Informe um CPF com 11 dígitos.';
    }
    if (!empty($dados['email']) && !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        $erros['email'] = 'Informe um e-mail válido.';
    }

    $data = !empty($dados['nascimento']) ? DateTime::createFromFormat('Y-m-d', (string) $dados['nascimento']) : false;
    if (!empty($dados['nascimento']) && (!$data || $data->format('Y-m-d') !== $dados['nascimento'] || $data > new DateTime('today'))) {
        $erros['nascimento'] = 'Informe uma data de nascimento válida.';
    }

    foreach (['sexo', 'escolaridade', 'renda_familiar', 'estado_civil'] as $campo) {
        if (isset($dados[$campo]) && trim((string) $dados[$campo]) !== '' && filter_var($dados[$campo], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
            $erros[$campo] = 'Selecione uma opção válida.';
        } elseif (isset($dados[$campo]) && trim((string) $dados[$campo]) !== '') {
            $dados[$campo] = (int) $dados[$campo];
        }
    }

    if (!empty($erros)) {
        throw new PacienteValidationException($erros);
    }
}
?>
