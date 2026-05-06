<?php
// ==========================================
// AULA 01: O CÓDIGO SPAGHETTI 
// ==========================================

// 1. CONEXÃO COM O BANCO DE DADOS
$dbFile = __DIR__ . '/tasks.sqlite';
$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ATUALIZAÇÃO DA TABELA
$pdo->exec("CREATE TABLE IF NOT EXISTS tasks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    description TEXT,
    due_date TEXT NOT NULL,
    responsible TEXT NOT NULL,
    done INTEGER DEFAULT 0
)");

$error = '';

// 2. CRIAR NOVA TAREFA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];
    $responsible = trim($_POST['responsible']);

    if (empty($title)) {
        $error = "O título não pode estar vazio!";
    } elseif (empty($due_date)) {
        $error = "A data de vencimento é obrigatória!";
    } elseif (empty($responsible)) {
        $error = "O responsável é obrigatório!";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO tasks (title, description, due_date, responsible)
            VALUES (:title, :description, :due_date, :responsible)
        ");
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':due_date', $due_date);
        $stmt->bindValue(':responsible', $responsible);
        $stmt->execute();

        header("Location: index.php");
        exit;
    }
}

// CONCLUIR / EXCLUIR
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    if ($_GET['action'] === 'complete') {
        $pdo->exec("UPDATE tasks SET done = 1 WHERE id = $id");
    } elseif ($_GET['action'] === 'delete') {
        $pdo->exec("DELETE FROM tasks WHERE id = $id");
    }

    header("Location: index.php");
    exit;
}

// BUSCAR DADOS
$stmt = $pdo->query("SELECT * FROM tasks ORDER BY id DESC");
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Task Master</title>
<style>
body { font-family: Arial; background: #f3f4f6; display:flex; justify-content:center; padding-top:50px;}
.container { background:#fff; padding:20px; border-radius:8px; width:500px;}
input, textarea { width:100%; padding:8px; margin-top:5px; margin-bottom:10px;}
button { padding:10px; background:#2563eb; color:#fff; border:none;}
li { border-bottom:1px solid #eee; padding:10px;}
.done { text-decoration: line-through; color: gray;}
</style>
</head>

<body>
<div class="container">
<h1>Task Master</h1>

<?php if ($error): ?>
    <div style="color:red;"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST">
    <input type="text" name="title" placeholder="Título da tarefa">

    <textarea name="description" placeholder="Descrição (opcional)"></textarea>

    <label>Data de vencimento:</label>
    <input type="date" name="due_date">

    <input type="text" name="responsible" placeholder="Responsável">

    <button type="submit">Adicionar</button>
</form>

<ul>
<?php foreach ($tasks as $task): ?>
    <li class="<?php echo $task['done'] ? 'done' : ''; ?>">
        <strong><?php echo htmlspecialchars($task['title']); ?></strong><br>

        <?php if (!empty($task['description'])): ?>
            <small><?php echo htmlspecialchars($task['description']); ?></small><br>
        <?php endif; ?>

        <small><?php echo $task['due_date']; ?></small><br>
        <small><?php echo htmlspecialchars($task['responsible']); ?></small><br>

        <?php if (!$task['done']): ?>
            <a href="?action=complete&id=<?php echo $task['id']; ?>">✅</a>
        <?php endif; ?>
        <a href="?action=delete&id=<?php echo $task['id']; ?>">❌</a>
    </li>
<?php endforeach; ?>
</ul>

</div>
</body>
</html>
