<?php
// CONEXÃO COM O BANCO DE DADOS
$dbFile = __DIR__.'/tasks.sqlite';
$pdo = new PDO('sqlite:'.$dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// CRIAÇÃO DA TABELA
$pdo->exec("CREATE TABLE IF NOT EXISTS tasks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    description TEXT,
    due_date TEXT NOT NULL,
    responsible TEXT NOT NULL,
    done INTEGER DEFAULT 0
)");

$error = '';

// CRIAR NOVA TAREFA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title']))
{
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];
    $responsible = trim($_POST['responsible']);

    if (empty($title)) {
        $error = "O título é obrigatório!";
    } elseif (empty($due_date)) {
        $error = "A data de vencimento é obrigatória!";
    } elseif (empty($responsible)) {
        $error = "O responsável é obrigatório!";
    } else {
        $stmt = $pdo->prepare("
        INSERT INTO tasks (title, description, due_date, responsible)
        VALUES (:title, :description, :due_date, :responsible)
        ");

        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':due_date' => $due_date,
            ':responsible' => $responsible
        ]);

        header("Location: index.php");
        exit;
    }
}

// AÇÕES
if (isset($_GET['action']) && isset($_GET['id']))
{
    $id = (int) $_GET['id'];

    if($_GET['action'] === 'complete') {
        $pdo->exec("UPDATE tasks SET done = 1 WHERE id = $id");
    } elseif ($_GET['action'] === 'delete') {
        $pdo->exec("DELETE FROM tasks WHERE id = $id");

        header("Location: index.php");
        exit;
    }
}

// LISTAGEM
$stmt = $pdo->query("SELECT * FROM tasks ORDER BY id DESC");
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title>Task Master - Expanded</title>
        <style>
        body { font-family: Arial; background: #f3f4f6; display: flex; justify-content: center; padding-top: 40px; }
        .container { background: white; padding: 20px; border-radius: 8px; width: 500px; }
        input, textarea { width: 100%; padding: 8px; margin-top: 5px; margin-bottom: 10px; }
        button { padding: 10px; background: blue; color: white; border: none; }
        li { border-bottom: 1px solid #ccc; padding: 10px 0; }
        .done { text-decoration: line-through; color: gray; }
        </style>
    </head>
    <body>

        <div class="container">
        <h1>Task Master</h1>

        <?php if ($error): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="title" placeholder="Título">

            <textarea name="description" placeholder="Descrição (opcional)"></textarea>

            <label>Data de vencimento:</label>
            <input type="date" name="due_date">

            <input type="text" name="responsible" placeholder="Responsável">

            <button type="submit">Adicionar</button>
        </form>

        <ul>
            <?php foreach($tasks as $task): ?>
                <li>
                    <strong class="<?php echo $task['done'] ? 'done' : ''; ?>">
                        <?php echo htmlspecialchars($task['title']); ?>
                    </strong>

                    <p><?php echo htmlspecialchars($task['description']); ?></p>

                    <small>
                    📅 <?php echo $task['due_date']; ?> |
                    👤 <?php echo htmlspecialchars($task['responsible']); ?>
                    </small>

                    <div>
                        <?php if (!$task['done']): ?>
                            <a href="?action=complete&id=<?php echo $task['id']; ?>">✅</a>
                        <?php endif; ?>
                        <a href="?action=delete&id=<?php echo $task['id']; ?>">❌</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        </div>

    </body>
</html>
