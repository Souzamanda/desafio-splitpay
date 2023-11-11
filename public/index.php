<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/db.php';


$app = AppFactory::create();

// Add Slim routing middleware
$app->addRoutingMiddleware();

// Configura o caminho base(base path) para rodar a aplicacao em um subdiretorio.
$app->setBasePath("/desafio/public");

$app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response) {
  $response->getBody()->write("Main page");
  return $response;
});

// Rotas de Produtos

// Listar todos os produtos
$app->get('/produtos', function (Request $request, Response $response) {
  $sql = "SELECT * FROM produtos";

  try {
    $db = new DB();
    $conn = $db->connect();

    $stmt = $conn->query($sql);
    $produtos = $stmt->fetchAll(PDO::FETCH_OBJ);

    $db = null;
    $response->getBody()->write(json_encode($produtos));
    return $response->withHeader('content-type', 'application/json')->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );

    $response->getBody()->write(json_encode($error));
    return $response->withHeader('content-type', 'application/json')->withStatus(500);
  }
});

// Listar produto por ID
$app->get('/produtos/{id}', function (Request $request, Response $response, array $args) {
  $id = $args['id'];
  $sql = "SELECT * FROM produtos WHERE id = $id";

  try {
    $db = new DB();
    $conn = $db->connect();

    $stmt = $conn->query($sql);
    $produto = $stmt->fetch(PDO::FETCH_OBJ);

    $db = null;
    $response->getBody()->write(json_encode($produto));
    return $response->withHeader('content-type', 'application/json')->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );

    $response->getBody()->write(json_encode($error));
    return $response->withHeader('content-type', 'application/json')->withStatus(500);
  }
});

// Adicionar um novo produto
$app->post('/produtos/add', function (Request $request, Response $response, array $args) {
  $nome = $request->getParam('nome');
  $descricao = $request->getParam('descricao');
  $preco = $request->getParam('preco');
  $quantidade = $request->getParam('quantidade');
  
  $sql = "INSERT INTO produtos (nome, descricao, preco, quantidade) VALUE (:nome, :descricao, :preco, :quantidade)";

  try {
    $db = new DB();
    $conn = $db->connect();

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':preco', $preco);
    $stmt->bindParam(':quantidade', $quantidade);

    $result = $stmt->execute();

    $db = null;
    $response->getBody()->write(json_encode($result));
    return $response->withHeader('content-type', 'application/json')->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );

    $response->getBody()->write(json_encode($error));
    return $response->withHeader('content-type', 'application/json')->withStatus(500);
  }
});

// Atualizar os dados de um produto existente pelo ID
$app->put('/produtos/update/{id}', function (Request $request, Response $response, array $args) {
  $id = $args['id'];

  $nome = $request->getParam('nome');
  $descricao = $request->getParam('descricao');
  $preco = $request->getParam('preco');
  $quantidade = $request->getParam('quantidade');
  
  $sql = "UPDATE produtos SET nome=:nome, descricao=:descricao, preco=:preco, quantidade=:quantidade WHERE id=$id";

  try {
    $db = new DB();
    $conn = $db->connect();

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':preco', $preco);
    $stmt->bindParam(':quantidade', $quantidade);

    $result = $stmt->execute();

    $db = null;
    $response->getBody()->write(json_encode($result));
    return $response->withHeader('content-type', 'application/json')->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );

    $response->getBody()->write(json_encode($error));
    return $response->withHeader('content-type', 'application/json')->withStatus(500);
  }
});

// Remover um produto pelo ID
$app->delete('/produtos/delete/{id}', function (Request $request, Response $response, array $args) {
  $id = $args['id'];
  $sql = "DELETE FROM produtos WHERE id = $id";

  try {
    $db = new DB();
    $conn = $db->connect();

    $stmt = $conn->prepare($sql);
    $result = $stmt->execute();

    $db = null;
    $response->getBody()->write(json_encode($result));
    return $response->withHeader('content-type', 'application/json')->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );

    $response->getBody()->write(json_encode($error));
    return $response->withHeader('content-type', 'application/json')->withStatus(500);
  }
});

$app->run();