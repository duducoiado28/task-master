<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . './../src/Model/Task.php';

class TaskModelTest exteds TestCase
{
    public function tesNaoPodeSalvarTarefaSemTitulo()
    {
        $pdoMock = $this->createMock(PDO::class); // Fingimos que o banco existe
        $model = new Task($pdoMock);

        $this->exceptException(Exception::class);
        $this->exceptExceptionMessage("Título e Data são obrigatórios.");

        $model->save("", "Fazer compras", "2026-12-31"); // Deve disparar exceção
    }
}
?>
