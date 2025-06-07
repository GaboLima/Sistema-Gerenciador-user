<?php
session_start();
require 'conexao.php';

// CRIAR USUÁRIO
if (isset($_POST['criar_usuario'])) {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $data_nascimento = $_POST['data_nascimento'];
    $status = $_POST['status'] ?? 'ativo';

    if (empty($nome) || empty($email) || empty($senha) || empty($data_nascimento)) {
        $_SESSION['mensagem'] = "Preencha todos os campos obrigatórios.";
        $_SESSION['tipo'] = "danger";
        header('Location: usuario-create.php');
        exit;
    }

    // Gera hash seguro da senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $conexao->prepare("INSERT INTO usuarios (nome, email, senha, data_nascimento, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nome, $email, $senha_hash, $data_nascimento, $status);

    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Usuário cadastrado com sucesso!";
        $_SESSION['tipo'] = "success";
        header('Location: index.php');
    } else {
        $_SESSION['mensagem'] = "Erro ao cadastrar usuário! Verifique se o e-mail já existe.";
        $_SESSION['tipo'] = "danger";
        header('Location: usuario-create.php');
    }
    exit;
}

// EDITAR USUÁRIO
if (isset($_POST['editar_usuario'])) {
    $id = intval($_POST['id']);
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $data_nascimento = $_POST['data_nascimento'];
    $status = $_POST['status'] ?? 'ativo';
    $senha = $_POST['senha'] ?? '';

    if (empty($id) || empty($nome) || empty($email) || empty($data_nascimento)) {
        $_SESSION['mensagem'] = "Preencha todos os campos obrigatórios.";
        $_SESSION['tipo'] = "danger";
        header('Location: usuario-edit.php?id=' . $id);
        exit;
    }

    if (!empty($senha)) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $conexao->prepare("UPDATE usuarios SET nome=?, email=?, senha=?, data_nascimento=?, status=? WHERE id=?");
        $stmt->bind_param("sssssi", $nome, $email, $senha_hash, $data_nascimento, $status, $id);
    } else {
        $stmt = $conexao->prepare("UPDATE usuarios SET nome=?, email=?, data_nascimento=?, status=? WHERE id=?");
        $stmt->bind_param("ssssi", $nome, $email, $data_nascimento, $status, $id);
    }

    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Usuário atualizado com sucesso!";
        $_SESSION['tipo'] = "success";
        header('Location: usuario-view.php?id=' . $id);
    } else {
        $_SESSION['mensagem'] = "Erro ao atualizar usuário!";
        $_SESSION['tipo'] = "danger";
        header('Location: usuario-edit.php?id=' . $id);
    }
    exit;
}

// EXCLUIR USUÁRIO
if (isset($_POST['delete_usuario']) || (isset($_POST['acao']) && $_POST['acao'] === 'excluir')) {
    $id = isset($_POST['delete_usuario']) ? intval($_POST['delete_usuario']) : intval($_POST['id']);

    $stmt = $conexao->prepare("DELETE FROM usuarios WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Usuário excluído com sucesso!";
        $_SESSION['tipo'] = "success";
        header('Location: index.php');
    } else {
        $_SESSION['mensagem'] = "Erro ao excluir usuário!";
        $_SESSION['tipo'] = "danger";
        header('Location: usuario-view.php?id=' . $id);
    }
    exit;
}

// ALTERAR STATUS ATIVO/INATIVO
if (isset($_POST['toggle_status']) || (isset($_POST['acao']) && $_POST['acao'] === 'toggle_status')) {
    $id = isset($_POST['toggle_status']) ? intval($_POST['toggle_status']) : intval($_POST['id']);

    // Pega status atual
    $result = $conexao->query("SELECT status FROM usuarios WHERE id=$id");
    if ($row = $result->fetch_assoc()) {
        $novoStatus = ($row['status'] === 'ativo') ? 'inativo' : 'ativo';

        $stmt = $conexao->prepare("UPDATE usuarios SET status=? WHERE id=?");
        $stmt->bind_param("si", $novoStatus, $id);

        if ($stmt->execute()) {
            $_SESSION['mensagem'] = "Status atualizado para <strong>" . ucfirst($novoStatus) . "</strong>!";
            $_SESSION['tipo'] = "success";
        } else {
            $_SESSION['mensagem'] = "Erro ao alterar status!";
            $_SESSION['tipo'] = "danger";
        }
    } else {
        $_SESSION['mensagem'] = "Usuário não encontrado!";
        $_SESSION['tipo'] = "danger";
    }
    header('Location: usuario-view.php?id=' . $id);
    exit;
}

// LOGIN
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    $stmt = $conexao->prepare("SELECT * FROM usuarios WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();

    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_nome'] = $user['nome'];
        $_SESSION['usuario_email'] = $user['email'];
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['mensagem'] = "E-mail ou senha inválidos.";
        $_SESSION['tipo'] = "danger";
        header('Location: login.php');
        exit;
    }
}

// Caso acessem diretamente
header('Location: index.php');
exit;
