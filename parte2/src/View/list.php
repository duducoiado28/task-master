<!DOCTYPE html>
<html lang="en">

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
            <h1>Task Master (MVC Edition)</h1>

            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php?action=create" class="form-group">
                <input type="text" name="title" placeholder="Título" required>
                <input type="text" name="description" placeholder="Descrição">
                <input type="date" name="due_date" required>
                <button type="submit">Adicionar</button>
            </form>

            <ul>
                <?php foreach ($tasks as $task): ?>
                    <li class="<?php echo $task["done"] ? "done" : ""; ?>">
                        <div>
                            <strong><?php echo htmlspecialchars(
                                $task["title"],
                            ); ?></strong><br>
                            <small><?php echo htmlspecialchars(
                                $task["description"],
                            ); ?> | Vence em: <?php echo $task["due_date"]; ?></small>
                        </div>
                        <div class='action'>
                            <?php if (!$task["done"]): ?>
                                <a href="index.php?action=complete&id=<?php echo $task[
                                    "id"
                                ]; ?>">✅</a>
                            <?php endif; ?>
                            <a href="index.php?action=delete&id=<?php echo $task[
                                "id"
                            ]; ?>">❌</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </body>
</html>
